<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_propositos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120)->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        $now = now();
        $propositos = [
            'Evangelístico',
            'Seminário',
            'Campanha',
            'Conferência',
            'Reunião de Membros',
            'Concílio',
            'Treinamento',
            'Culto Especial',
            'Ação Social',
            'Retiro',
            'Outro',
        ];

        foreach ($propositos as $proposito) {
            DB::table('evento_propositos')->insert([
                'nome' => $proposito,
                'ativo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituicao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->foreignId('evento_proposito_id')->nullable()->constrained('evento_propositos')->nullOnDelete();
            $table->string('titulo', 180);
            $table->text('descricao')->nullable();
            $table->string('local', 180)->nullable();
            $table->date('data_inicio');
            $table->time('hora_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->time('hora_fim')->nullable();
            $table->string('status', 20)->default('planejado');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['instituicao_id', 'data_inicio'], 'eventos_instituicao_data_inicio_index');
            $table->index(['instituicao_id', 'status'], 'eventos_instituicao_status_index');
        });

        Schema::create('evento_equipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->string('nome', 150);
            $table->string('funcao', 120)->nullable();
            $table->string('contato', 60)->nullable();
            $table->boolean('lider')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['evento_id', 'lider'], 'evento_equipes_evento_lider_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_equipes');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('evento_propositos');
    }
};
