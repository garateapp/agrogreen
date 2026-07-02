<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faena_registro_cuartel', function (Blueprint $table) {
            $table->uuid('faena_registro_id');
            $table->uuid('cuartel_id');
            $table->timestamps();

            $table->primary(['faena_registro_id', 'cuartel_id']);
            $table->foreign('faena_registro_id')
                ->references('id')->on('faenas_registro')
                ->cascadeOnDelete();
            $table->foreign('cuartel_id')
                ->references('id')->on('cuartels')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faena_registro_cuartel');
    }
};
