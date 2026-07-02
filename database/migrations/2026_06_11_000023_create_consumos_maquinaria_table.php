<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumos_maquinaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('uso_maquinaria_id')->constrained('uso_maquinaria')->onDelete('cascade');
            $table->foreignUuid('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('cantidad_litros', 8, 2);
            $table->decimal('costo_total_moneda_base', 12, 2);
            $table->timestamps();

            $table->index(['uso_maquinaria_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumos_maquinaria');
    }
};
