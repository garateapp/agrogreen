<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_weather_conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_record_id')->unique()->constrained('application_records')->cascadeOnDelete();
            $table->decimal('temperatura', 5, 1)->nullable();
            $table->decimal('humedad', 5, 1)->nullable();
            $table->decimal('viento_velocidad', 5, 1)->nullable();
            $table->string('viento_direccion', 20)->nullable();
            $table->string('estado_general', 100)->nullable();
            $table->string('riesgo_deriva', 10)->nullable();
            $table->string('fuente', 20)->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_weather_conditions');
    }
};
