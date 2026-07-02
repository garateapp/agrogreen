<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuartel_variedad', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->onDelete('cascade');
            $table->foreignUuid('variedad_id')->constrained('variedades')->onDelete('cascade');
            $table->integer('cantidad_plantas');
            $table->timestamps();

            $table->unique(['cuartel_id', 'variedad_id']);
        });

        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropForeign(['variedad_id']);
            $table->dropColumn(['variedad_id', 'cantidad_plantas']);
        });
    }

    public function down(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->foreignUuid('variedad_id')->nullable()->after('especie_id')->constrained('variedades')->nullOnDelete();
            $table->integer('cantidad_plantas')->nullable()->after('ano_plantacion');
        });

        Schema::dropIfExists('cuartel_variedad');
    }
};
