<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->enum('tipo_documento', ['factura', 'boleta', 'guia']);
            $table->string('folio_documento');
            $table->date('fecha_emision');
            $table->string('moneda', 10)->default('CLP');
            $table->decimal('tipo_cambio', 12, 4)->default(1.0000);
            $table->decimal('monto_neto', 14, 2);
            $table->decimal('iva', 14, 2);
            $table->decimal('monto_total', 14, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'cliente_id', 'fecha_emision'], 'ing_tenant_cliente_fecha_idx');
            $table->index(['tenant_id', 'estado'], 'ing_tenant_estado_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
