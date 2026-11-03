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
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected Categoria $categoriaBebidas;

    protected Producto $productoGaseosa;

    protected PresentacionProducto $presUnidad;

    protected PresentacionProducto $presPack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoriaBebidas = Categoria::create([
            'nombre_categoria' => 'Bebidas',
        ]);

        $this->productoGaseosa = Producto::create([
            'categoria_id' => $this->categoriaBebidas->id,
            'nombre_producto' => 'Marca Líder - Gaseosa Cola 2.25L',
            'descripcion_producto' => 'Gaseosa sabor cola retornable',
            'imagen_url' => 'https://example.com/gaseosa.jpg',
            'stock_actual' => 48,
            'stock_minimo' => 6,
            'activo' => true,
        ]);

        $this->presUnidad = PresentacionProducto::create([
            'producto_id' => $this->productoGaseosa->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 1200,
            'precio_mayorista' => 1150,
        ]);

        $this->presPack = PresentacionProducto::create([
            'producto_id' => $this->productoGaseosa->id,
            'tipo' => 'pack',
            'cantidad_contenida' => 6,
            'precio_minorista' => 7000,
            'precio_mayorista' => 5700,
        ]);
    }

    /**
     * RF 5.1: El endpoint de historial devuelve mensaje de bienvenida y sugerencias.
     */
    public function test_puede_obtener_historial_inicial_del_chatbot(): void
    {
        $response = $this->getJson(route('chatbot.history'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages',
                'suggestions',
                'is_authenticated',
                'tipo_cliente',
            ]);

        $this->assertFalse($response->json('is_authenticated'));
        $this->assertEquals('minorista', $response->json('tipo_cliente'));
    }

    /**
     * RF 5.1 & RF 1.2: El asistente devuelve precios diferenciados según si el usuario es minorista o mayorista.
     */
    public function test_chatbot_devuelve_precios_diferenciados_minorista_y_mayorista(): void
    {
        config(['services.gemini.api_key' => '']);

        // 1. Consulta como invitado / minorista
        $resMinorista = $this->postJson(route('chatbot.send'), [
            'message' => 'Cuánto sale la gaseosa cola',
        ]);

        $resMinorista->assertStatus(200);
        $productosMin = $resMinorista->json('products');
        $this->assertNotEmpty($productosMin);
        $this->assertEquals('Minorista', $productosMin[0]['tipo_precio']);
        $this->assertEquals(1200, $productosMin[0]['presentacion_default']['precio']);

        // 2. Consulta como cliente Mayorista autenticado
        $mayorista = User::create([
            'name' => 'Distribuidora Formosa',
            'email' => 'mayorista@hyk.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'mayorista',
            'role' => 'cliente',
        ]);

        $this->actingAs($mayorista);

        $resMayorista = $this->postJson(route('chatbot.send'), [
            'message' => 'Cuánto sale la gaseosa cola',
        ]);

        $resMayorista->assertStatus(200);
        $productosMay = $resMayorista->json('products');
        $this->assertNotEmpty($productosMay);
        $this->assertEquals('Mayorista', $productosMay[0]['tipo_precio']);
        // Para mayorista sugiere por defecto el pack a precio mayorista ($5700)
        $this->assertEquals('Pack', $productosMay[0]['presentacion_default']['tipo']);
        $this->assertEquals(5700, $productosMay[0]['presentacion_default']['precio']);
    }

    /**
     * RF 5.1 & RF 2.3 & RNF 3: El cliente autenticado puede consultar el estado de su pedido, pero nunca el de otro usuario.
     */
    public function test_cliente_puede_consultar_estado_de_su_pedido_pero_no_ajeno(): void
    {
        config(['services.gemini.api_key' => '']);

        $clientePropio = User::create([
            'name' => 'Miguel Casasola',
            'email' => 'miguel@hyk.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $otroCliente = User::create([
            'name' => 'Otro Usuario',
            'email' => 'otro@hyk.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $pedidoPropio = Pedido::create([
            'user_id' => $clientePropio->id,
            'fecha_pedido' => now()->toDateString(),
            'total' => 7000,
            'estado' => 'PAGADO',
        ]);

        DetallePedido::create([
            'pedido_id' => $pedidoPropio->id,
            'presentacion_producto_id' => $this->presPack->id,
            'cantidad' => 1,
            'precio_unitario' => 7000,
        ]);

        $pedidoAjeno = Pedido::create([
            'user_id' => $otroCliente->id,
            'fecha_pedido' => now()->toDateString(),
            'total' => 24000,
            'estado' => 'PENDIENTE',
        ]);

        $this->actingAs($clientePropio);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Cuál es el estado de mi último pedido',
        ]);

        $response->assertStatus(200);
        $orders = $response->json('orders');
        $this->assertCount(1, $orders);
        $this->assertEquals($pedidoPropio->id, $orders[0]['id']);
        $this->assertEquals('PAGADO', $orders[0]['estado']);
        $this->assertNotEquals($pedidoAjeno->id, $orders[0]['id']);
    }

    /**
     * RF 5.1: Integración con la API de Google Gemini cuando GEMINI_API_KEY está configurada.
     */
    public function test_chatbot_consulta_api_de_google_gemini_cuando_hay_api_key(): void
    {
        config([
            'services.gemini.api_key' => 'fake-gemini-key-123',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Hola, tenemos la Gaseosa Cola 2.25L en stock tanto por unidad como por pack de 6 unidades.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Tenés gaseosa cola?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'source' => 'gemini',
                'reply' => 'Hola, tenemos la Gaseosa Cola 2.25L en stock tanto por unidad como por pack de 6 unidades.',
            ]);

        $this->assertNotEmpty($response->json('products'));
    }

    /**
     * RF 3.1 & RF 5.1: Permite agregar un producto recomendado al carrito directamente desde el chat.
     */
    public function test_puede_agregar_producto_al_carrito_desde_el_chatbot(): void
    {
        $response = $this->postJson(route('chatbot.add-cart'), [
            'presentacion_id' => $this->presPack->id,
            'cantidad' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cart_count' => 2,
            ]);

        $this->assertEquals(2, session('carrito')[$this->presPack->id]['cantidad']);
    }

    /**
     * Permite limpiar el historial de la conversación en sesión.
     */
    public function test_puede_limpiar_la_sesion_del_chatbot(): void
    {
        $this->postJson(route('chatbot.send'), [
            'message' => 'Hola',
        ]);

        $this->assertNotEmpty(session('chatbot_messages'));

        $response = $this->postJson(route('chatbot.clear'));
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEmpty(session('chatbot_messages', []));
    }
}
