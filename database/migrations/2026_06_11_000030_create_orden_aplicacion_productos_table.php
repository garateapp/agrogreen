<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_aplicacion_productos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('orden_aplicacion_id')->constrained('ordenes_aplicacion')->onDelete('cascade');
            $table->foreignUuid('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('dosis_comercial_por_hl', 8, 4);
            $table->decimal('cantidad_total_insumo', 12, 4);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['orden_aplicacion_id', 'producto_id'], 'oap_oap_id_pro_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_aplicacion_productos');
    }
};
