<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    protected Categoria $categoriaBebidas;

    protected Categoria $categoriaAlimentos;

    protected Producto $productoActivo;

    protected Producto $productoInactivo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoriaBebidas = Categoria::create([
            'nombre_categoria' => 'Bebidas',
        ]);

        $this->categoriaAlimentos = Categoria::create([
            'nombre_categoria' => 'Alimentos',
        ]);

        $this->productoActivo = Producto::create([
            'categoria_id' => $this->categoriaBebidas->id,
            'nombre_producto' => 'Gaseosa Cola 2.25L Retornable',
            'descripcion_producto' => 'Bebida gaseosa sabor cola',
            'imagen_url' => 'https://example.com/gaseosa.jpg',
            'stock_actual' => 50,
            'stock_minimo' => 5,
            'activo' => true,
        ]);

        PresentacionProducto::create([
            'producto_id' => $this->productoActivo->id,
            'tipo' => 'unidad',
            'precio_minorista' => 1500,
            'precio_mayorista' => 1200,
            'cantidad_contenida' => 1,
        ]);

        $this->productoInactivo = Producto::create([
            'categoria_id' => $this->categoriaAlimentos->id,
            'nombre_producto' => 'Galletitas Descontinuadas',
            'descripcion_producto' => 'Producto fuera de circulación',
            'imagen_url' => 'https://example.com/galletitas.jpg',
            'stock_actual' => 0,
            'stock_minimo' => 0,
            'activo' => false,
        ]);
    }

    public function test_catalogo_page_loads_successfully_with_products_and_categories(): void
    {
        $response = $this->get('/catalogo');

        $response->assertStatus(200);
        $response->assertViewIs('catalogo');
        $response->assertSee('Gaseosa Cola 2.25L Retornable');
        $response->assertSee('Bebidas');
        $response->assertSee('Alimentos');
    }

    public function test_catalogo_renders_dynamic_search_and_filter_elements(): void
    {
        $response = $this->get('/catalogo');

        $response->assertStatus(200);
        // Search inputs
        $response->assertSee('id="search-input-desktop"', false);
        $response->assertSee('id="search-input-mobile"', false);
        $response->assertSee('id="search-clear-desktop"', false);
        $response->assertSee('id="search-clear-mobile"', false);

        // Category pills and sidebar elements
        $response->assertSee('id="category-pills-bar"', false);
        $response->assertSee('data-categoria-id="todas"', false);
        $response->assertSee('data-categoria-id="'.$this->categoriaBebidas->id.'"', false);

        // Product card data attributes for live JS filtering
        $response->assertSee('data-categoria-id="'.$this->categoriaBebidas->id.'"', false);
        $response->assertSee('data-nombre="'.strtolower($this->productoActivo->nombre_producto).'"', false);

        // Real-time counter and no-results feedback
        $response->assertSee('id="contador-productos"', false);
        $response->assertSee('id="sin-resultados"', false);
        $response->assertSee('id="btn-limpiar-todos-filtros"', false);

        // Mobile drawer navigation
        $response->assertSee('id="mobile-drawer"', false);
        $response->assertSee('id="bottom-nav-categorias"', false);
    }

    public function test_catalogo_only_displays_active_products(): void
    {
        $response = $this->get('/catalogo');

        $response->assertStatus(200);
        $response->assertSee('Gaseosa Cola 2.25L Retornable');
        $response->assertDontSee('Galletitas Descontinuadas');
    }

    public function test_catalogo_product_cards_have_valid_add_to_cart_form(): void
    {
        $response = $this->get('/catalogo');

        $response->assertStatus(200);
        $response->assertSee(route('carrito.agregar'), false);
        $response->assertSee('name="presentacion_id"', false);
        $response->assertSee('name="cantidad"', false);
        $response->assertSee('type="submit"', false);
    }
}
