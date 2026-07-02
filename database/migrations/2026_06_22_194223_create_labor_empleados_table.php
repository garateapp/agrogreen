<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labor_empleados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('labor_id')->constrained('labores')->cascadeOnDelete();
            $table->foreignUuid('empleado_id')->constrained('empleados')->restrictOnDelete();
            $table->decimal('horas_trabajadas', 4, 2)->default(0);
            $table->decimal('cantidad_unidades_producidas', 10, 2)->nullable();
            $table->decimal('valor_trato_unitario', 12, 2)->nullable();
            $table->decimal('monto_bono', 12, 2)->default(0);
            $table->decimal('liquido_a_pagar', 12, 2);
            $table->string('sync_id')->nullable()->index();
            $table->enum('sync_status', ['pendiente', 'sincronizado', 'error'])->default('sincronizado');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['labor_id', 'empleado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labor_empleados');
    }
};
