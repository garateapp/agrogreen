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
        Schema::table('presupuesto_detalles', function (Blueprint $table) {
            $table->integer('anho_fiscal')->nullable()->after('presupuesto_id');
            $table->integer('mes')->nullable()->after('anho_fiscal');
        });
    }

    public function down(): void
    {
        Schema::table('presupuesto_detalles', function (Blueprint $table) {
            $table->dropColumn(['anho_fiscal', 'mes']);
        });
    }
};
