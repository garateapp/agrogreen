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
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('razon_social');
            $table->string('rut')->index();
            $table->enum('moneda_base', ['CLP', 'USD', 'MXN', 'PEN', 'COP', 'ARS', 'BOB']);
            $table->enum('status', ['activo', 'suspendido_pago']);
            $table->timestamps();
            $table->softDeletes();

            // Compound index for filtering active/suspended tenants within creation date ranges
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
