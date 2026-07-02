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
        Schema::create('cuartels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('centro_costo_id')->constrained('centros_costo')->onDelete('cascade');
            $table->decimal('superficie_hectareas', 8, 2);
            $table->string('especie_cultivo');
            $table->string('variedad')->nullable();
            $table->integer('ano_plantacion');
            $table->decimal('distancia_sobre_hilera', 4, 2)->default(0.00);
            $table->decimal('distancia_intra_hilera', 4, 2)->default(0.00);
            $table->json('geometria_geojson')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Compound indexes for optimization of range queries (ano_plantacion and superficie_hectareas)
            $table->index(['centro_costo_id', 'ano_plantacion']);
            $table->index(['centro_costo_id', 'superficie_hectareas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuartels');
    }
};
