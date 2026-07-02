<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->foreignUuid('especie_id')->nullable()->after('centro_costo_id')->constrained('especies')->nullOnDelete();
            $table->foreignUuid('variedad_id')->nullable()->after('especie_id')->constrained('variedades')->nullOnDelete();
        });

        DB::statement('UPDATE cuartels c JOIN especies e ON e.nombre = c.especie_cultivo SET c.especie_id = e.id');
        DB::statement('UPDATE cuartels c JOIN variedades v ON v.nombre = c.variedad SET c.variedad_id = v.id');

        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropColumn(['especie_cultivo', 'variedad']);
        });
    }

    public function down(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->string('especie_cultivo')->nullable()->after('centro_costo_id');
            $table->string('variedad')->nullable()->after('especie_cultivo');
        });

        DB::statement('UPDATE cuartels c JOIN especies e ON e.id = c.especie_id SET c.especie_cultivo = e.nombre');
        DB::statement('UPDATE cuartels c JOIN variedades v ON v.id = c.variedad_id SET c.variedad = v.nombre');

        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropForeign(['especie_id']);
            $table->dropForeign(['variedad_id']);
            $table->dropColumn(['especie_id', 'variedad_id']);
        });
    }
};
