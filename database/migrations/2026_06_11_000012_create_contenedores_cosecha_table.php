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
        Schema::create('contenedores_cosecha', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nombre');
            $table->decimal('peso_tara_estandar', 6, 3);
            $table->timestamps();
            $table->softDeletes();

            // Compound index for querying containers by weight tara standard range within a tenant
            $table->index(['tenant_id', 'peso_tara_estandar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenedores_cosecha');
    }
};
