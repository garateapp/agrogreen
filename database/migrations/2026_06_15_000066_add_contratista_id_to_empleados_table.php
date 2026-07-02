<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->foreignUuid('contratista_id')
                ->nullable()
                ->constrained('contratistas')
                ->onDelete('set null')
                ->after('tipo_contrato');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['contratista_id']);
            $table->dropColumn('contratista_id');
        });
    }
};
