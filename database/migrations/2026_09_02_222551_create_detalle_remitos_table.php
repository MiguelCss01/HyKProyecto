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
        Schema::create('detalle_remitos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->foreignId('remito_id')->constrained('remitos')->onDelete('cascade');
            $table->foreignId('presentacion_producto_id')->constrained('presentacion_productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_remitos');
    }
};
