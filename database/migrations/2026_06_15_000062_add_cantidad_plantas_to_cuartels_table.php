<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->integer('cantidad_plantas')->nullable()->after('ano_plantacion');
        });
    }

    public function down(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropColumn('cantidad_plantas');
        });
    }
};
