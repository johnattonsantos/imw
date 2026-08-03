<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrimonio_riscos_juridicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igreja_id')
                ->nullable()
                ->comment('Se o sistema usar unidade_id, ajustar esta coluna antes de subir em produção.')
                ->constrained('instituicoes_instituicoes');

            $table->foreignId('imovel_id')
                ->nullable()
                ->constrained('patrimonio_imoveis')
                ->nullOnDelete();

            $table->foreignId('bem_movel_id')
                ->nullable()
                ->constrained('patrimonio_bens_moveis')
                ->nullOnDelete();

            $table->foreignId('documento_id')
                ->nullable()
                ->constrained('patrimonio_documentos')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrimonio_riscos_juridicos');
    }
};
