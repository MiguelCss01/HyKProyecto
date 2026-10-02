<?php

namespace App\Http\Controllers;

use App\Mail\ProfileSecurityCodeMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PerfilController extends Controller
{
    /**
     * Muestra la vista principal del perfil de cliente.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $isVerified = $this->isProfileVerified();

        return view('perfil.index', [
            'user' => $user,
            'isVerified' => $isVerified,
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    /**
     * Verifica la identidad del usuario mediante su contraseña actual.
     */
    public function verifyPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_actual' => ['required', 'string'],
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($request->password_actual, $user->password)) {
            return back()
                ->withErrors(['password_actual' => 'La contraseña actual ingresada es incorrecta.'])
                ->with('open_security_modal', true)
                ->with('security_tab', 'password');
        }

        // Autorizar sesión para edición de perfil
        session(['profile_verified_at' => now()->timestamp]);
        session()->forget(['profile_security_code', 'profile_security_code_expires_at']);

        return redirect()->route('perfil.edit')->with(
            'success',
            'Identidad confirmada exitosamente. Ya puedes modificar tus datos y tu contraseña.'
        );
    }

    /**
     * Genera y envía un código de seguridad de 6 dígitos al correo del usuario.
     */
    public function sendCode(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'profile_security_code' => Hash::make($code),
            'profile_security_code_expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        Mail::to($user->email)->send(new ProfileSecurityCodeMail($code, 15));

        return back()
            ->with('success', 'Hemos enviado un código de seguridad de 6 dígitos a tu correo ('.$this->maskEmail($user->email).').')
            ->with('open_security_modal', true)
            ->with('security_tab', 'code')
            ->with('code_sent', true);
    }

    /**
     * Verifica el código de seguridad de 6 dígitos recibido por correo.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Debes ingresar el código de verificación.',
            'code.digits' => 'El código debe tener exactamente 6 dígitos numéricos.',
        ]);

        $hashedCode = session('profile_security_code');
        $expiresAt = session('profile_security_code_expires_at');

        if (! $hashedCode || ! $expiresAt || now()->timestamp > $expiresAt) {
            session()->forget(['profile_security_code', 'profile_security_code_expires_at']);

            return back()
                ->withErrors(['code' => 'El código de seguridad ha expirado o no es válido. Por favor solicita uno nuevo.'])
                ->with('open_security_modal', true)
                ->with('security_tab', 'code');
        }

        if (! Hash::check($request->code, $hashedCode)) {
            return back()
                ->withErrors(['code' => 'El código de verificación ingresado es incorrecto.'])
                ->with('open_security_modal', true)
                ->with('security_tab', 'code')
                ->with('code_sent', true);
        }

        // Autorizar sesión para edición de perfil
        session(['profile_verified_at' => now()->timestamp]);
        session()->forget(['profile_security_code', 'profile_security_code_expires_at']);

        return redirect()->route('perfil.edit')->with(
            'success',
            'Identidad confirmada exitosamente. Ya puedes modificar tus datos y tu contraseña.'
        );
    }

    /**
     * Muestra la pantalla para editar datos y contraseña (requiere verificación previa).
     */
    public function edit(): View|RedirectResponse
    {
        if (! $this->isProfileVerified()) {
            return redirect()->route('perfil.index')
                ->with('error', 'Por motivos de seguridad, debes verificar tu identidad antes de modificar tus datos.')
                ->with('open_security_modal', true);
        }

        /** @var User $user */
        $user = Auth::user();

        return view('perfil.edit', compact('user'));
    }

    /**
     * Procesa la actualización de los datos del perfil y contraseña.
     */
    public function update(Request $request): RedirectResponse
    {
        if (! $this->isProfileVerified()) {
            return redirect()->route('perfil.index')
                ->with('error', 'La autorización de seguridad ha expirado. Por favor confírmala nuevamente.')
                ->with('open_security_modal', true);
        }

        /** @var User $user */
        $user = Auth::user();
        $isMinorista = $user->tipo_cliente === 'minorista';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'regex:/^[0-9\-\+\s\(\)]+$/', 'min:8', 'max:20'],
            'dni' => $isMinorista ? ['required', 'digits_between:7,8'] : ['nullable'],
            'cuit' => ! $isMinorista ? ['required', 'regex:/^[0-9]{2}\-?[0-9]{8}\-?[0-9]{1}$/'] : ['nullable'],
            'razon_social' => ! $isMinorista ? ['required', 'string', 'max:255'] : ['nullable'],
            'nueva_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required' => 'El nombre y apellido son obligatorios.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.regex' => 'El formato del teléfono solo permite números, espacios y los símbolos + o -.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'dni.required' => 'El DNI es obligatorio para clientes minoristas.',
            'dni.digits_between' => 'El DNI debe tener entre 7 y 8 números exactos (sin puntos).',
            'cuit.required' => 'El CUIT es obligatorio para clientes mayoristas.',
            'cuit.regex' => 'El CUIT no tiene un formato válido (Ej: 20-12345678-9).',
            'razon_social.required' => 'La razón social es obligatoria para clientes mayoristas.',
            'nueva_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'nueva_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ];

        $request->validate($rules, $messages);

        $user->name = $request->name;
        $user->telefono = $request->telefono;

        if ($isMinorista) {
            $user->dni = $request->dni;
        } else {
            $user->cuit = $request->cuit;
            $user->razon_social = $request->razon_social;
        }

        // Si se especificó una nueva contraseña, actualizarla encriptada
        if ($request->filled('nueva_password')) {
            $user->password = Hash::make($request->nueva_password);
        }

        $user->save();

        // Limpiar la autorización de edición
        session()->forget('profile_verified_at');

        return redirect()->route('perfil.index')->with(
            'success',
            '¡Tus datos y perfil fueron actualizados exitosamente!'
        );
    }

    /**
     * Determina si la sesión actual cuenta con verificación de seguridad activa (máximo 15 min).
     */
    private function isProfileVerified(): bool
    {
        $verifiedAt = session('profile_verified_at');

        if (! $verifiedAt) {
            return false;
        }

        return (now()->timestamp - $verifiedAt) < (15 * 60);
    }

    /**
     * Oculta parte del correo electrónico para proteger la privacidad.
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];
        $len = strlen($name);

        $maskedName = $len > 2
            ? substr($name, 0, 2).str_repeat('*', max(1, $len - 2))
            : $name.'***';

        return $maskedName.'@'.$domain;
    }
}
