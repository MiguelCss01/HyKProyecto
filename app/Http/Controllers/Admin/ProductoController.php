<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'presentaciones'])->paginate(10);

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::all();

        return view('admin.productos.create', compact('categorias'));
    }

    public function store(SaveProductoRequest $request)
    {
        // 1. Usar una transacción para asegurar que se guarde el producto Y sus presentaciones juntos
        DB::transaction(function () use ($request) {
            // Guardar datos básicos
            $producto = Producto::create([
                'nombre_producto' => $request->nombre_producto,
                'descripcion_producto' => $request->descripcion_producto,
                'categoria_id' => $request->categoria_id,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'activo' => $request->has('activo'),
            ]);

            // Guardar presentaciones usando la relación
            foreach ($request->presentaciones as $pres) {
                $producto->presentaciones()->create($pres);
            }
        });

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        // Cargar el producto con sus presentaciones
        $producto->load('presentaciones');
        $categorias = Categoria::all();

        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(SaveProductoRequest $request, Producto $producto)
    {
        DB::transaction(function () use ($request, $producto) {
            $producto->update([
                'nombre_producto' => $request->nombre_producto,
                'descripcion_producto' => $request->descripcion_producto,
                'categoria_id' => $request->categoria_id,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'activo' => $request->has('activo'),
            ]);

            // Borrar presentaciones viejas y recrearlas (enfoque más simple)
            $producto->presentaciones()->delete();

            foreach ($request->presentaciones as $pres) {
                $producto->presentaciones()->create($pres);
            }
        });

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }
}
