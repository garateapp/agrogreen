<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uso_maquinaria', function (Blueprint $table) {
            $table->date('fecha')->nullable()->after('faena_registro_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('uso_maquinaria', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
            $table->dropColumn('fecha');
        });
    }
};
