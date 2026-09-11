<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicacao_chat_conversas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);
            $table->string('status', 20)->default('ativa');
            $table->foreignId('remetente_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('remetente_pessoa_id')->constrained('pessoas_pessoas')->cascadeOnDelete();
            $table->foreignId('destinatario_pessoa_id')->nullable()->constrained('pessoas_pessoas')->nullOnDelete();
            $table->foreignId('igreja_id')->nullable()->constrained('instituicoes_instituicoes')->nullOnDelete();
            $table->foreignId('distrito_id')->nullable()->constrained('instituicoes_instituicoes')->nullOnDelete();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->string('titulo', 180)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['regiao_id', 'tipo'], 'com_chat_conversas_regiao_tipo_index');
            $table->index(['distrito_id', 'tipo'], 'com_chat_conversas_distrito_tipo_index');
            $table->index(['remetente_pessoa_id'], 'com_chat_conversas_remetente_index');
        });

        Schema::create('comunicacao_chat_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversa_id')->constrained('comunicacao_chat_conversas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas_pessoas')->cascadeOnDelete();
            $table->foreignId('igreja_id')->nullable()->constrained('instituicoes_instituicoes')->nullOnDelete();
            $table->foreignId('distrito_id')->nullable()->constrained('instituicoes_instituicoes')->nullOnDelete();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->timestamp('lido_em')->nullable();
            $table->timestamps();

            $table->unique(['conversa_id', 'pessoa_id'], 'com_chat_participante_unique');
            $table->index(['pessoa_id', 'lido_em'], 'com_chat_participantes_pessoa_lido_index');
        });

        Schema::create('comunicacao_chat_mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversa_id')->constrained('comunicacao_chat_conversas')->cascadeOnDelete();
            $table->foreignId('remetente_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('remetente_pessoa_id')->constrained('pessoas_pessoas')->cascadeOnDelete();
            $table->text('conteudo');
            $table->string('status_entrega', 20)->default('enviada');
            $table->timestamp('enviado_em')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['conversa_id', 'enviado_em'], 'com_chat_mensagens_conversa_enviado_index');
        });

        Schema::create('comunicacao_chat_mensagem_leituras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensagem_id')->constrained('comunicacao_chat_mensagens')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas_pessoas')->cascadeOnDelete();
            $table->timestamp('lido_em')->nullable();
            $table->timestamps();

            $table->unique(['mensagem_id', 'pessoa_id'], 'com_chat_mensagem_leitura_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicacao_chat_mensagem_leituras');
        Schema::dropIfExists('comunicacao_chat_mensagens');
        Schema::dropIfExists('comunicacao_chat_participantes');
        Schema::dropIfExists('comunicacao_chat_conversas');
    }
};
