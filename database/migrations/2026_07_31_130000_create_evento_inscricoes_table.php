<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->string('origem', 20);
            $table->uuid('membro_id')->nullable();
            $table->foreignId('pessoa_id')->nullable()->constrained('pessoas_pessoas')->nullOnDelete();
            $table->string('cpf', 11);
            $table->string('nome', 150);
            $table->string('funcao_eclesiastica', 150)->nullable();
            $table->foreignId('igreja_id')->nullable()->constrained('instituicoes_instituicoes')->nullOnDelete();
            $table->string('igreja_nome', 180)->nullable();
            $table->string('telefone', 60)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['evento_id', 'cpf'], 'evento_inscricoes_evento_cpf_unique');
            $table->index(['evento_id', 'origem'], 'evento_inscricoes_evento_origem_index');
            $table->foreign('membro_id')->references('id')->on('membresia_membros')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_inscricoes');
    }
};
