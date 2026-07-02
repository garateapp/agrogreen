<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_fiscales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->integer('ano');
            $table->integer('mes');
            $table->boolean('cerrado')->default(false);
            $table->foreignUuid('cerrado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'ano', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_fiscales');
    }
};
