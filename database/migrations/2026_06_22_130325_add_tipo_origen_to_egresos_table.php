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
        Schema::table('egresos', function (Blueprint $table) {
            $table->enum('tipo_origen', ['oc', 'directo'])->default('oc')->after('orden_compra_id');
            $table->foreignUuid('proveedor_id')->nullable()->after('tipo_origen')
                ->constrained('proveedores')->nullOnDelete();
            $table->foreignUuid('centro_costo_id')->nullable()->after('proveedor_id')
                ->constrained('centros_costo')->nullOnDelete();
            $table->foreignUuid('item_gasto_id')->nullable()->after('centro_costo_id')
                ->constrained('items_gasto')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('egresos', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
            $table->dropForeign(['centro_costo_id']);
            $table->dropForeign(['item_gasto_id']);
            $table->dropColumn(['tipo_origen', 'proveedor_id', 'centro_costo_id', 'item_gasto_id']);
        });
    }
};
