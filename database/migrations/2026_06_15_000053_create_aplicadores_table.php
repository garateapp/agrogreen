<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aplicadores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombres', 255);
            $table->string('apellidos', 255);
            $table->string('rut', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->boolean('capacitado')->default(false);
            $table->string('certificado_url', 500)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'activo', 'capacitado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aplicadores');
    }
};
