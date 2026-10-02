<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        // Métricas rápidas
        $bajoStock = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $pedidosPendientes = Pedido::where('estado', 'pendiente')->count();
        $ingresosHoy = Pedido::whereDate('created_at', today())->sum('total');
        
        // Tablas resumen
        $ultimosPedidos = Pedido::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $nuevosClientes = User::where('role', 'cliente')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'bajoStock',
            'pedidosPendientes',
            'ingresosHoy',
            'ultimosPedidos',
            'nuevosClientes'
        ));
    }
}
