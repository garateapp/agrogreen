<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->string('tipo_trato')->default('monto');
            $table->string('unidad_medida')->nullable();
            $table->boolean('no_agrupar_actividad')->default(false);
            $table->boolean('depende_jornada')->default(false);
            $table->boolean('sustraer_trato_base')->default(false);
            $table->boolean('bonificacion')->default(false);
            $table->boolean('hora_extra')->default(false);
            $table->boolean('no_enviar_integraciones')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratos');
    }
};
