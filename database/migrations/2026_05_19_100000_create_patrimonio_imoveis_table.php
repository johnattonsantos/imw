<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrimonio_imoveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igreja_id')
                ->nullable()
                ->comment('Se o sistema usar unidade_id, ajustar esta coluna antes de subir em produção.')
                ->constrained('instituicoes_instituicoes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrimonio_imoveis');
    }
};
