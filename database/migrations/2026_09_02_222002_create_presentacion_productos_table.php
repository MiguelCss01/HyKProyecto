<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presentacion_productos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['unidad', 'caja', 'pack', 'bulto']);
            $table->decimal('precio_mayorista', 10, 2);
            $table->decimal('precio_minorista', 10, 2);
            $table->integer('cantidad_contenida');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentacion_productos');
    }
};
