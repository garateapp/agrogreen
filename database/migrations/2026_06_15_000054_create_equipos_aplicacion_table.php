<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_aplicacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre', 255);
            $table->string('tipo', 30);
            $table->date('ultima_calibracion')->nullable();
            $table->date('proxima_calibracion')->nullable();
            $table->date('ultima_mantencion')->nullable();
            $table->date('proxima_mantencion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_aplicacion');
    }
};
