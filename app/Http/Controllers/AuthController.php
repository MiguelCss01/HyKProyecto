<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar la vista de acceso
    public function showAcceso()
    {
        return view('auth.acceso');
    }

    // Registrar usuario
    public function register(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|in:retail,wholesale',
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9\-\+\s\(\)]+$/|min:8|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'dni' => 'nullable|required_if:customer_type,retail|digits_between:7,8',
            'cuit' => 'nullable|required_if:customer_type,wholesale|regex:/^[0-9]{2}\-?[0-9]{8}\-?[0-9]{1}$/',
            'business_name' => 'nullable|required_if:customer_type,wholesale|string|max:255',
        ], [
            'name.required' => 'El nombre y apellido son obligatorios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios y los símbolos + o -.',
            'dni.digits_between' => 'El DNI debe tener entre 7 y 8 números exactos (sin puntos).',
            'dni.required_if' => 'El DNI es obligatorio para clientes minoristas.',
            'cuit.regex' => 'El CUIT no tiene un formato válido (Ej: 20-12345678-9).',
            'cuit.required_if' => 'El CUIT es obligatorio para clientes mayoristas.',
            'business_name.required_if' => 'La razón social es obligatoria para clientes mayoristas.',
        ]);

        $tipoCliente = $request->customer_type == 'retail' ? 'minorista' : 'mayorista';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->phone,
            'tipo_cliente' => $tipoCliente,
            'dni' => $request->dni,
            'cuit' => $request->cuit,
            'razon_social' => $request->business_name,
            'role' => 'cliente', // Por defecto todos son clientes
        ]);

        // Redirigir de nuevo a la pantalla de acceso para que inicie sesión manualmente
        return redirect('/acceso')->with('success', '¡Cuenta creada con éxito! Ahora por favor iniciá sesión.');
    }

    // Iniciar Sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
