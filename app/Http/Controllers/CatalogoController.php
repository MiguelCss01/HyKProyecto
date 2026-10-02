<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(): View
    {
        // Obtener categorías con el conteo de productos
        $categorias = Categoria::withCount('productos')->orderBy('nombre_categoria')->get();

        // Obtener productos activos con sus presentaciones y categoría
        $productos = Producto::with(['presentaciones', 'categoria'])
            ->where('activo', 1)
            ->get();

        return view('catalogo', compact('categorias', 'productos'));
    }
}
