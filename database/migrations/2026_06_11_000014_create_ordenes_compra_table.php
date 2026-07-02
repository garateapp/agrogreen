<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->integer('numero_oc');
            $table->foreignUuid('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->date('fecha_emision');
            $table->date('fecha_entrega')->nullable();
            $table->string('moneda');
            $table->decimal('tipo_cambio_oc', 12, 4)->default(1.0000);
            $table->enum('estado', ['borrador', 'pendiente_aprobacion', 'aprobado', 'rechazado', 'recibido']);
            $table->decimal('total_neto', 14, 2);
            $table->decimal('iva', 14, 2);
            $table->decimal('total', 14, 2);
            $table->foreignUuid('aprobado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'numero_oc']);
            $table->index(['tenant_id', 'proveedor_id', 'fecha_emision']);
            $table->index(['tenant_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
