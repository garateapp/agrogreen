<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('centros_costo', function (Blueprint $table) {
            $table->string('agrupador')->nullable()->after('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('centros_costo', function (Blueprint $table) {
            $table->dropColumn('agrupador');
        });
    }
};
