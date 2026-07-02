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
        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->dropForeign(['cuartel_id']);
            $table->dropForeign(['variedad_id']);
        });

        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
            $table->dropUnique(['cuartel_id', 'variedad_id']);
        });

        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->primary(['cuartel_id', 'variedad_id']);
            $table->foreign('cuartel_id')->references('id')->on('cuartels')->onDelete('cascade');
            $table->foreign('variedad_id')->references('id')->on('variedades')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->dropForeign(['cuartel_id']);
            $table->dropForeign(['variedad_id']);
        });

        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('cuartel_variedad', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unique(['cuartel_id', 'variedad_id']);
            $table->foreign('cuartel_id')->references('id')->on('cuartels')->onDelete('cascade');
            $table->foreign('variedad_id')->references('id')->on('variedades')->onDelete('cascade');
        });
    }
};
