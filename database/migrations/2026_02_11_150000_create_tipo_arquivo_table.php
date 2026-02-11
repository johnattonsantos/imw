<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_arquivo', function (Blueprint $table) {
            $table->id();
            $table->string('extensao', 10);
            $table->timestamps();

            $table->unique('extensao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_arquivo');
    }
};
