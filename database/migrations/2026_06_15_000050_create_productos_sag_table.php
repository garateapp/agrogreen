<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_sag', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('producto_id')->unique()->constrained('productos')->restrictOnDelete();
            $table->foreignUuid('clasificacion_agroquimico_id')->nullable()->constrained('clasificacion_agroquimicos')->nullOnDelete();
            $table->string('nro_autorizacion_sag', 50)->unique();
            $table->string('nombre_comercial', 255);
            $table->string('ingrediente_activo', 255);
            $table->string('titular', 255)->nullable();
            $table->string('estado_sag', 20)->default('autorizado');
            $table->string('toxicidad_abejas', 20)->nullable();
            $table->string('url_etiqueta', 500)->nullable();
            $table->string('url_hds', 500)->nullable();
            $table->date('ultima_actualizacion_sag')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'estado_sag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_sag');
    }
};
