<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\ItemCarrito;
use App\Models\PresentacionProducto;
use App\Models\User;

class CartService
{
    /**
     * Obtiene el contenido completo del carrito con productos,
     * presentaciones, precios aplicables y totales calculados.
     *
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     total: float,
     *     total_items: int,
     *     is_mayorista: bool
     * }
     */
    public function obtenerCarrito(): array
    {
        $rawCart = session()->get('carrito', []);
        $items = [];
        $total = 0.0;
        $totalItems = 0;

        $user = auth()->user();
        $isMayorista = $user?->tipo_cliente === 'mayorista';

        if (! empty($rawCart)) {
            $presentacionesIds = array_keys($rawCart);
            $presentaciones = PresentacionProducto::with('producto.categoria')
                ->whereIn('id', $presentacionesIds)
                ->get()
                ->keyBy('id');

            foreach ($rawCart as $presentacionId => $datos) {
                $presentacion = $presentaciones->get($presentacionId);

                // Si la presentación o el producto fueron eliminados o están inactivos, omitir
                if (! $presentacion || ! $presentacion->producto || ! $presentacion->producto->activo) {
                    continue;
                }

                $cantidad = (int) ($datos['cantidad'] ?? 1);
                $precioUnitario = $isMayorista
                    ? (float) $presentacion->precio_mayorista
                    : (float) $presentacion->precio_minorista;

                $subtotal = $precioUnitario * $cantidad;
                $total += $subtotal;
                $totalItems += $cantidad;

                $stockFisicoProducto = (int) $presentacion->producto->stock_actual;
                $stockMaxPresentacion = $presentacion->cantidad_contenida > 0
                    ? intdiv($stockFisicoProducto, $presentacion->cantidad_contenida)
                    : 0;

                $items[$presentacionId] = [
                    'presentacion_id' => $presentacion->id,
                    'presentacion' => $presentacion,
                    'producto' => $presentacion->producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'stock_max_presentacion' => $stockMaxPresentacion,
                ];
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'total_items' => $totalItems,
            'is_mayorista' => $isMayorista,
        ];
    }

    /**
     * Agrega una presentación de producto al carrito con verificación de stock.
     *
     * @return array{success: bool, message: string}
     */
    public function agregar(int $presentacionId, int $cantidad = 1): array
    {
        if ($cantidad <= 0) {
            return [
                'success' => false,
                'message' => 'La cantidad ingresada debe ser al menos 1 unidad.',
            ];
        }

        $presentacion = PresentacionProducto::with('producto')->find($presentacionId);

        if (! $presentacion || ! $presentacion->producto || ! $presentacion->producto->activo) {
            return [
                'success' => false,
                'message' => 'El producto o presentación seleccionada no está disponible.',
            ];
        }

        $cart = session()->get('carrito', []);
        $cantidadPrevia = isset($cart[$presentacionId]) ? (int) $cart[$presentacionId]['cantidad'] : 0;
        $nuevaCantidadPresentacion = $cantidadPrevia + $cantidad;

        // Validar stock físico acumulado del producto
        $validacionStock = $this->validarStockDisponible($presentacion, $nuevaCantidadPresentacion, $cart);
        if (! $validacionStock['valido']) {
            return [
                'success' => false,
                'message' => $validacionStock['mensaje'],
            ];
        }

        // Guardar en sesión
        $cart[$presentacionId] = [
            'presentacion_id' => $presentacionId,
            'cantidad' => $nuevaCantidadPresentacion,
        ];
        session()->put('carrito', $cart);

        // Si el usuario está autenticado, sincronizar con base de datos
        if (auth()->check()) {
            $this->sincronizarConBD(auth()->user());
        }

        return [
            'success' => true,
            'message' => "¡{$presentacion->producto->nombre_producto} ({$presentacion->tipo}) agregado al carrito!",
        ];
    }

    /**
     * Modifica la cantidad de una presentación en el carrito.
     *
     * @return array{success: bool, message: string}
     */
    public function actualizar(int $presentacionId, int $nuevaCantidad): array
    {
        if ($nuevaCantidad <= 0) {
            return $this->eliminar($presentacionId);
        }

        $cart = session()->get('carrito', []);
        if (! isset($cart[$presentacionId])) {
            return [
                'success' => false,
                'message' => 'El producto no se encuentra en el carrito.',
            ];
        }

        $presentacion = PresentacionProducto::with('producto')->find($presentacionId);
        if (! $presentacion || ! $presentacion->producto || ! $presentacion->producto->activo) {
            return [
                'success' => false,
                'message' => 'El producto solicitado ya no se encuentra disponible.',
            ];
        }

        // Validar stock con la nueva cantidad
        $validacionStock = $this->validarStockDisponible($presentacion, $nuevaCantidad, $cart);
        if (! $validacionStock['valido']) {
            return [
                'success' => false,
                'message' => $validacionStock['mensaje'],
            ];
        }

        $cart[$presentacionId]['cantidad'] = $nuevaCantidad;
        session()->put('carrito', $cart);

        if (auth()->check()) {
            $this->sincronizarConBD(auth()->user());
        }

        return [
            'success' => true,
            'message' => 'Cantidad actualizada correctamente.',
        ];
    }

    /**
     * Elimina un producto/presentación del carrito.
     *
     * @return array{success: bool, message: string}
     */
    public function eliminar(int $presentacionId): array
    {
        $cart = session()->get('carrito', []);

        if (isset($cart[$presentacionId])) {
            unset($cart[$presentacionId]);
            session()->put('carrito', $cart);
        }

        if (auth()->check()) {
            $this->sincronizarConBD(auth()->user());
        }

        return [
            'success' => true,
            'message' => 'Producto quitado del carrito.',
        ];
    }

    /**
     * Vacía todos los ítems del carrito.
     *
     * @return array{success: bool, message: string}
     */
    public function vaciar(): array
    {
        session()->forget('carrito');

        if (auth()->check()) {
            $carrito = Carrito::where('user_id', auth()->id())->first();
            if ($carrito) {
                $carrito->items()->delete();
                $carrito->update(['total' => 0.0]);
            }
        }

        return [
            'success' => true,
            'message' => 'El carrito ha sido vaciado.',
        ];
    }

    /**
     * Retorna la cantidad total de artículos agregados en el carrito.
     */
    public function conteoTotal(): int
    {
        $cart = session()->get('carrito', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += (int) ($item['cantidad'] ?? 0);
        }

        return $total;
    }

    /**
     * Sincroniza el carrito de la sesión con las tablas carritos e item_carritos de la base de datos.
     */
    public function sincronizarConBD(User $user): void
    {
        $cart = session()->get('carrito', []);

        $carrito = Carrito::firstOrCreate(
            ['user_id' => $user->id],
            ['total' => 0.0]
        );

        // Fusionar ítems que el usuario ya tenía en la base de datos con los que añadió como invitado
        $itemsBD = $carrito->items()->get();
        foreach ($itemsBD as $itemBD) {
            if (! isset($cart[$itemBD->presentacion_producto_id])) {
                $cart[$itemBD->presentacion_producto_id] = [
                    'presentacion_id' => $itemBD->presentacion_producto_id,
                    'cantidad' => (int) $itemBD->cantidad,
                ];
            }
        }

        session()->put('carrito', $cart);

        // Persistir el estado consolidado en la base de datos
        $carrito->items()->whereNotIn('presentacion_producto_id', array_keys($cart))->delete();

        foreach ($cart as $presentacionId => $datos) {
            ItemCarrito::updateOrCreate(
                [
                    'carrito_id' => $carrito->id,
                    'presentacion_producto_id' => $presentacionId,
                ],
                [
                    'cantidad' => (int) $datos['cantidad'],
                ]
            );
        }

        $carrito->recalcularTotal();
    }

    /**
     * Valida si existe stock físico suficiente para el producto considerando
     * todas las presentaciones que estén en el carrito para el mismo producto.
     *
     * @param  array<int, array<string, mixed>>  $cartActual
     * @return array{valido: bool, mensaje: string}
     */
    private function validarStockDisponible(PresentacionProducto $presentacion, int $cantidadDeseada, array $cartActual): array
    {
        $producto = $presentacion->producto;
        $stockFisicoTotal = (int) $producto->stock_actual;

        if ($stockFisicoTotal <= 0) {
            return [
                'valido' => false,
                'mensaje' => "El producto '{$producto->nombre_producto}' no cuenta con stock disponible.",
            ];
        }

        // Calcular unidades físicas ya ocupadas por otras presentaciones del mismo producto en el carrito
        $unidadesFisicasOcupadasPorOtras = 0;
        foreach ($cartActual as $presId => $item) {
            if ((int) $presId === (int) $presentacion->id) {
                continue; // Omitimos la presentación que se está evaluando
            }

            $otraPres = PresentacionProducto::find($presId);
            if ($otraPres && (int) $otraPres->producto_id === (int) $producto->id) {
                $unidadesFisicasOcupadasPorOtras += ((int) $item['cantidad'] * (int) $otraPres->cantidad_contenida);
            }
        }

        $unidadesFisicasRequeridas = $cantidadDeseada * (int) $presentacion->cantidad_contenida;
        $unidadesFisicasDisponibles = $stockFisicoTotal - $unidadesFisicasOcupadasPorOtras;

        if ($unidadesFisicasRequeridas > $unidadesFisicasDisponibles) {
            $maxPresentaciones = $presentacion->cantidad_contenida > 0
                ? max(0, intdiv($unidadesFisicasDisponibles, $presentacion->cantidad_contenida))
                : 0;

            return [
                'valido' => false,
                'mensaje' => "Stock insuficiente para '{$producto->nombre_producto}'. Solo quedan {$unidadesFisicasDisponibles} unidades físicas disponibles (máximo: {$maxPresentaciones} {$presentacion->tipo}(s)).",
            ];
        }

        return [
            'valido' => true,
            'mensaje' => '',
        ];
    }
}
