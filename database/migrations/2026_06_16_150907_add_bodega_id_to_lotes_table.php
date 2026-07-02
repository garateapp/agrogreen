<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->foreignUuid('bodega_id')->nullable()->after('tenant_id')->constrained('bodegas')->nullOnDelete();
            $table->decimal('costo_unitario', 14, 4)->default(0)->after('cantidad_disponible');
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropForeign(['bodega_id']);
            $table->dropColumn('bodega_id');
            $table->dropColumn('costo_unitario');
        });
    }
};
