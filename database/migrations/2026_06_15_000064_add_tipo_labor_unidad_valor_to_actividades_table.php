<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->string('tipo_labor', 20)->default('dia')->after('nombre');
            $table->foreignUuid('unidad_medida_id')->nullable()->after('tipo_labor')->constrained('unidades')->nullOnDelete();
            $table->decimal('valor', 10, 2)->nullable()->after('unidad_medida_id');
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['unidad_medida_id']);
            $table->dropColumn(['tipo_labor', 'unidad_medida_id', 'valor']);
        });
    }
};
