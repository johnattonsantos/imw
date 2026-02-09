<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_comunicacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituicao_id')
                ->constrained('instituicoes_instituicoes')
                ->cascadeOnDelete();
            $table->string('nome', 150);
            $table->timestamps();

            $table->unique(['instituicao_id', 'nome'], 'categoria_comunicacao_instituicao_nome_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_comunicacao');
    }
};
