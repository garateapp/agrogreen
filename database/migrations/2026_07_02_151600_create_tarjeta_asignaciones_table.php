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
        Schema::create('tarjeta_asignaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tarjeta_id')->constrained('tarjetas')->onDelete('cascade');
            $table->foreignUuid('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->timestamp('fecha_asignacion');
            $table->timestamp('fecha_desasignacion')->nullable();
            $table->foreignUuid('asignado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('desasignado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_asignaciones');
    }
};
