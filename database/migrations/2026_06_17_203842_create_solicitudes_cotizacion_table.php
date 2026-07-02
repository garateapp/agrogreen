<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_cotizacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->string('numero_solicitud');
            $table->date('fecha_solicitud');
            $table->text('descripcion')->nullable();
            $table->decimal('monto_estimado', 14, 2)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'numero_solicitud']);
            $table->index(['tenant_id', 'proveedor_id', 'fecha_solicitud'], 'sol_cot_tenant_prov_fecha_idx');
            $table->index(['tenant_id', 'estado'], 'sol_cot_tenant_estado_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cotizacion');
    }
};
