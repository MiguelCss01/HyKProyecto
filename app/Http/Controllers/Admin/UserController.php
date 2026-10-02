<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Obtener clientes filtrando opcionalmente por tipo
        $query = User::where('role', 'cliente');

        if ($request->has('tipo') && in_array($request->tipo, ['minorista', 'mayorista'])) {
            $query->where('tipo_cliente', $request->tipo);
        }

        // Paginamos para que no se rompa la vista si hay muchos
        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function destroy(User $usuario)
    {
        // Asegurarnos de que no borren a un admin por accidente
        if ($usuario->role === 'admin') {
            return redirect()->back()->with('error', 'No puedes eliminar a un administrador.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
