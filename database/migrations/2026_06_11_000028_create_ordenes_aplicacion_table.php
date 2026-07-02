<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_aplicacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->date('fecha_planificada')->index();
            $table->enum('estado', ['pendiente', 'en_proceso', 'ejecutada', 'cancelada']);
            $table->decimal('mojamiento_l_ha', 6, 2);
            $table->foreignUuid('tractor_id')->constrained('tractores_maquinaria')->onDelete('restrict');
            $table->foreignUuid('nebulizadora_id')->constrained('tractores_maquinaria')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'fecha_planificada', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_aplicacion');
    }
};
