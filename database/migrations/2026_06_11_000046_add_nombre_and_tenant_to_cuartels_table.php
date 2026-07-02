<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            if (!Schema::hasColumn('cuartels', 'nombre')) {
                $table->string('nombre')->after('id');
            }
            if (!Schema::hasColumn('cuartels', 'tenant_id')) {
                $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade')->after('nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cuartels', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'tenant_id']);
        });
    }
};
