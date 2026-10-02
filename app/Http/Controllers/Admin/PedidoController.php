<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with('user');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['user', 'detalles.presentacionProducto.producto']);
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobado,rechazado,en_preparacion,listo_retiro,entregado,cancelado'
        ]);

        $pedido->update([
            'estado' => $request->estado
        ]);

        // Acá en un futuro se podría restar el stock si pasa a 'aprobado' o 'entregado', 
        // dependiendo de la lógica de negocio (RF 6.4). 
        // Por ahora solo cambiamos el estado visualmente.

        return redirect()->back()->with('success', 'Estado del pedido actualizado a: ' . strtoupper($request->estado));
    }
}
