<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_arquivo_comunicacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituicao_id')
                ->constrained('instituicoes_instituicoes')
                ->cascadeOnDelete();
            $table->string('extensao', 10);
            $table->timestamps();

            $table->unique(
                ['instituicao_id', 'extensao'],
                'tipo_arquivo_comunicacao_instituicao_extensao_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_arquivo_comunicacao');
    }
};
