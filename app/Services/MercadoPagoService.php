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

        $backUrl = config('services.mercadopago.back_url');

        if (empty($backUrl)) {
            $appUrl = config('app.url');
            // En entorno local XAMPP (cuando se sirve por http://localhost:8000),
            // Mercado Pago exige obligatoriamente HTTPS para almacenar back_urls y procesar auto_return.
            // Apache en XAMPP responde por SSL en https://localhost/HyKProyecto/public.
            if (str_starts_with($appUrl, 'http://localhost') || str_starts_with($appUrl, 'http://127.0.0.1')) {
                $backUrl = 'https://localhost/HyKProyecto/public/pedidos/retorno-mercadopago';
            } else {
                $backUrl = route('pedidos.retorno-mp');
            }
        }

        $payload = [
            'items' => $items,
            'payer' => $payer,
            'back_urls' => [
                'success' => $backUrl,
                'failure' => $backUrl,
                'pending' => $backUrl,
            ],
            'external_reference' => (string) $pedido->id,
            'statement_descriptor' => 'HyK Mayorista',
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
