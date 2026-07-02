<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('egreso_id')->constrained('egresos')->onDelete('cascade');
            $table->date('fecha_pago');
            $table->decimal('monto_moneda_base', 14, 2);
            $table->enum('metodo_pago', ['transferencia', 'cheque', 'efectivo']);
            $table->string('cuenta_bancaria_origen')->nullable();
            $table->timestamps();

            $table->index(['egreso_id', 'fecha_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
