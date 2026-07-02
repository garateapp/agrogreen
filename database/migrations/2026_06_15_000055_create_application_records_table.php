<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('codigo', 50)->unique();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->restrictOnDelete();
            $table->foreignUuid('variedad_id')->nullable()->constrained('variedades')->nullOnDelete();
            $table->string('temporada', 50)->nullable();
            $table->decimal('superficie', 10, 2);
            $table->date('fecha_aplicacion');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_termino')->nullable();
            $table->string('estado', 20)->default('borrador');
            $table->string('objetivo_tipo', 30);
            $table->string('objetivo_nombre', 255)->nullable();
            $table->foreignUuid('responsable_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('aplicador_id')->nullable()->constrained('aplicadores')->nullOnDelete();
            $table->foreignUuid('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('equipo_id')->nullable()->constrained('equipos_aplicacion')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->foreignUuid('creado_por')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('aprobado_en')->nullable();
            $table->foreignUuid('anulado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'fecha_aplicacion']);
            $table->index(['tenant_id', 'estado']);
            $table->index(['tenant_id', 'cuartel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_records');
    }
};
