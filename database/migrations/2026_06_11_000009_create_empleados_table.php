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
        Schema::create('empleados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('rut');
            $table->string('nombre');
            $table->string('apellido');
            $table->enum('tipo_contrato', ['planta', 'contratista', 'temporero']);
            $table->decimal('valor_dia_base', 12, 2);
            $table->decimal('valor_hora_extra', 12, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Compound index for filtering active employees by contract type for each tenant
            $table->index(['tenant_id', 'tipo_contrato', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
