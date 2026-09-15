<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    protected Categoria $categoria;

    protected Producto $producto;

    protected PresentacionProducto $presentacionUnidad;

    protected PresentacionProducto $presentacionPack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoria = Categoria::create([
            'nombre_categoria' => 'Bebidas',
        ]);

        // Producto con 20 unidades de stock físico
        $this->producto = Producto::create([
            'categoria_id' => $this->categoria->id,
            'nombre_producto' => 'Gaseosa Cola 2.25L',
            'descripcion_producto' => 'Gaseosa refrescante',
            'imagen_url' => 'https://example.com/gaseosa.jpg',
            'stock_actual' => 20,
            'stock_minimo' => 5,
            'activo' => true,
        ]);

        // Presentación Unidad (1 unidad física)
        $this->presentacionUnidad = PresentacionProducto::create([
            'producto_id' => $this->producto->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 1200,
            'precio_mayorista' => 1000,
        ]);

        // Presentación Pack (6 unidades físicas)
        $this->presentacionPack = PresentacionProducto::create([
            'producto_id' => $this->producto->id,
            'tipo' => 'pack',
            'cantidad_contenida' => 6,
            'precio_minorista' => 7000,
            'precio_mayorista' => 5500,
        ]);
    }

    /**
     * RF 3.1: Cliente puede agregar productos sin iniciar sesión previa (guest cart).
     */
    public function test_invitado_puede_agregar_producto_al_carrito_sesion(): void
    {
        $response = $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 2,
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('carrito');

        $carrito = session('carrito');
        $this->assertArrayHasKey($this->presentacionUnidad->id, $carrito);
        $this->assertEquals(2, $carrito[$this->presentacionUnidad->id]['cantidad']);
    }

    /**
     * RF 3.5: Valida y rechaza cuando la cantidad solicitada supera el stock físico.
     */
    public function test_rechaza_agregar_cuando_supera_stock_disponible(): void
    {
        // 4 packs x 6 unidades = 24 unidades, pero stock_actual es 20
        $response = $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionPack->id,
            'cantidad' => 4,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Stock insuficiente', session('error'));

        // El carrito debe continuar vacío
        $this->assertEmpty(session('carrito', []));
    }

    /**
     * RF 3.2: Modificar cantidad de unidades en el carrito.
     */
    public function test_puede_actualizar_cantidad_en_el_carrito(): void
    {
        // Primero agregamos 1 pack (6 unidades de 20 disponibles)
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionPack->id,
            'cantidad' => 1,
        ]);

        // Ahora actualizamos a 2 packs (12 unidades de 20 disponibles)
        $response = $this->put(route('carrito.actualizar', $this->presentacionPack->id), [
            'cantidad' => 2,
        ]);

        $response->assertSessionHas('success');
        $carrito = session('carrito');
        $this->assertEquals(2, $carrito[$this->presentacionPack->id]['cantidad']);
    }

    /**
     * RF 3.3: Quitar un artículo específico del carrito.
     */
    public function test_puede_eliminar_producto_del_carrito(): void
    {
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 3,
        ]);

        $response = $this->delete(route('carrito.eliminar', $this->presentacionUnidad->id));

        $response->assertSessionHas('success');
        $carrito = session('carrito');
        $this->assertArrayNotHasKey($this->presentacionUnidad->id, $carrito);
    }

    /**
     * RF 3.3: Vaciar el carrito por completo.
     */
    public function test_puede_vaciar_el_carrito(): void
    {
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 1,
        ]);
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionPack->id,
            'cantidad' => 1,
        ]);

        $response = $this->delete(route('carrito.vaciar'));

        $response->assertRedirect(route('carrito.index'));
        $this->assertEmpty(session('carrito', []));
    }

    /**
     * RF 3.4 & RF 1.2: Consulta de carrito con precios diferenciados minorista vs mayorista.
     */
    public function test_precios_diferenciados_segun_tipo_de_cliente(): void
    {
        // 1. Como invitado / minorista: precio_minorista
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionPack->id,
            'cantidad' => 2,
        ]);

        $response = $this->get(route('carrito.index'));
        $response->assertStatus(200);
        // 2 packs a precio minorista (7.000) = 14.000
        $response->assertSee('14.000');

        // 2. Como cliente mayorista logueado: precio_mayorista
        $usuarioMayorista = User::create([
            'name' => 'Comercial Norte',
            'email' => 'mayorista@test.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'mayorista',
            'role' => 'cliente',
        ]);

        $this->actingAs($usuarioMayorista);

        $responseMayorista = $this->get(route('carrito.index'));
        $responseMayorista->assertStatus(200);
        // 2 packs a precio mayorista (5.500) = 11.000
        $responseMayorista->assertSee('11.000');
    }

    /**
     * Sincronización: Al autenticarse, los ítems de sesión se guardan en la BD.
     */
    public function test_sincroniza_carrito_a_bd_al_iniciar_sesion(): void
    {
        $user = User::create([
            'name' => 'Juan Perez',
            'email' => 'juan@test.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        // Agrega como invitado en la sesión
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 4,
        ]);

        // Login a través de la ruta web
        $this->post('/login', [
            'email' => 'juan@test.com',
            'password' => 'password123',
        ]);

        // Verifica que la tabla carritos y item_carritos en BD se pobló
        $this->assertDatabaseHas('carritos', [
            'user_id' => $user->id,
            'total' => 4800, // 4 x 1200
        ]);

        $this->assertDatabaseHas('item_carritos', [
            'presentacion_producto_id' => $this->presentacionUnidad->id,
            'cantidad' => 4,
        ]);
    }
}
