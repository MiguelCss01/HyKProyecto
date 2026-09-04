<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        // Obtener categorías para el menú/filtros
        $categorias = Categoria::all();

        // Obtener productos con sus presentaciones para armar las tarjetas
        $productos = Producto::with('presentaciones')->get();

        return view('catalogo', compact('categorias', 'productos'));
    }
}
