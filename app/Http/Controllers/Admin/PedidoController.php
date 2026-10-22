<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['user', 'pago']);

        if ($request->filled('estado')) {
            $estado = strtolower($request->estado);
            if (in_array($estado, ['aprobado', 'pagado'])) {
                $query->whereIn('estado', ['aprobado', 'PAGADO', 'pagado', 'CONFIRMADO']);
            } elseif ($estado === 'pendiente') {
                $query->whereIn('estado', ['pendiente', 'PENDIENTE']);
            } elseif ($estado === 'entregado') {
                $query->whereIn('estado', ['entregado', 'ENTREGADO']);
            } else {
                $query->where('estado', $request->estado);
            }
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['user', 'detalles.presentacionProducto.producto', 'pago']);

        return view('admin.pedidos.show', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|string|max:50',
        ]);

        $pedido->update([
            'estado' => strtoupper($request->estado),
        ]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado a: '.strtoupper($request->estado));
    }
}
