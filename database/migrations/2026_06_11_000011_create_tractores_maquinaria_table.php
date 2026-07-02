<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tractores_maquinaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nombre');
            $table->string('patente_o_identificador');
            $table->enum('tipo', ['tractor', 'nebulizadora', 'rastra', 'vehiculo_carga']);
            $table->decimal('horas_motor_iniciales', 10, 2);
            $table->decimal('consumo_estimado_combustible_hora', 6, 2);
            $table->timestamps();
            $table->softDeletes();

            // Compound index for optimizing ranges and queries on machinery by type and motor hours per tenant
            $table->index(
                ['tenant_id', 'tipo', 'horas_motor_iniciales'],
                'tractores_maq_tenant_tipo_horas_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tractores_maquinaria');
    }
};
