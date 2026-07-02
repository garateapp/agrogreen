<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto_detalles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->restrictOnDelete();
            $table->foreignUuid('actividad_id')->constrained('actividades')->restrictOnDelete();
            $table->foreignUuid('estimacion_id')->nullable()->constrained('estimaciones')->nullOnDelete();
            $table->decimal('rendimiento_promedio', 12, 2);
            $table->decimal('hectareas', 8, 2)->nullable();
            $table->integer('n_plantas')->nullable();
            $table->decimal('kilos_estimados', 14, 2)->nullable();
            $table->decimal('jh_totales', 12, 2);
            $table->decimal('valor_unitario', 14, 2);
            $table->decimal('valor_total', 14, 2);
            $table->timestamps();

            $table->index('presupuesto_id');
            $table->index(['presupuesto_id', 'cuartel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto_detalles');
    }
};
