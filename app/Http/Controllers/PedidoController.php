<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\CartService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function __construct(
        protected CartService $cartService
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
     * Procesa la confirmación definitiva del pedido, descontando stock y vaciando el carrito.
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

                // Crear el Pedido
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

            return redirect()->route('pedido.exito', $pedido->id)
                ->with('success', '¡Tu pedido ha sido confirmado con éxito!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el historial de pedidos del cliente autenticado ("Mis Pedidos").
     */
    public function misPedidos(): View
    {
        $pedidos = Pedido::with(['detalles.presentacionProducto.producto'])
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
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Muestra la pantalla de éxito y comprobante del pedido confirmado.
     */
    public function exito(int $id): View
    {
        $pedido = Pedido::with(['detalles.presentacionProducto.producto.categoria', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.exito', compact('pedido'));
    }
}
