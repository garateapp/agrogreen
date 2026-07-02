<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->restrictOnDelete();
            $table->integer('anho');
            $table->string('nombre');
            $table->decimal('kilos_estimados', 14, 2);
            $table->date('fecha_estimacion');
            $table->enum('estado', ['borrador', 'confirmado'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'cuartel_id', 'anho', 'nombre'], 'estimacion_unique');
            $table->index(['tenant_id', 'cuartel_id', 'anho']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimaciones');
    }
};
