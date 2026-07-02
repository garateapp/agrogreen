<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('presupuestos');

        Schema::create('presupuestos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer('anho_fiscal');
            $table->integer('mes');
            $table->enum('estado', ['borrador', 'aprobado', 'cerrado'])->default('borrador');
            $table->decimal('tipo_cambio_usd', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'anho_fiscal', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
