<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'presentaciones'])->paginate(10);

        return view('admin.productos.index', compact('productos'));
    }
}
