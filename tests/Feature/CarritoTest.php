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

    /**
     * RF Flujo Checkout: Usuario invitado al presionar confirmar pedido es redirigido a login con mensaje flash y URL prevista.
     */
    public function test_invitado_al_confirmar_pedido_es_redirigido_a_login_con_mensaje_flash_y_url_prevista(): void
    {
        // Invitado agrega un producto al carrito
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 2,
        ]);

        // Intenta proceder a la confirmación
        $response = $this->get(route('carrito.confirmar'));

        // Debe ser redirigido a login
        $response->assertRedirect(route('login'));
        // Debe tener el mensaje flash informativo explicativo
        $response->assertSessionHas('info', 'Debes iniciar sesión para confirmar tu pedido.');
        // Debe registrar la URL prevista (url.intended)
        $this->assertEquals(route('carrito.confirmar'), session('url.intended'));
    }

    /**
     * RF Flujo Checkout: Al autenticarse tras la redirección, el usuario vuelve a la confirmación con su carrito intacto.
     */
    public function test_usuario_tras_autenticarse_vuelve_a_confirmacion_con_carrito_intacto(): void
    {
        $user = User::create([
            'name' => 'Cliente Test',
            'email' => 'cliente@test.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        // Invitado agrega productos al carrito (2 unidades x $1200 = $2400)
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 2,
        ]);

        // Al intentar confirmar como invitado se guarda url.intended
        $this->get(route('carrito.confirmar'));

        // Se autentica mediante login
        $loginResponse = $this->post('/login', [
            'email' => 'cliente@test.com',
            'password' => 'password123',
        ]);

        // Debe redirigir automáticamente a la URL prevista (intended: carrito.confirmar)
        $loginResponse->assertRedirect(route('carrito.confirmar'));

        // Al acceder a la confirmación ya autenticado
        $confirmacionResponse = $this->get(route('carrito.confirmar'));
        $confirmacionResponse->assertStatus(200);
        $confirmacionResponse->assertViewIs('pedidos.confirmar');
        // El carrito se mantiene intacto con el total de 2400
        $confirmacionResponse->assertSee('2.400');
        $confirmacionResponse->assertSee('Cliente Test');
    }

    /**
     * RF Flujo Checkout: Usuario ya autenticado avanza directamente a la confirmación de pedido sin redirección a login.
     */
    public function test_usuario_ya_autenticado_avanza_directamente_a_confirmacion(): void
    {
        $user = User::create([
            'name' => 'Maria Lopez',
            'email' => 'maria@test.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $this->actingAs($user);

        // Agrega producto al carrito
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 1,
        ]);

        // Avanza directamente al checkout
        $response = $this->get(route('carrito.confirmar'));

        $response->assertStatus(200);
        $response->assertViewIs('pedidos.confirmar');
        $response->assertSee('Maria Lopez');
        $response->assertSee('1.200');
    }

    /**
     * RF Flujo Checkout: No se permite confirmar pedido si el carrito está vacío.
     */
    public function test_no_permite_confirmar_pedido_si_carrito_esta_vacio(): void
    {
        $user = User::create([
            'name' => 'Carlos Gomez',
            'email' => 'carlos@test.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        // Caso autenticado con carrito vacío
        $this->actingAs($user);
        $responseAuth = $this->get(route('carrito.confirmar'));
        $responseAuth->assertRedirect(route('carrito.index'));
        $responseAuth->assertSessionHas('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');

        // Caso invitado con carrito vacío
        auth()->logout();
        $responseGuest = $this->get(route('carrito.confirmar'));
        $responseGuest->assertRedirect(route('carrito.index'));
        $responseGuest->assertSessionHas('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');
    }

    /**
     * RF Flujo Checkout: El botón en la vista del carrito contiene el enlace a la ruta de confirmación.
     */
    public function test_boton_en_vista_carrito_enlaza_a_ruta_confirmar(): void
    {
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacionUnidad->id,
            'cantidad' => 1,
        ]);

        $response = $this->get(route('carrito.index'));
        $response->assertStatus(200);
        $response->assertSee(route('carrito.confirmar'));
        $response->assertSee('Iniciar Pedido / Confirmar');
    }
}
