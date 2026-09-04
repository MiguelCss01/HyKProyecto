<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\PresentacionProducto;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías
        $catAlimentos = Categoria::create(['nombre_categoria' => 'Alimentos']);
        $catBebidas = Categoria::create(['nombre_categoria' => 'Bebidas']);
        $catLimpieza = Categoria::create(['nombre_categoria' => 'Limpieza']);

        // Productos y Presentaciones
        // Producto 1
        $prod1 = Producto::create([
            'categoria_id' => $catBebidas->id,
            'nombre_producto' => 'Marca Líder - Gaseosa Cola 2.25L Retornable',
            'descripcion_producto' => 'Gaseosa sabor cola primera marca',
            'imagen_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAs-26PXYnZ1ThmoKy5WYQpgI380X3IwmFmdxTtDqHkSJT1CTyHm8RwhELtYNhxHYcPko7W7A8YHJPvC0aT6f1Ti2fPeONZzOLMAEd719KykLPNA_b_aY2ZSzB-DeA_A9z2wjlYWGf6gJkUVMw--IGcTzx5Vt390yuKasLMRFUtIiTsB0VHJOo2U3GgYgND2HCTtmXs8DZ9alRcX8Xn53L9seFwWOZV3dH7YfHihrcZ0cqvX8DTIIYk',
            'stock_actual' => 500
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod1->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 1200,
            'precio_mayorista' => 1150
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod1->id,
            'tipo' => 'pack',
            'cantidad_contenida' => 6,
            'precio_minorista' => 7000,
            'precio_mayorista' => 5700
        ]);

        // Producto 2
        $prod2 = Producto::create([
            'categoria_id' => $catAlimentos->id,
            'nombre_producto' => 'Dulce Vida - Galletitas Surtidas Familiares 400g',
            'descripcion_producto' => 'Surtido de galletitas dulces',
            'imagen_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDrSqr-ChD3XbBhFeU18sE7ICaWyttqxg3_s_LR8IBYW4B4IQkqpqGbyBv1ypNkmSY__LY1VEGlksj5jm8Mo6W_nPs0KvBzycora5yes_iYR5VuP4173GO1m-08ktRTktzBW4-b1F-ui3Li-buLhF1HjmGeg6UOlJXgANZwJIM-Uw4ssFLii6qMLymLC3xjONOOCOwdizZX8fQDZvJL_kkfQrihA1aSfediJF1kR27BO08ivB4QUdQt',
            'stock_actual' => 1200
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod2->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 850,
            'precio_mayorista' => 800
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod2->id,
            'tipo' => 'bulto',
            'cantidad_contenida' => 24,
            'precio_minorista' => 20000,
            'precio_mayorista' => 14880
        ]);

        // Producto 3
        $prod3 = Producto::create([
            'categoria_id' => $catLimpieza->id,
            'nombre_producto' => 'LimpioPlus - Limpiador Pisos Limón 1 Litro',
            'descripcion_producto' => 'Líquido limpiador para todo tipo de pisos',
            'imagen_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAcAS8aXLGqmo7SD4RilCgYivn1iPhnryinln2Kr8UzBkF-SO2KsOLAalQgTcRZKLGpW3QK3bIx3g2WRYF2lmzUGdhdkfnVKbrs1woEbkQ7VFwd1LCohxDEWDDPa6OSDdX-kXg_QRPFI1nx2ysuQFR7s9UdQKRKmNMQzoeE-l4hg_vuPfy5CWAOUsfNiHIbFjUGc9mffnGRAv9NXHBbsG0LfJ55fWU3HmROUwfH4D4jGTfcgKiT_55w',
            'stock_actual' => 50
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod3->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 900,
            'precio_mayorista' => 850
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod3->id,
            'tipo' => 'caja',
            'cantidad_contenida' => 12,
            'precio_minorista' => 10000,
            'precio_mayorista' => 8520
        ]);
        
        // Producto 4
        $prod4 = Producto::create([
            'categoria_id' => $catAlimentos->id,
            'nombre_producto' => 'Arcor - Chocolate Taza Águila Clásico 150g',
            'descripcion_producto' => 'Chocolate para taza',
            'imagen_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA0OonlGnEk_rgFJXJuwi6qXHQBAHt_I79UV1_ypBEBa1oSebsx5IBhUAGQDgXsgfvW3lknwCCK7AUf7Afd5jyfcoSgDwwAB56T2RaFh-DGCIE74UhvLA3UXbXjphpNIDj9aFrRA78YCAa1ow2GbW6JV_3tJlNTe6fHOgUWlNLfZ1r-ZP5zxhkHdK88LdPWRu4y1NKUXEDtS0hRV4SEBIna5EL1crfk-QADEGf7UdIaTCCw9ximvErC',
            'stock_actual' => 300
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod4->id,
            'tipo' => 'unidad',
            'cantidad_contenida' => 1,
            'precio_minorista' => 1250,
            'precio_mayorista' => 1200
        ]);
        PresentacionProducto::create([
            'producto_id' => $prod4->id,
            'tipo' => 'caja',
            'cantidad_contenida' => 12,
            'precio_minorista' => 14000,
            'precio_mayorista' => 11400
        ]);
    }
}
