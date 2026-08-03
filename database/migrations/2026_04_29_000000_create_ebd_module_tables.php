<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebd_liderancas', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('membro_id')->constrained('membresia_membros');
            $table->enum('cargo', ['superintendente', 'secretario', 'tesoureiro']);
            $table->boolean('ativo')->default(true);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });

        Schema::create('ebd_professores', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('membro_id')->constrained('membresia_membros');
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('ebd_alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('membro_id')->constrained('membresia_membros');
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('ebd_classes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120);
            $table->string('faixa_etaria', 120)->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('ebd_turmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('ebd_classes');
            $table->foreignId('professor_id')->constrained('ebd_professores');
            $table->string('nome', 120);
            $table->unsignedSmallInteger('ano');
            $table->unsignedTinyInteger('semestre')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('ebd_turma_alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('ebd_turmas');
            $table->foreignId('aluno_id')->constrained('ebd_alunos');
            $table->date('data_entrada');
            $table->date('data_saida')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->index(['turma_id', 'aluno_id', 'ativo']);
        });

        Schema::create('ebd_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('ebd_turmas');
            $table->date('data_aula');
            $table->string('tema_aula', 160);
            $table->text('conteudo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('ebd_diario_presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diario_id')->constrained('ebd_diarios');
            $table->foreignId('aluno_id')->constrained('ebd_alunos');
            $table->boolean('presente')->default(false);
            $table->text('justificativa')->nullable();
            $table->timestamps();
            $table->unique(['diario_id', 'aluno_id']);
        });

        Schema::create('ebd_agendas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 160);
            $table->text('descricao')->nullable();
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->foreignId('turma_id')->nullable()->constrained('ebd_turmas');
            $table->string('local', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebd_agendas');
        Schema::dropIfExists('ebd_diario_presencas');
        Schema::dropIfExists('ebd_diarios');
        Schema::dropIfExists('ebd_turma_alunos');
        Schema::dropIfExists('ebd_turmas');
        Schema::dropIfExists('ebd_classes');
        Schema::dropIfExists('ebd_alunos');
        Schema::dropIfExists('ebd_professores');
        Schema::dropIfExists('ebd_liderancas');
    }
};
