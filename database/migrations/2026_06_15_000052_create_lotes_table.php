<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('producto_id')->constrained('productos')->restrictOnDelete();
            $table->string('codigo_lote', 100);
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_inicial', 12, 4)->default(0);
            $table->decimal('cantidad_disponible', 12, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'producto_id']);
            $table->index(['tenant_id', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
