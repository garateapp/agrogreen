<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_safety_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_record_id')->unique()->constrained('application_records')->cascadeOnDelete();
            $table->boolean('epp_guantes')->default(false);
            $table->boolean('epp_mascarilla')->default(false);
            $table->boolean('epp_overol')->default(false);
            $table->boolean('epp_botas')->default(false);
            $table->boolean('epp_proteccion_ocular')->default(false);
            $table->boolean('senalizacion_instalada')->default(false);
            $table->boolean('agua_emergencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_safety_checks');
    }
};
