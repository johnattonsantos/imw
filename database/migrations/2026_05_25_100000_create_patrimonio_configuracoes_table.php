<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrimonio_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igreja_id')
                ->nullable()
                ->comment('Se o sistema usar unidade_id, ajustar esta coluna antes de subir em produção.')
                ->constrained('instituicoes_instituicoes');

            $table->string('tipo', 40);
            $table->string('nome', 180);
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['igreja_id', 'tipo'], 'patrimonio_config_igreja_tipo_idx');
            $table->unique(['igreja_id', 'tipo', 'nome'], 'patrimonio_config_unique_nome_por_tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrimonio_configuracoes');
    }
};
