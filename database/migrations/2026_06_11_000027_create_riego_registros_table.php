<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riego_registros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sector_riego_id')->constrained('sectores_riego')->onDelete('cascade');
            $table->date('fecha')->index();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->decimal('metros_cubicos_aplicados', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sector_riego_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riego_registros');
    }
};
