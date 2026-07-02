<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->integer('ano_fiscal');
            $table->integer('mes');
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->onDelete('restrict');
            $table->enum('categoria_gasto', ['mano_obra', 'insumos', 'maquinaria', 'otros']);
            $table->decimal('monto_presupuestado_moneda_base', 14, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'ano_fiscal', 'mes', 'centro_costo_id', 'categoria_gasto'], 'presupuesto_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
