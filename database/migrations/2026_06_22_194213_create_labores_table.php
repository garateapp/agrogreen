<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('actividad_id')->constrained('actividades')->restrictOnDelete();
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->restrictOnDelete();
            $table->foreignUuid('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('plantilla_id')->nullable()->constrained('labores')->nullOnDelete();

            $table->enum('estado', ['programada', 'en_curso', 'en_pausa', 'completada', 'realizada', 'cancelada'])->default('programada');
            $table->date('fecha_programada')->index();
            $table->date('fecha_ejecucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('requiere_empleados')->default(true);
            $table->boolean('es_ciclica')->default(false);
            $table->enum('frecuencia', ['none', 'diaria', 'semanal', 'quincenal', 'mensual'])->default('none');
            $table->date('fecha_fin_ciclo')->nullable();
            $table->dateTime('inicio_real')->nullable();
            $table->dateTime('fin_real')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'fecha_programada', 'estado']);
            $table->index(['tenant_id', 'plantilla_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labores');
    }
};
