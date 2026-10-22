<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\CartService;
use App\Services\MercadoPagoService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected MercadoPagoService $mercadoPagoService
    ) {}

    /**
     * Pantalla de checkout / confirmación de pedido (Requiere autenticación).
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = $this->cartService->obtenerCarrito();

        if ($cart['total_items'] <= 0) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');
        }

        if (! Auth::check()) {
            return redirect()->guest(route('login'))
                ->with('info', 'Debes iniciar sesión para confirmar tu pedido.');
        }

        $user = Auth::user();

        return view('pedidos.confirmar', compact('cart', 'user'));
    }

    /**
     * Procesa la confirmación del pedido, reserva stock, vacía el carrito
     * y redirige al flujo de pago de Mercado Pago.
     */
    public function confirmar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $pedido = DB::transaction(function () use ($user) {
                $cart = $this->cartService->obtenerCarrito();

                if ($cart['total_items'] <= 0) {
                    throw new Exception('El carrito de compras está vacío.');
                }

                // Validar y bloquear stock para evitar condiciones de carrera
                foreach ($cart['items'] as $item) {
                    $presentacion = $item['presentacion'];
                    $unidadesFisicas = $item['cantidad'] * $presentacion->cantidad_contenida;

                    $producto = Producto::lockForUpdate()->find($presentacion->producto_id);

                    if (! $producto || ! $producto->activo) {
                        throw new Exception("El producto '{$item['producto']->nombre_producto}' ya no está disponible.");
                    }

                    if ($producto->stock_actual < $unidadesFisicas) {
                        $maxDisponibles = $presentacion->cantidad_contenida > 0
                            ? intdiv($producto->stock_actual, $presentacion->cantidad_contenida)
                            : 0;

                        throw new Exception(
                            "Stock insuficiente para '{$producto->nombre_producto}'. Solo quedan {$producto->stock_actual} unidades en total (máximo: {$maxDisponibles} {$presentacion->tipo}(s))."
                        );
                    }
                }

                // Crear el Pedido en estado PENDIENTE
                $pedido = Pedido::create([
                    'fecha_pedido' => now(),
                    'total' => $cart['total'],
                    'estado' => 'PENDIENTE',
                    'user_id' => $user->id,
                ]);

                // Registrar detalles y descontar stock físico
                foreach ($cart['items'] as $item) {
                    $presentacion = $item['presentacion'];
                    $unidadesFisicas = $item['cantidad'] * $presentacion->cantidad_contenida;

                    DetallePedido::create([
                        'pedido_id' => $pedido->id,
                        'presentacion_producto_id' => $item['presentacion_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                    ]);

                    Producto::where('id', $presentacion->producto_id)
                        ->decrement('stock_actual', $unidadesFisicas);
                }

                // Vaciar el carrito de compras
                $this->cartService->vaciar();

                return $pedido;
            });

            // Redirigir a la pantalla de pago de Mercado Pago, donde se muestra el resumen,
            // las credenciales de prueba y el enlace/redirección al Checkout Oficial de Mercado Pago
            return redirect()->route('pedidos.pago', $pedido->id);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra la pantalla de pago y el acceso al Checkout oficial de Mercado Pago.
     */
    public function mostrarPago(int $id): View|RedirectResponse
    {
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user', 'pago'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($pedido->estado === 'PAGADO' || $pedido->pago !== null) {
            return redirect()->route('pedido.exito', $pedido->id)
                ->with('info', 'Este pedido ya cuenta con un pago acreditado.');
        }

        $initPoint = null;
        $errorMp = null;

        // Generar la preferencia en la API oficial de Mercado Pago
        if (! empty(config('services.mercadopago.access_token'))) {
            try {
                $preferencia = $this->mercadoPagoService->crearPreferencia($pedido);
                $initPoint = config('services.mercadopago.sandbox', true)
                    ? ($preferencia['sandbox_init_point'] ?? $preferencia['init_point'])
                    : $preferencia['init_point'];
            } catch (Exception $e) {
                $errorMp = $e->getMessage();
            }
        }

        $credenciales = $this->mercadoPagoService->obtenerCredencialesTest();

        return view('pedidos.mercadopago', compact('pedido', 'credenciales', 'initPoint', 'errorMp'));
    }

    /**
     * Procesa y registra el pago ficticio a través de Mercado Pago.
     */
    public function procesarPago(Request $request, int $id): RedirectResponse
    {
        $pedido = Pedido::where('user_id', Auth::id())->findOrFail($id);

        try {
            $this->mercadoPagoService->procesarPagoFicticio($pedido, [
                'id_transaccion_mp' => $request->input('id_transaccion_mp'),
                'metodo_pago' => $request->input('metodo_pago', 'cuenta_mp'),
            ]);

            return redirect()->route('pedido.exito', $pedido->id)
                ->with('success', '¡Pago procesado con éxito por Mercado Pago!');
        } catch (Exception $e) {
            return back()->with('error', 'Error al procesar el pago: '.$e->getMessage());
        }
    }

    /**
     * Retorno automático desde el verdadero Mercado Pago tras completar o procesar el pago.
     */
    public function retornoMercadoPago(Request $request): RedirectResponse
    {
        $pedidoId = $request->input('external_reference');
        $status = strtolower($request->input('status') ?? $request->input('collection_status') ?? '');
        $paymentId = $request->input('payment_id') ?? $request->input('collection_id');

        $baseUrl = rtrim(config('app.url'), '/');

        if (! $pedidoId) {
            return redirect()->to($baseUrl.'/mis-pedidos')
                ->with('error', 'No se pudo identificar la referencia del pedido devuelto por Mercado Pago.');
        }

        $pedido = Pedido::with('user')->findOrFail($pedidoId);

        // Si el usuario está autenticado, validar que sea el dueño del pedido
        if (Auth::check() && $pedido->user_id !== Auth::id()) {
            abort(403, 'No autorizado para gestionar este pedido.');
        }

        // Si el retorno llega desde Mercado Pago sin sesión en este host/puerto, autenticar con el usuario del pedido
        if (Auth::guest() && $pedido->user) {
            Auth::login($pedido->user);
        }

        if ($status === 'approved') {
            $this->mercadoPagoService->registrarPagoAprobado(
                $pedido,
                (string) ($paymentId ?? 'MP-'.mt_rand(1000000000, 9999999999)),
                (float) $pedido->total
            );

            return redirect()->to($baseUrl.'/pedidos/'.$pedido->id.'/exito')
                ->with('success', '¡Tu pago ha sido acreditado exitosamente por Mercado Pago!');
        }

        if ($status === 'in_process' || $status === 'pending') {
            return redirect()->to($baseUrl.'/mis-pedidos/'.$pedido->id)
                ->with('info', 'Tu pago se encuentra en proceso de validación en Mercado Pago.');
        }

        return redirect()->to($baseUrl.'/mis-pedidos/'.$pedido->id)
            ->with('error', 'El pago no fue completado o fue cancelado en Mercado Pago.');
    }

    /**
     * Muestra el historial de pedidos del cliente autenticado ("Mis Pedidos").
     */
    public function misPedidos(): View
    {
        $pedidos = Pedido::with(['detalles.presentacionProducto.producto', 'pago'])
            ->where('user_id', Auth::id())
            ->latest('id')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Muestra el detalle específico de un pedido perteneciente al cliente autenticado.
     */
    public function show(int $id): View
    {
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user', 'pago'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Muestra la pantalla de éxito y comprobante del pedido confirmado con información de pago y aviso de retiro.
     */
    public function exito(int $id): View
    {
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user', 'pago'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.exito', compact('pedido'));
    }

    /**
     * Muestra el comprobante oficial del pedido en formato imprimible/PDF.
     */
    public function comprobante(int $id): View
    {
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user', 'pago'])
            ->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $pedido->user_id !== Auth::id()) {
            abort(403, 'No autorizado para ver este comprobante.');
        }

        return view('pedidos.comprobante', compact('pedido'));
    }
}
