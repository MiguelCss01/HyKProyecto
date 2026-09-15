<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Muestra el contenido detallado del carrito de compras.
     */
    public function index(): View
    {
        $cart = $this->cartService->obtenerCarrito();

        return view('carrito.index', compact('cart'));
    }

    /**
     * Agrega una presentación de producto al carrito.
     */
    public function agregar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'presentacion_id' => 'required|exists:presentacion_productos,id',
            'cantidad' => 'required|integer|min:1',
        ], [
            'presentacion_id.required' => 'Debes seleccionar una presentación de producto.',
            'presentacion_id.exists' => 'La presentación seleccionada no es válida.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima a agregar es 1 unidad.',
        ]);

        $resultado = $this->cartService->agregar(
            (int) $validated['presentacion_id'],
            (int) $validated['cantidad']
        );

        if (! $resultado['success']) {
            return back()->with('error', $resultado['message'])->withInput();
        }

        return back()->with('success', $resultado['message']);
    }

    /**
     * Modifica la cantidad de una presentación en el carrito.
     */
    public function actualizar(Request $request, int $presentacionId): RedirectResponse
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0',
        ], [
            'cantidad.required' => 'La cantidad es requerida.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ]);

        $resultado = $this->cartService->actualizar(
            $presentacionId,
            (int) $validated['cantidad']
        );

        if (! $resultado['success']) {
            return back()->with('error', $resultado['message']);
        }

        return back()->with('success', $resultado['message']);
    }

    /**
     * Quita una presentación específica del carrito.
     */
    public function eliminar(int $presentacionId): RedirectResponse
    {
        $resultado = $this->cartService->eliminar($presentacionId);

        return back()->with('success', $resultado['message']);
    }

    /**
     * Vacía todos los artículos del carrito.
     */
    public function vaciar(): RedirectResponse
    {
        $resultado = $this->cartService->vaciar();

        return redirect()->route('carrito.index')->with('success', $resultado['message']);
    }
}
