<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->after('tenant_id');
            $table->index(['tenant_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'codigo']);
            $table->dropColumn('codigo');
        });
    }
};
