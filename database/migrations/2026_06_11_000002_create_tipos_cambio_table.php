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
        Schema::create('tipos_cambio', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('moneda_origen');
            $table->string('moneda_destino');
            $table->decimal('factor_conversion', 12, 4);
            $table->date('fecha')->index();
            $table->timestamps();
            $table->softDeletes();

            // Unique compound index for currency conversion rates by tenant and date
            $table->unique(
                ['tenant_id', 'moneda_origen', 'moneda_destino', 'fecha'],
                'tipos_cambio_tenant_moneda_fecha_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_cambio');
    }
};
