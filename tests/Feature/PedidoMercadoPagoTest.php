<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PedidoMercadoPagoTest extends TestCase
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
            'name' => 'Juan Comprador Test',
            'email' => 'juan@comprador.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $this->categoria = Categoria::create([
            'nombre_categoria' => 'Alimentos',
        ]);

        $this->producto = Producto::create([
            'categoria_id' => $this->categoria->id,
            'nombre_producto' => 'Aceite de Girasol 1.5L',
            'descripcion_producto' => 'Aceite puro comestible',
            'imagen_url' => 'https://example.com/aceite.jpg',
            'stock_actual' => 50,
            'stock_minimo' => 5,
            'activo' => true,
        ]);

        $this->presentacion = PresentacionProducto::create([
            'producto_id' => $this->producto->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 2500,
            'precio_mayorista' => 2000,
        ]);
    }

    /**
     * Al confirmar pedido desde el carrito, se descuenta stock, se crea el pedido PENDIENTE
     * y se redirige a la pasarela de pago de Mercado Pago.
     */
    public function test_confirmar_pedido_crea_pedido_pendiente_y_redirige_a_mercado_pago(): void
    {
        $this->actingAs($this->cliente);

        // Agregar 2 unidades al carrito (Total: $5000)
        $this->post(route('carrito.agregar'), [
            'presentacion_id' => $this->presentacion->id,
            'cantidad' => 2,
        ]);

        // Confirmar pedido
        $response = $this->post(route('pedidos.confirmar'));

        $pedido = Pedido::where('user_id', $this->cliente->id)->first();
        $this->assertNotNull($pedido);
        $this->assertEquals('PENDIENTE', $pedido->estado);
        $this->assertEquals(5000, $pedido->total);

        // El stock físico debe haber sido decrementado de 50 a 48
        $this->assertEquals(48, $this->producto->fresh()->stock_actual);

        // El carrito debe haber sido vaciado
        $this->assertEmpty(session('carrito', []));

        // Redirige a la pasarela de Mercado Pago
        $response->assertRedirect(route('pedidos.pago', $pedido->id));
    }

    /**
     * La pantalla de pago de Mercado Pago muestra el pedido, el total y las credenciales de prueba.
     */
    public function test_pantalla_de_pago_mercado_pago_muestra_informacion_y_credenciales_test(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 5000,
            'estado' => 'PENDIENTE',
        ]);

        $response = $this->get(route('pedidos.pago', $pedido->id));

        $response->assertStatus(200);
        $response->assertViewIs('pedidos.mercadopago');
        $response->assertSee('mercado');
        $response->assertSee('pago');
        $response->assertSee('5.000');
        $response->assertSee('TESTUSER6236431954893547037');
        $response->assertSee('okgbUMLSGQ');
        $response->assertSee('TESTUSER2984400822875817517');
    }

    /**
     * Si un pedido ya fue pagado, al acceder a la pasarela de pago redirige a la pantalla de éxito.
     */
    public function test_si_pedido_ya_fue_pagado_redirige_a_pantalla_de_exito(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 5000,
            'estado' => 'PAGADO',
        ]);

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => 5000,
            'id_transaccion_mp' => 'MP-123456789',
            'fecha_pago' => now()->toDateString(),
        ]);

        $response = $this->get(route('pedidos.pago', $pedido->id));

        $response->assertRedirect(route('pedido.exito', $pedido->id));
        $response->assertSessionHas('info');
    }

    /**
     * El procesamiento del pago registra la transacción en la tabla pagos, actualiza el estado a PAGADO
     * y redirige a la pantalla de éxito.
     */
    public function test_procesar_pago_registra_pago_y_actualiza_estado_a_pagado(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 5000,
            'estado' => 'PENDIENTE',
        ]);

        $response = $this->post(route('pedidos.procesar-pago', $pedido->id), [
            'id_transaccion_mp' => 'MP-9988776655',
            'metodo_pago' => 'cuenta_mp',
        ]);

        $response->assertRedirect(route('pedido.exito', $pedido->id));
        $response->assertSessionHas('success');

        $this->assertEquals('PAGADO', $pedido->fresh()->estado);

        $pago = Pago::where('pedido_id', $pedido->id)->first();
        $this->assertNotNull($pago);
        $this->assertEquals('MP-9988776655', $pago->id_transaccion_mp);
        $this->assertEquals(5000, $pago->monto);
    }

    /**
     * La pantalla de éxito muestra la información del pago de Mercado Pago
     * y el aviso explícito de aviso cuando el pedido esté listo para retirar en la sucursal.
     */
    public function test_pantalla_de_exito_muestra_informacion_de_pago_y_aviso_retiro_sucursal(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 5000,
            'estado' => 'PAGADO',
        ]);

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => 5000,
            'id_transaccion_mp' => 'MP-888777666',
            'fecha_pago' => now()->toDateString(),
        ]);

        $response = $this->get(route('pedido.exito', $pedido->id));

        $response->assertStatus(200);
        $response->assertViewIs('pedidos.exito');
        $response->assertSee('¡Pedido Confirmado y Pagado con Éxito!');
        $response->assertSee('Mercado Pago');
        $response->assertSee('MP-888777666');
        $response->assertSee('Aprobado');
        $response->assertSee('¡Te avisaremos cuando tu pedido esté listo para pasar a buscar a la sucursal!');
        $response->assertSee('Sucursal Central');
        $response->assertSee('Calle Saavedra 4600');
    }

    /**
     * Un usuario no puede pagar ni ver la pasarela de pago de un pedido ajeno.
     */
    public function test_usuario_no_puede_pagar_pedido_de_otro_usuario(): void
    {
        $otroUsuario = User::create([
            'name' => 'Otro Cliente',
            'email' => 'otro@cliente.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $pedidoAjeno = Pedido::create([
            'user_id' => $otroUsuario->id,
            'fecha_pedido' => now(),
            'total' => 3000,
            'estado' => 'PENDIENTE',
        ]);

        $this->actingAs($this->cliente);

        // Al intentar ver el pago ajeno debe dar 404
        $responseGet = $this->get(route('pedidos.pago', $pedidoAjeno->id));
        $responseGet->assertStatus(404);

        // Al intentar procesar el pago ajeno debe dar 404
        $responsePost = $this->post(route('pedidos.procesar-pago', $pedidoAjeno->id), [
            'id_transaccion_mp' => 'MP-HACK-123',
        ]);
        $responsePost->assertStatus(404);
    }

    /**
     * Cuando MERCADOPAGO_ACCESS_TOKEN está configurado, la pantalla de pago genera la preferencia
     * oficial y ofrece el botón/enlace al Checkout oficial de Mercado Pago.
     */
    public function test_pantalla_de_pago_con_access_token_genera_url_oficial_de_mercado_pago(): void
    {
        config(['services.mercadopago.access_token' => 'TEST-123456789-SAMPLE-TOKEN']);
        config(['services.mercadopago.sandbox' => true]);

        Http::fake([
            'https://api.mercadopago.com/checkout/preferences' => Http::response([
                'id' => 'pref-sample-123',
                'init_point' => 'https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref-sample-123',
                'sandbox_init_point' => 'https://sandbox.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref-sample-123',
            ], 201),
        ]);

        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 2500,
            'estado' => 'PENDIENTE',
        ]);

        $response = $this->get(route('pedidos.pago', $pedido->id));

        $response->assertStatus(200);
        $response->assertViewHas('initPoint', 'https://sandbox.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref-sample-123');
        $response->assertSee('https://sandbox.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref-sample-123');
        $response->assertSee('Pagar $2.500 en Mercado Pago Oficial');
    }

    /**
     * El retorno automático desde la pasarela real de Mercado Pago con status=approved
     * acredita el pago y redirige a la pantalla de éxito.
     */
    public function test_retorno_mercado_pago_aprobado_registra_pago_y_redirige_a_exito(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 2500,
            'estado' => 'PENDIENTE',
        ]);

        $response = $this->get(route('pedidos.retorno-mp', [
            'external_reference' => $pedido->id,
            'status' => 'approved',
            'payment_id' => '1234567890',
        ]));

        $response->assertRedirect(route('pedido.exito', $pedido->id));
        $response->assertSessionHas('success');

        $this->assertEquals('PAGADO', $pedido->fresh()->estado);

        $pago = Pago::where('pedido_id', $pedido->id)->first();
        $this->assertNotNull($pago);
        $this->assertEquals('1234567890', $pago->id_transaccion_mp);
        $this->assertEquals(2500, $pago->monto);
    }

    /**
     * La pantalla de éxito contiene el enlace para ver el comprobante en PDF.
     */
    public function test_pantalla_de_exito_muestra_enlace_a_comprobante_pdf(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 2500,
            'estado' => 'PAGADO',
        ]);

        $response = $this->get(route('pedido.exito', $pedido->id));

        $response->assertStatus(200);
        $response->assertSee(route('pedidos.comprobante', $pedido->id));
        $response->assertSee('Ver Comprobante (PDF)');
    }

    /**
     * El comprobante oficial muestra toda la información del pedido, cliente y pago.
     */
    public function test_comprobante_muestra_toda_la_informacion_de_pago_y_pedido(): void
    {
        $this->actingAs($this->cliente);

        $pedido = Pedido::create([
            'user_id' => $this->cliente->id,
            'fecha_pedido' => now(),
            'total' => 5000,
            'estado' => 'PAGADO',
        ]);

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => 5000,
            'id_transaccion_mp' => 'MP-TEST-999888',
            'fecha_pago' => now(),
        ]);

        $response = $this->get(route('pedidos.comprobante', $pedido->id));

        $response->assertStatus(200);
        $response->assertSee('COMP-'.str_pad($pedido->id, 8, '0', STR_PAD_LEFT));
        $response->assertSee('MP-TEST-999888');
        $response->assertSee('$5.000');
        $response->assertSee('Descargar PDF');
        $response->assertSee('Imprimir');
    }

    /**
     * Un usuario no puede ver el comprobante de otro usuario.
     */
    public function test_usuario_no_puede_ver_comprobante_ajeno(): void
    {
        $otroUsuario = User::create([
            'name' => 'Otro Cliente',
            'email' => 'otro_comprobante@cliente.com',
            'password' => Hash::make('password123'),
            'tipo_cliente' => 'minorista',
            'role' => 'cliente',
        ]);

        $pedidoAjeno = Pedido::create([
            'user_id' => $otroUsuario->id,
            'fecha_pedido' => now(),
            'total' => 3000,
            'estado' => 'PAGADO',
        ]);

        $this->actingAs($this->cliente);

        $response = $this->get(route('pedidos.comprobante', $pedidoAjeno->id));
        $response->assertStatus(403);
    }
}
