<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Muestra la vista para solicitar el código de recuperación.
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Valida el correo, genera el código de 6 dígitos, lo almacena y lo envía por correo.
     */
    public function sendResetCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.exists' => 'No encontramos ninguna cuenta registrada con este correo electrónico.',
        ]);

        $email = (string) $request->email;

        // Generar código numérico aleatorio y seguro de 6 dígitos
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Almacenar el código hasheado con su timestamp de creación
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        // Guardar email en sesión para los siguientes pasos
        session(['password_reset_email' => $email]);

        // Enviar el correo con formato limpio
        Mail::to($email)->send(new PasswordResetCodeMail($code, 15));

        return redirect()->route('password.code')->with(
            'success',
            'Hemos enviado un código de verificación de 6 dígitos a tu correo electrónico.'
        );
    }

    /**
     * Muestra la vista para ingresar el código de verificación de 6 dígitos.
     */
    public function showCodeForm(Request $request): View|RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request')->with(
                'info',
                'Por favor ingresa tu correo electrónico para comenzar la recuperación.'
            );
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return redirect()->route('password.request')->with(
                'error',
                'No hay ninguna solicitud activa para este correo. Por favor inicia nuevamente.'
            );
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    /**
     * Reenvía un nuevo código de verificación al correo en proceso.
     */
    public function resendCode(Request $request): RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email || ! User::where('email', $email)->exists()) {
            return redirect()->route('password.request')->with(
                'error',
                'Sesión de recuperación no encontrada. Por favor ingresa tu correo nuevamente.'
            );
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        Mail::to($email)->send(new PasswordResetCodeMail($code, 15));

        return redirect()->route('password.code')->with(
            'success',
            'Te hemos enviado un nuevo código de verificación a tu correo.'
        );
    }

    /**
     * Valida el código de verificación ingresado y el tiempo de expiración (15 minutos).
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Debes ingresar el código de verificación.',
            'code.digits' => 'El código debe tener exactamente 6 dígitos numéricos.',
        ]);

        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request')->with(
                'error',
                'Tu sesión ha expirado. Por favor inicia el proceso nuevamente.'
            );
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return redirect()->route('password.request')->with(
                'error',
                'No se encontró una solicitud activa para este correo.'
            );
        }

        // Validar expiración (15 minutos)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            session()->forget('password_reset_email');

            return redirect()->route('password.request')->with(
                'error',
                'El código de verificación ha expirado (validez de 15 minutos). Por favor solicita uno nuevo.'
            );
        }

        // Validar el código numérico
        if (! Hash::check($request->code, $record->token)) {
            return back()->withErrors([
                'code' => 'El código de verificación ingresado es incorrecto.',
            ])->withInput();
        }

        // Código correcto: generar token temporal de restablecimiento seguro
        $resetToken = Str::random(60);

        DB::table('password_reset_tokens')->where('email', $email)->update([
            'token' => Hash::make($resetToken),
            'created_at' => now(),
        ]);

        session([
            'password_reset_verified' => true,
            'password_reset_token' => $resetToken,
        ]);

        return redirect()->route('password.reset', ['token' => $resetToken]);
    }

    /**
     * Muestra el formulario para definir la nueva contraseña.
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        $email = session('password_reset_email') ?? $request->query('email');

        if (! $email) {
            return redirect()->route('password.request')->with(
                'error',
                'Sesión no válida. Por favor inicia la recuperación de contraseña nuevamente.'
            );
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record || ! Hash::check($token, $record->token)) {
            return redirect()->route('password.request')->with(
                'error',
                'El enlace de recuperación es inválido o ya ha sido utilizado.'
            );
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')->with(
                'error',
                'El tiempo para restablecer tu contraseña ha expirado. Por favor solicita un nuevo código.'
            );
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Restablece la contraseña del usuario, encriptándola en la base de datos e invalidando el código/token.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'El token de restablecimiento es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo válido.',
            'email.exists' => 'No encontramos ninguna cuenta con este correo.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas ingresadas no coinciden.',
        ]);

        $email = (string) $request->email;
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return redirect()->route('password.request')->with(
                'error',
                'El token de restablecimiento es inválido o ya ha sido utilizado.'
            );
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')->with(
                'error',
                'El tiempo para restablecer tu contraseña ha expirado. Por favor solicita un nuevo código.'
            );
        }

        // Actualizar la contraseña encriptada en la base de datos
        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Invalidar el código/token utilizado en la base de datos
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Limpiar las variables de recuperación en la sesión
        session()->forget(['password_reset_email', 'password_reset_token', 'password_reset_verified']);

        // Redirigir al inicio de sesión con mensaje de éxito
        return redirect()->route('login')->with(
            'success',
            '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión con tu nueva clave.'
        );
    }
}
