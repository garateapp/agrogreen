<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iot_lecturas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('sensor_id');
            $table->enum('tipo_sensor', ['humedad_suelo_10cm', 'humedad_suelo_30cm', 'humedad_suelo_60cm', 'temperatura_aire', 'precipitacion']);
            $table->decimal('valor', 8, 2);
            $table->timestamp('timestamp');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'sensor_id', 'timestamp'], 'iot_series_temporal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_lecturas');
    }
};
