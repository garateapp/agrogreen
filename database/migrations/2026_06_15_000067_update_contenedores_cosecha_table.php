<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contenedores_cosecha', function (Blueprint $table) {
            $table->foreignUuid('especie_id')
                ->nullable()
                ->constrained('especies')
                ->onDelete('set null')
                ->after('tenant_id');

            $table->integer('unidades_por_bin')->nullable()->after('nombre');
            $table->decimal('peso_bin_kg', 8, 2)->nullable()->after('unidades_por_bin');

            $table->dropColumn('peso_tara_estandar');
        });
    }

    public function down(): void
    {
        Schema::table('contenedores_cosecha', function (Blueprint $table) {
            $table->decimal('peso_tara_estandar', 6, 3)->after('nombre');
            $table->dropColumn(['especie_id', 'unidades_por_bin', 'peso_bin_kg']);
        });
    }
};
