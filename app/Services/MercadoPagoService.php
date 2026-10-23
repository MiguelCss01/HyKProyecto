<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Pedido;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    /**
     * Crea una preferencia de pago en la API oficial de Mercado Pago (Checkout Pro).
     * Retorna la respuesta oficial con los links de redirección (init_point y sandbox_init_point).
     *
     * @throws Exception
     */
    public function crearPreferencia(Pedido $pedido): array
    {
        $accessToken = config('services.mercadopago.access_token');

        if (empty($accessToken)) {
            throw new Exception('Falta configurar el MERCADOPAGO_ACCESS_TOKEN en el archivo .env para conectar con la API de Mercado Pago.');
        }

        $pedido->loadMissing(['detalles.presentacionProducto.producto', 'user']);

        $items = [];
        foreach ($pedido->detalles as $detalle) {
            $nombre = $detalle->presentacionProducto?->producto?->nombre_producto ?? 'Producto HyK';
            $tipo = $detalle->presentacionProducto?->tipo ?? 'unidad';

            $items[] = [
                'id' => (string) $detalle->id,
                'title' => "{$nombre} ({$tipo})",
                'description' => "Compra en HyK Mayorista - Pedido #{$pedido->id}",
                'quantity' => (int) $detalle->cantidad,
                'currency_id' => 'ARS',
                'unit_price' => (float) $detalle->precio_unitario,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'id' => (string) $pedido->id,
                'title' => "Pedido #{$pedido->id} - HyK Mayorista",
                'quantity' => 1,
                'currency_id' => 'ARS',
                'unit_price' => (float) $pedido->total,
            ];
        }

        $user = $pedido->user;
        $payer = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        if (! empty($user->telefono)) {
            $payer['phone'] = [
                'number' => $user->telefono,
            ];
        }

        $backUrl = $this->resolverBackUrl();

        $payload = [
            'items' => $items,
            'payer' => $payer,
            'back_urls' => [
                'success' => $backUrl,
                'failure' => $backUrl,
                'pending' => $backUrl,
            ],
            'external_reference' => (string) $pedido->id,
            'statement_descriptor' => 'HYK MAYORISTA',
        ];

        // Mercado Pago exige HTTPS para auto_return. Si la URL es HTTPS, lo activamos.
        if (str_starts_with($backUrl, 'https://')) {
            $payload['auto_return'] = 'approved';
        }

        $httpClient = Http::withToken($accessToken)->acceptJson();

        if (app()->environment('local', 'testing')) {
            $httpClient = $httpClient->withoutVerifying();
        }

        $response = $httpClient->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if ($response->failed()) {
            Log::error('Error al crear preferencia en Mercado Pago', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            $errorMsg = $response->json('message') ?? 'Error al comunicarse con la API de Mercado Pago.';
            throw new Exception("Mercado Pago API: {$errorMsg}");
        }

        return $response->json();
    }

    /**
     * Resuelve la URL de retorno (back_url) garantizando HTTPS en desarrollo local para que Mercado Pago habilite auto_return.
     */
    public function resolverBackUrl(): string
    {
        $backUrl = config('services.mercadopago.back_url');

        if (! empty($backUrl)) {
            return (string) $backUrl;
        }

        $appUrl = (string) config('app.url');

        // En entorno local (cuando se sirve por http://localhost:8000),
        // Mercado Pago exige obligatoriamente HTTPS para almacenar back_urls y procesar auto_return.
        // Detectamos automáticamente si Apache en XAMPP responde por SSL en el puerto 4433 o 443.
        if (str_starts_with($appUrl, 'http://localhost') || str_starts_with($appUrl, 'http://127.0.0.1')) {
            if (! app()->runningUnitTests() && $this->puertoLocalActivo(4433) && ! $this->puertoLocalActivo(443)) {
                return 'https://localhost:4433/HyKProyecto/public/pedidos/retorno-mercadopago';
            }

            return 'https://localhost/HyKProyecto/public/pedidos/retorno-mercadopago';
        }

        return route('pedidos.retorno-mp');
    }

    /**
     * Verifica si un puerto TCP local se encuentra escuchando conexiones.
     */
    protected function puertoLocalActivo(int $port): bool
    {
        $connection = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.15);

        if (is_resource($connection)) {
            fclose($connection);

            return true;
        }

        return false;
    }

    /**
     * Consulta en la API de Mercado Pago si ya existe un pago aprobado para el pedido
     * y, en caso afirmativo, lo acredita automáticamente en la base de datos.
     */
    public function verificarPagoPorReferencia(Pedido $pedido): ?Pago
    {
        if ($pedido->estado === 'PAGADO' || $pedido->pago !== null) {
            return $pedido->pago;
        }

        $accessToken = config('services.mercadopago.access_token');

        if (empty($accessToken) || app()->runningUnitTests()) {
            return null;
        }

        try {
            $httpClient = Http::withToken($accessToken)->acceptJson()->timeout(4);

            if (app()->environment('local', 'testing')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get('https://api.mercadopago.com/v1/payments/search', [
                'external_reference' => (string) $pedido->id,
                'sort' => 'date_created',
                'criteria' => 'desc',
            ]);

            if ($response->successful()) {
                $results = $response->json('results', []);

                foreach ($results as $payment) {
                    if (($payment['status'] ?? '') === 'approved') {
                        return $this->registrarPagoAprobado(
                            $pedido,
                            (string) $payment['id'],
                            isset($payment['transaction_amount']) ? (float) $payment['transaction_amount'] : (float) $pedido->total
                        );
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning('No se pudo verificar estado de pago en Mercado Pago', [
                'pedido_id' => $pedido->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Registra un pago aprobado de Mercado Pago para el pedido dado.
     */
    public function registrarPagoAprobado(Pedido $pedido, string $paymentId, ?float $monto = null): Pago
    {
        return DB::transaction(function () use ($pedido, $paymentId, $monto) {
            $pedidoFresh = Pedido::lockForUpdate()->findOrFail($pedido->id);

            $pago = Pago::updateOrCreate(
                ['pedido_id' => $pedidoFresh->id],
                [
                    'monto' => $monto ?? $pedidoFresh->total,
                    'id_transaccion_mp' => $paymentId,
                    'fecha_pago' => now()->toDateString(),
                ]
            );

            $pedidoFresh->update([
                'estado' => 'PAGADO',
            ]);

            return $pago;
        });
    }

    /**
     * Procesa y registra de forma ficticia un pago para un pedido a través de Mercado Pago.
     *
     * @throws Exception
     */
    public function procesarPagoFicticio(Pedido $pedido, array $datosPago = []): Pago
    {
        return DB::transaction(function () use ($pedido, $datosPago) {
            $pedidoFresh = Pedido::lockForUpdate()->findOrFail($pedido->id);

            if ($pedidoFresh->estado === 'PAGADO') {
                return $pedidoFresh->pago ?? Pago::where('pedido_id', $pedidoFresh->id)->firstOrFail();
            }

            $idTransaccion = 'MP-'.mt_rand(1000000000, 9999999999);
            if (! empty($datosPago['id_transaccion_mp'])) {
                $idTransaccion = (string) $datosPago['id_transaccion_mp'];
            }

            $pago = Pago::create([
                'pedido_id' => $pedidoFresh->id,
                'monto' => $pedidoFresh->total,
                'id_transaccion_mp' => $idTransaccion,
                'fecha_pago' => now()->toDateString(),
            ]);

            $pedidoFresh->update([
                'estado' => 'PAGADO',
            ]);

            return $pago;
        });
    }

    /**
     * Obtiene las credenciales del entorno de pruebas (Sandbox / Ficticio).
     *
     * @return array<string, string>
     */
    public function obtenerCredencialesTest(): array
    {
        $credencialesFile = base_path('MercadoPago/Credenciales.txt');
        $usuarioVendedor = 'TESTUSER2984400822875817517';
        $claveVendedor = 'Zkh4RXjnN5';
        $compradorNombre = 'Comprador Prueba Huertas';
        $compradorUsuario = 'TESTUSER6236431954893547037';
        $claveComprador = 'okgbUMLSGQ';

        if (File::exists($credencialesFile)) {
            $content = File::get($credencialesFile);

            if (preg_match('/Usuario de prueba:\s*([^\r\n]+)/i', $content, $m)) {
                $usuarioVendedor = trim($m[1]);
            }
            if (preg_match('/Contraseña:\s*([^\r\n]+)/i', $content, $m)) {
                $claveVendedor = trim($m[1]);
            }
            if (preg_match('/Comprador Prueba Huertas:\s*([^\r\n]+)/i', $content, $m)) {
                $compradorUsuario = trim($m[1]);
            }
            if (preg_match_all('/Contraseña:\s*([^\r\n]+)/i', $content, $allMatches)) {
                if (isset($allMatches[1][1])) {
                    $claveComprador = trim($allMatches[1][1]);
                }
            }
        }

        return [
            'vendedor_usuario' => $usuarioVendedor,
            'vendedor_clave' => $claveVendedor,
            'comprador_nombre' => $compradorNombre,
            'comprador_usuario' => $compradorUsuario,
            'comprador_clave' => $claveComprador,
        ];
    }
}
