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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('rut');
            $table->string('razon_social');
            $table->string('clasificacion');
            $table->string('contacto_email')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Compound index for optimizing provider RUT lookup within each tenant
            $table->index(['tenant_id', 'rut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
