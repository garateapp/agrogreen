<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labores', function (Blueprint $table) {
            $table->date('fecha_fin_estimada')->nullable()->after('fecha_ejecucion');
            $table->integer('avance')->default(0)->after('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('labores', function (Blueprint $table) {
            $table->dropColumn(['fecha_fin_estimada', 'avance']);
        });
    }
};
