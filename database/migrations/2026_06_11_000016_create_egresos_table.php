<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('orden_compra_id')->nullable()->constrained('ordenes_compra')->nullOnDelete();
            $table->enum('tipo_documento', ['factura', 'boleta', 'guia']);
            $table->string('folio_documento');
            $table->date('fecha_registro')->index();
            $table->string('moneda');
            $table->decimal('tipo_cambio_egreso', 12, 4);
            $table->decimal('monto_total_moneda_base', 14, 2);
            $table->enum('estado_pago', ['pendiente', 'abono_parcial', 'pagado']);
            $table->timestamps();

            $table->index(['tenant_id', 'orden_compra_id']);
            $table->index(['tenant_id', 'fecha_registro', 'estado_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
