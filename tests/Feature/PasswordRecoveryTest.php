<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Usuario Test',
            'email' => 'usuario@test.com',
            'password' => Hash::make('claveVieja123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);
    }

    public function test_renders_forgot_password_screen(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('¿Olvidaste tu contraseña?');
        $response->assertSee('Enviar código de verificación');
    }

    public function test_validates_email_is_required_and_exists(): void
    {
        // Correo vacío
        $responseEmpty = $this->post(route('password.email'), [
            'email' => '',
        ]);
        $responseEmpty->assertSessionHasErrors(['email']);

        // Correo no registrado
        $responseNotFound = $this->post(route('password.email'), [
            'email' => 'noexiste@correo.com',
        ]);
        $responseNotFound->assertSessionHasErrors(['email']);
    }

    public function test_generates_6_digit_code_stores_it_and_sends_email(): void
    {
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => $this->user->email,
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('success');
        $this->assertEquals($this->user->email, session('password_reset_email'));

        // Verificar que el registro existe en la base de datos
        $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
        $this->assertNotNull($record);

        // Verificar que el correo fue enviado con un código de 6 dígitos
        Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use ($record) {
            $this->assertEquals(6, strlen($mail->code));
            $this->assertTrue(ctype_digit($mail->code));
            $this->assertEquals(15, $mail->expiresInMinutes);
            $this->assertTrue(Hash::check($mail->code, $record->token));

            return $mail->hasTo($this->user->email);
        });
    }

    public function test_renders_verify_code_screen_only_with_active_session(): void
    {
        // Sin sesión redirige a password.request
        $responseWithoutSession = $this->get(route('password.code'));
        $responseWithoutSession->assertRedirect(route('password.request'));

        // Con sesión y token en BD muestra la vista
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $responseWithSession = $this->withSession(['password_reset_email' => $this->user->email])
            ->get(route('password.code'));

        $responseWithSession->assertStatus(200);
        $responseWithSession->assertSee('Verifica tu código');
        $responseWithSession->assertSee($this->user->email);
    }

    public function test_rejects_incorrect_verification_code(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $response = $this->withSession(['password_reset_email' => $this->user->email])
            ->post(route('password.verify_code'), [
                'code' => '999999',
            ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_rejects_expired_verification_code(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make('123456'),
            'created_at' => Carbon::now()->subMinutes(16), // Expirado (más de 15 min)
        ]);

        $response = $this->withSession(['password_reset_email' => $this->user->email])
            ->post(route('password.verify_code'), [
                'code' => '123456',
            ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('error');

        // Se debió eliminar el token expirado
        $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
        $this->assertNull($record);
    }

    public function test_accepts_valid_code_and_advances_to_reset_screen(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $response = $this->withSession(['password_reset_email' => $this->user->email])
            ->post(route('password.verify_code'), [
                'code' => '123456',
            ]);

        $this->assertTrue(session('password_reset_verified'));
        $resetToken = session('password_reset_token');
        $this->assertNotEmpty($resetToken);

        $response->assertRedirect(route('password.reset', ['token' => $resetToken]));

        // Verificar que podemos ver la pantalla de cambio de clave
        $resetScreenResponse = $this->withSession([
            'password_reset_email' => $this->user->email,
            'password_reset_token' => $resetToken,
        ])->get(route('password.reset', ['token' => $resetToken]));

        $resetScreenResponse->assertStatus(200);
        $resetScreenResponse->assertSee('Crea tu nueva contraseña');
        $resetScreenResponse->assertSee($this->user->email);
    }

    public function test_can_resend_verification_code(): void
    {
        Mail::fake();

        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make('111111'),
            'created_at' => now(),
        ]);

        $response = $this->withSession(['password_reset_email' => $this->user->email])
            ->post(route('password.resend_code'));

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('success');

        Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_validates_password_confirmation_and_minimum_length(): void
    {
        $rawToken = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make($rawToken),
            'created_at' => now(),
        ]);

        // Contraseña demasiado corta (menor a 8 caracteres)
        $shortPasswordResponse = $this->post(route('password.update'), [
            'token' => $rawToken,
            'email' => $this->user->email,
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        $shortPasswordResponse->assertSessionHasErrors(['password']);

        // Contraseñas no coinciden
        $mismatchResponse = $this->post(route('password.update'), [
            'token' => $rawToken,
            'email' => $this->user->email,
            'password' => 'nuevaClaveSegura123',
            'password_confirmation' => 'otraClaveDiferente456',
        ]);
        $mismatchResponse->assertSessionHasErrors(['password']);
    }

    public function test_resets_password_encrypts_it_invalidates_code_and_redirects_to_login(): void
    {
        $rawToken = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make($rawToken),
            'created_at' => now(),
        ]);

        $newPassword = 'miNuevaClaveSegura2026';

        $response = $this->withSession([
            'password_reset_email' => $this->user->email,
            'password_reset_token' => $rawToken,
            'password_reset_verified' => true,
        ])->post(route('password.update'), [
            'token' => $rawToken,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        // Redirige al login (ruta 'login' -> /acceso)
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // La contraseña encriptada en la base de datos debe ser válida para la nueva clave
        $this->user->refresh();
        $this->assertTrue(Hash::check($newPassword, $this->user->password));

        // El token/código en password_reset_tokens debe haberse eliminado (invalidado)
        $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
        $this->assertNull($record);

        // Las variables de recuperación en sesión deben haberse limpiado
        $this->assertNull(session('password_reset_email'));
        $this->assertNull(session('password_reset_token'));

        // Intentar iniciar sesión con la nueva contraseña debe ser exitoso
        $loginResponse = $this->post('/login', [
            'email' => $this->user->email,
            'password' => $newPassword,
        ]);
        $loginResponse->assertRedirect(route('catalogo'));
        $this->assertAuthenticatedAs($this->user);
    }
}
