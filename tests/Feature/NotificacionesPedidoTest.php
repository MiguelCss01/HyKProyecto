<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Notifications\EstadoPedidoActualizadoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificacionesPedidoTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente;

    protected User $admin;

    protected Pedido $pedido;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliente = User::create([
            'name' => 'Cliente Notificaciones',
            'email' => 'cliente.notif@example.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin.notif@example.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'admin',
        ]);

        $categoria = Categoria::create(['nombre_categoria' => 'Almacén']);
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre_producto' => 'Fideos 500g',
            'descripcion_producto' => 'Fideos secos',
            'imagen_url' => 'https://example.com/fideos.jpg',
            'stock_actual' => 50,
            'stock_minimo' => 5,
            'activo' => true,
        ]);

        $this->pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now()->toDateString(),
            'total' => 1500,
            'estado' => 'PENDIENTE',
        ]);
    }

    public function test_admin_actualiza_estado_a_en_preparacion_y_genera_notificacion(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pedidos.update', $this->pedido), [
            'estado' => 'EN_PREPARACION',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pedidos', [
            'id' => $this->pedido->id,
            'estado' => 'EN_PREPARACION',
        ]);

        // Verificar que se haya insertado en la tabla de notificaciones
        $this->assertEquals(1, $this->cliente->notifications()->count());
        $notificacion = $this->cliente->notifications()->first();
        $this->assertEquals("Tu pedido #{$this->pedido->id} está en preparación", $notificacion->data['titulo']);
        $this->assertEquals('EN_PREPARACION', $notificacion->data['estado_nuevo']);
    }

    public function test_admin_actualiza_estado_a_listo_retiro_y_genera_notificacion(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pedidos.update', $this->pedido), [
            'estado' => 'LISTO_RETIRO',
        ]);

        $response->assertSessionHas('success');
        $notificacion = $this->cliente->notifications()->first();
        $this->assertNotNull($notificacion);
        $this->assertEquals("¡Tu pedido #{$this->pedido->id} está listo para retirar en el local!", $notificacion->data['titulo']);
        $this->assertStringContainsString('listo para que pases a retirarlo', $notificacion->data['mensaje']);
    }

    public function test_cliente_puede_ver_notificaciones_en_seccion_separada(): void
    {
        $this->cliente->notify(new EstadoPedidoActualizadoNotification($this->pedido, 'PENDIENTE', 'LISTO_RETIRO'));

        // En la página separada de notificaciones se visualizan correctamente
        $response = $this->actingAs($this->cliente)->get(route('notificaciones.index'));

        $response->assertOk();
        $response->assertSee('Mis Notificaciones y Avisos');
        $response->assertSee("¡Tu pedido #{$this->pedido->id} está listo para retirar en el local!");

        // En la página de Mi Perfil no se mezclan debajo de los datos personales
        $responsePerfil = $this->actingAs($this->cliente)->get(route('perfil.index'));
        $responsePerfil->assertOk();
        $responsePerfil->assertDontSee("¡Tu pedido #{$this->pedido->id} está listo para retirar en el local!");
    }

    public function test_cliente_puede_marcar_notificacion_como_leida(): void
    {
        $this->cliente->notify(new EstadoPedidoActualizadoNotification($this->pedido, 'PENDIENTE', 'EN_PREPARACION'));

        $notificacion = $this->cliente->unreadNotifications()->first();
        $this->assertNotNull($notificacion);

        $response = $this->actingAs($this->cliente)->patch(route('notificaciones.leer', $notificacion->id));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $this->cliente->unreadNotifications()->count());
    }

    public function test_cliente_puede_marcar_todas_las_notificaciones_como_leidas(): void
    {
        $this->cliente->notify(new EstadoPedidoActualizadoNotification($this->pedido, 'PENDIENTE', 'EN_PREPARACION'));
        $this->cliente->notify(new EstadoPedidoActualizadoNotification($this->pedido, 'EN_PREPARACION', 'LISTO_RETIRO'));

        $this->assertEquals(2, $this->cliente->unreadNotifications()->count());

        $response = $this->actingAs($this->cliente)->post(route('notificaciones.marcar-todas'));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $this->cliente->unreadNotifications()->count());
    }
}
