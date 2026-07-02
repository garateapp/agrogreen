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
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade')->after('id');
            $table->index('tenant_id');
        });

        Schema::table('consumos_maquinaria', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade')->after('id');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('uso_maquinaria', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('consumos_maquinaria', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
