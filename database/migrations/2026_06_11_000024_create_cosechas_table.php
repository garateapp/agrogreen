<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cosechas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->timestamp('fecha_hora')->index();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->onDelete('restrict');
            $table->foreignUuid('empleado_id')->constrained('empleados')->onDelete('restrict');
            $table->foreignUuid('jefe_cosecha_id')->constrained('users')->onDelete('restrict');
            $table->foreignUuid('contenedor_id')->constrained('contenedores_cosecha')->onDelete('restrict');
            $table->string('codigo_tarjeta_qr')->unique();
            $table->decimal('peso_bruto', 10, 3);
            $table->decimal('peso_tara', 10, 3);
            $table->decimal('peso_neto', 10, 3);
            $table->string('sync_id')->nullable()->index();
            $table->enum('sync_status', ['pendiente', 'sincronizado', 'error'])->default('sincronizado');
            $table->timestamps();

            $table->index(['tenant_id', 'fecha_hora', 'cuartel_id']);
            $table->index(['tenant_id', 'sync_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cosechas');
    }
};
