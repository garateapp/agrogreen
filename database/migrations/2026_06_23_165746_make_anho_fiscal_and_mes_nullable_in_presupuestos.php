<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->integer('anho_fiscal')->nullable()->change();
            $table->integer('mes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->integer('anho_fiscal')->nullable(false)->change();
            $table->integer('mes')->nullable(false)->change();
        });
    }
};
