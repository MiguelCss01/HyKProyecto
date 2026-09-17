<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MisPedidosTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente;

    protected Categoria $categoria;

    protected Producto $producto;

    protected PresentacionProducto $presentacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliente = User::create([
            'name' => 'Comprador Demo',
            'email' => 'comprador@demo.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $this->categoria = Categoria::create([
            'nombre_categoria' => 'Alimentos',
        ]);

        $this->producto = Producto::create([
            'categoria_id' => $this->categoria->id,
            'nombre_producto' => 'Arroz Largo Fino 1kg',
            'descripcion_producto' => 'Arroz de primera calidad',
            'imagen_url' => 'https://example.com/arroz.jpg',
            'stock_actual' => 100,
            'stock_minimo' => 10,
            'activo' => true,
        ]);

        $this->presentacion = PresentacionProducto::create([
            'producto_id' => $this->producto->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 1500,
            'precio_mayorista' => 1200,
        ]);
    }

    /**
     * Invitado no puede acceder a Mis Pedidos y es redirigido al login.
     */
    public function test_invitado_es_redirigido_al_login_al_intentar_ver_mis_pedidos(): void
    {
        $response = $this->get(route('pedidos.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Cliente autenticado sin pedidos ve el mensaje de estado vacío.
     */
    public function test_cliente_sin_pedidos_visualiza_estado_vacio(): void
    {
        $this->actingAs($this->cliente);

        $response = $this->get(route('pedidos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pedidos.index');
        $response->assertSee('Aún no tienes pedidos');
        $response->assertSee(route('catalogo'));
    }

    /**
     * Cliente autenticado ve su historial de pedidos con su estado PENDIENTE.
     */
    public function test_cliente_visualiza_su_historial_de_pedidos_y_estado_pendiente(): void
    {
        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 4500,
            'estado' => 'PENDIENTE',
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'presentacion_producto_id' => $this->presentacion->id,
            'cantidad' => 3,
            'precio_unitario' => 1500,
        ]);

        $this->actingAs($this->cliente);

        $response = $this->get(route('pedidos.index'));

        $response->assertStatus(200);
        $response->assertSee("#{$pedido->id}");
        $response->assertSee('Pendiente');
        $response->assertSee('4.500');
        $response->assertSee(route('pedidos.show', $pedido->id));
    }

    /**
     * Cliente puede ver el detalle específico de su pedido.
     */
    public function test_cliente_puede_ver_el_detalle_de_su_pedido(): void
    {
        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 3000,
            'estado' => 'PENDIENTE',
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'presentacion_producto_id' => $this->presentacion->id,
            'cantidad' => 2,
            'precio_unitario' => 1500,
        ]);

        $this->actingAs($this->cliente);

        $response = $this->get(route('pedidos.show', $pedido->id));

        $response->assertStatus(200);
        $response->assertViewIs('pedidos.show');
        $response->assertSee("Detalle del Pedido #{$pedido->id}");
        $response->assertSee('Pendiente');
        $response->assertSee('Arroz Largo Fino 1kg');
        $response->assertSee('3.000');
    }

    /**
     * Un cliente no puede ver el pedido de otro usuario (aislamiento de seguridad).
     */
    public function test_cliente_no_puede_ver_pedido_de_otro_usuario(): void
    {
        $otroCliente = User::create([
            'name' => 'Otro Usuario',
            'email' => 'otro@usuario.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $pedidoAjeno = Pedido::create([
            'user_id' => $otroCliente->id,
            'fecha_pedido' => now(),
            'total' => 1500,
            'estado' => 'PENDIENTE',
        ]);

        $this->actingAs($this->cliente);

        // Al intentar ver el pedido del otro cliente, debe fallar con 404
        $response = $this->get(route('pedidos.show', $pedidoAjeno->id));

        $response->assertStatus(404);
    }
}
