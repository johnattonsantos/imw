<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juridico_regiao_advogados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->string('nome', 180);
            $table->string('tipo', 20)->default('causa');
            $table->string('registro_oab', 60)->nullable();
            $table->string('telefone', 60)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('contatos', 255)->nullable();
            $table->string('endereco_escritorio', 255)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['regiao_id', 'tipo'], 'juridico_regiao_advogados_regiao_tipo_index');
        });

        Schema::create('juridico_regiao_acoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->foreignId('instituicao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->foreignId('advogado_causa_id')->nullable()->constrained('juridico_regiao_advogados')->nullOnDelete();
            $table->foreignId('advogado_oposicao_id')->nullable()->constrained('juridico_regiao_advogados')->nullOnDelete();
            $table->string('numero_processo', 120)->nullable();
            $table->string('autor', 180);
            $table->string('reu', 180);
            $table->string('vara_tribunal', 180)->nullable();
            $table->string('advogado_oposicao_nome', 180)->nullable();
            $table->string('status', 30)->default('em_curso');
            $table->string('resultado', 30)->default('sem_sentenca');
            $table->date('data_distribuicao')->nullable();
            $table->date('data_sentenca')->nullable();
            $table->decimal('custo_demanda', 15, 2)->nullable();
            $table->text('objeto')->nullable();
            $table->text('teor_decisao')->nullable();
            $table->text('outros')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['regiao_id', 'status'], 'juridico_regiao_acoes_regiao_status_index');
            $table->index(['regiao_id', 'resultado'], 'juridico_regiao_acoes_regiao_resultado_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juridico_regiao_acoes');
        Schema::dropIfExists('juridico_regiao_advogados');
    }
};
