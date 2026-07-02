<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_aplicacion_cuarteles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('orden_aplicacion_id')->constrained('ordenes_aplicacion')->onDelete('cascade');
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['orden_aplicacion_id', 'cuartel_id'], 'oac_oap_id_cue_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_aplicacion_cuarteles');
    }
};
