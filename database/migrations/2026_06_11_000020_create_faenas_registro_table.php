<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faenas_registro', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->date('fecha')->index();
            $table->foreignUuid('actividad_id')->constrained('actividades')->onDelete('restrict');
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->onDelete('restrict');
            $table->foreignUuid('supervisor_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['tenant_id', 'fecha']);
            $table->index(['tenant_id', 'centro_costo_id', 'actividad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faenas_registro');
    }
};
