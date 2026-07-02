<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_sag_usos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('producto_sag_id')->constrained('productos_sag')->cascadeOnDelete();
            $table->foreignUuid('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('objetivo', 255);
            $table->decimal('dosis_min', 10, 4);
            $table->decimal('dosis_max', 10, 4);
            $table->string('unidad_dosis', 20);
            $table->integer('carencia_dias')->default(0);
            $table->integer('reingreso_horas')->default(0);
            $table->text('restricciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_sag_usos');
    }
};
