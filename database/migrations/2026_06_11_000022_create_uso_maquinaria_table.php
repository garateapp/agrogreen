<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uso_maquinaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tractor_id')->constrained('tractores_maquinaria')->onDelete('restrict');
            $table->foreignUuid('operador_id')->constrained('empleados')->onDelete('restrict');
            $table->foreignUuid('faena_registro_id')->nullable()->constrained('faenas_registro')->nullOnDelete();
            $table->decimal('horas_inicio', 8, 2);
            $table->decimal('horas_fin', 8, 2);
            $table->decimal('horas_totales', 8, 2);
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->onDelete('restrict');
            $table->timestamps();

            $table->index(['tractor_id', 'horas_inicio']);
            $table->index(['faena_registro_id']);
            $table->index(['centro_costo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uso_maquinaria');
    }
};
