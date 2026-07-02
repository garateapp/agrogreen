<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_movimiento_detalles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('movimiento_id')->constrained('inventario_movimientos')->onDelete('cascade');
            $table->foreignUuid('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('cantidad', 12, 4);
            $table->decimal('costo_unitario_moneda_base', 14, 4);
            $table->decimal('saldo_stock_anterior', 12, 4);
            $table->decimal('saldo_stock_posterior', 12, 4);
            $table->timestamps();

            $table->index(['movimiento_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimiento_detalles');
    }
};
