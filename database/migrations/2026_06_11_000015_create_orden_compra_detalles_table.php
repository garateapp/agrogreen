<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_compra_detalles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('orden_compra_id')->constrained('ordenes_compra')->onDelete('cascade');
            $table->foreignUuid('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('cantidad', 12, 4);
            $table->decimal('precio_unitario_moneda_origen', 14, 4);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->onDelete('restrict');
            $table->timestamps();

            $table->index(['orden_compra_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_compra_detalles');
    }
};
