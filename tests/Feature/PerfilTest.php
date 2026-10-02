<?php

namespace Tests\Feature;

use App\Mail\ProfileSecurityCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    protected User $userMinorista;

    protected User $userMayorista;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userMinorista = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('password123'),
            'telefono' => '11-4567-8901',
            'tipo_cliente' => 'minorista',
            'dni' => '38123456',
            'role' => 'cliente',
        ]);

        $this->userMayorista = User::create([
            'name' => 'Distribuidora Norte SRL',
            'email' => 'compras@distribuidora.com',
            'password' => Hash::make('mayorista123'),
            'telefono' => '11-9876-5432',
            'tipo_cliente' => 'mayorista',
            'cuit' => '30-71234567-8',
            'razon_social' => 'Distribuidora Norte SRL',
            'role' => 'cliente',
        ]);
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('perfil.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_client_can_view_profile(): void
    {
        $response = $this->actingAs($this->userMinorista)->get(route('perfil.index'));

        $response->assertStatus(200);
        $response->assertViewIs('perfil.index');
        $response->assertSee('Juan Pérez');
        $response->assertSee('juan.perez@example.com');
        $response->assertSee('38123456');
        $response->assertSee('Modificar Datos y Contraseña');
        $response->assertSee('id="security-modal-backdrop"', false);
    }

    public function test_client_cannot_access_edit_screen_without_security_verification(): void
    {
        $response = $this->actingAs($this->userMinorista)->get(route('perfil.edit'));

        $response->assertRedirect(route('perfil.index'));
        $response->assertSessionHas('error');
    }

    public function test_client_can_verify_identity_with_current_password(): void
    {
        // 1. Contraseña incorrecta falla
        $failResponse = $this->actingAs($this->userMinorista)
            ->post(route('perfil.verify.password'), [
                'password_actual' => 'clave_erronea',
            ]);

        $failResponse->assertSessionHasErrors('password_actual');
        $this->assertNull(session('profile_verified_at'));

        // 2. Contraseña correcta autoriza la sesión
        $successResponse = $this->actingAs($this->userMinorista)
            ->post(route('perfil.verify.password'), [
                'password_actual' => 'password123',
            ]);

        $successResponse->assertRedirect(route('perfil.edit'));
        $this->assertNotNull(session('profile_verified_at'));

        // 3. Ya puede acceder a la pantalla de edición
        $editResponse = $this->actingAs($this->userMinorista)->get(route('perfil.edit'));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Modificar Perfil y Seguridad');
    }

    public function test_client_can_verify_identity_with_email_code(): void
    {
        Mail::fake();

        // 1. Solicitar código por correo
        $sendResponse = $this->actingAs($this->userMinorista)
            ->post(route('perfil.send.code'));

        $sendResponse->assertSessionHas('success');
        $sendResponse->assertSessionHas('code_sent', true);
        $this->assertNotNull(session('profile_security_code'));

        Mail::assertSent(ProfileSecurityCodeMail::class, function ($mail) {
            return $mail->hasTo('juan.perez@example.com');
        });

        // 2. Probar código erróneo
        $failResponse = $this->actingAs($this->userMinorista)
            ->post(route('perfil.verify.code'), [
                'code' => '999999',
            ]);

        $failResponse->assertSessionHasErrors('code');
        $this->assertNull(session('profile_verified_at'));

        // 3. Para simular el código real, guardamos uno conocido hasheado en sesión
        $validCode = '123456';
        session([
            'profile_security_code' => Hash::make($validCode),
            'profile_security_code_expires_at' => now()->addMinutes(15)->timestamp,
        ]);

        $successResponse = $this->actingAs($this->userMinorista)
            ->post(route('perfil.verify.code'), [
                'code' => $validCode,
            ]);

        $successResponse->assertRedirect(route('perfil.edit'));
        $this->assertNotNull(session('profile_verified_at'));
    }

    public function test_verified_client_can_update_profile_data(): void
    {
        // Simular sesión ya verificada
        session(['profile_verified_at' => now()->timestamp]);

        $response = $this->actingAs($this->userMinorista)
            ->put(route('perfil.update'), [
                'name' => 'Juan Carlos Pérez',
                'telefono' => '11-9999-8888',
                'dni' => '39999888',
            ]);

        $response->assertRedirect(route('perfil.index'));
        $response->assertSessionHas('success');

        $this->userMinorista->refresh();
        $this->assertEquals('Juan Carlos Pérez', $this->userMinorista->name);
        $this->assertEquals('11-9999-8888', $this->userMinorista->telefono);
        $this->assertEquals('39999888', $this->userMinorista->dni);

        // La autorización de edición debe limpiarse tras guardar
        $this->assertNull(session('profile_verified_at'));
    }

    public function test_verified_client_can_change_password(): void
    {
        // Simular sesión ya verificada
        session(['profile_verified_at' => now()->timestamp]);

        $response = $this->actingAs($this->userMinorista)
            ->put(route('perfil.update'), [
                'name' => 'Juan Pérez',
                'telefono' => '11-4567-8901',
                'dni' => '38123456',
                'nueva_password' => 'nuevaClaveSegura2026',
                'nueva_password_confirmation' => 'nuevaClaveSegura2026',
            ]);

        $response->assertRedirect(route('perfil.index'));
        $response->assertSessionHas('success');

        $this->userMinorista->refresh();
        $this->assertTrue(Hash::check('nuevaClaveSegura2026', $this->userMinorista->password));
    }
}
