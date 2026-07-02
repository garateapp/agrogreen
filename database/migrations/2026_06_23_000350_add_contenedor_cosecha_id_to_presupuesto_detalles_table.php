<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presupuesto_detalles', function (Blueprint $table) {
            $table->foreignUuid('contenedor_cosecha_id')
                ->nullable()
                ->after('estimacion_id')
                ->constrained('contenedores_cosecha')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presupuesto_detalles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contenedor_cosecha_id');
        });
    }
};
