<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('numero');
            $table->date('fecha_emision');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['productos', 'servicios'])->default('productos');
            $table->foreignUuid('proveedor_id')->nullable()->constrained('proveedores')->onDelete('set null');
            $table->boolean('distribuir_costos')->default(false);
            $table->boolean('descuento_linea')->default(false);
            $table->boolean('vencimiento_lote')->default(false);
            $table->json('lineas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'numero']);
            $table->index(['tenant_id', 'fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
