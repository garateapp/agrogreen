<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario_movimiento_detalles', function (Blueprint $table) {
            $table->foreignUuid('lote_id')->nullable()->constrained('lotes')->nullOnDelete()->after('producto_id');
            $table->index('lote_id');
        });
    }

    public function down(): void
    {
        Schema::table('inventario_movimiento_detalles', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropIndex(['lote_id']);
            $table->dropColumn('lote_id');
        });
    }
};
