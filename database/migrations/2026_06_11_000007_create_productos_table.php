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
        Schema::create('productos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nombre');
            $table->string('codigo_barras')->nullable();
            $table->enum('categoria', ['agroquimico', 'fertilizante', 'maquinaria_repuesto', 'combustible', 'EPP', 'otros']);
            $table->foreignUuid('unidad_medida_id')->constrained('unidades')->onDelete('restrict');
            $table->string('ingrediente_activo')->nullable();
            $table->decimal('dosis_recomendada_por_ha', 8, 2)->nullable();
            $table->integer('dias_carencia')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Compound index to optimize range queries on withholding days (dias_carencia) within product categories
            $table->index(['tenant_id', 'categoria', 'dias_carencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
