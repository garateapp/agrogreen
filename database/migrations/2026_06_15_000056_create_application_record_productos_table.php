<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_record_productos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_record_id')->constrained('application_records')->cascadeOnDelete();
            $table->foreignUuid('producto_sag_id')->constrained('productos_sag')->restrictOnDelete();
            $table->foreignUuid('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->decimal('dosis', 10, 4);
            $table->string('unidad_dosis', 20);
            $table->decimal('cantidad_total', 12, 4);
            $table->decimal('volumen_agua', 10, 2)->nullable();
            $table->json('label_snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_record_productos');
    }
};
