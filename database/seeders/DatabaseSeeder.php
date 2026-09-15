<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrador de prueba
        User::factory()->create([
            'name' => 'Admin HyK',
            'email' => 'admin@hyk.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Cliente de prueba
        User::factory()->create([
            'name' => 'Cliente Test',
            'email' => 'cliente@hyk.com',
            'role' => 'cliente',
            'tipo_cliente' => 'minorista',
            'password' => bcrypt('password'),
        ]);

        // Correr el seeder del catálogo (Categorías, Productos, Presentaciones)
        $this->call([
            CatalogoSeeder::class,
        ]);
    }
}
