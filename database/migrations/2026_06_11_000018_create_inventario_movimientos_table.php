<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('bodega_origen_id')->nullable()->constrained('bodegas')->nullOnDelete();
            $table->foreignUuid('bodega_destino_id')->nullable()->constrained('bodegas')->nullOnDelete();
            $table->enum('tipo_movimiento', ['entrada_compra', 'consumo_faena', 'traspaso', 'ajuste_inventario']);
            $table->uuid('documento_referencia_id')->nullable()->index();
            $table->timestamp('fecha_movimiento')->index();
            $table->timestamps();

            $table->index(['tenant_id', 'tipo_movimiento', 'fecha_movimiento'], 'inv_mov_tid_tipo_fecha_idx');
            $table->index(['tenant_id', 'bodega_origen_id'], 'inv_mov_tid_origen_idx');
            $table->index(['tenant_id', 'bodega_destino_id'], 'inv_mov_tid_destino_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
