<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labor_cuarteles', function (Blueprint $table) {
            $table->foreignUuid('labor_id')->constrained('labores')->cascadeOnDelete();
            $table->foreignUuid('cuartel_id')->constrained('cuartels')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['labor_id', 'cuartel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labor_cuarteles');
    }
};
