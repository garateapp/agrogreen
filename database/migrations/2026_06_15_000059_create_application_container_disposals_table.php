<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_container_disposals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_record_id')->constrained('application_records')->cascadeOnDelete();
            $table->foreignUuid('producto_sag_id')->constrained('productos_sag')->restrictOnDelete();
            $table->integer('envases_usados');
            $table->decimal('capacidad_envase', 10, 2)->nullable();
            $table->boolean('triple_lavado')->default(true);
            $table->string('almacenamiento_temporal', 255)->nullable();
            $table->string('metodo_disposicion', 255)->nullable();
            $table->string('documento_respaldo_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_container_disposals');
    }
};
