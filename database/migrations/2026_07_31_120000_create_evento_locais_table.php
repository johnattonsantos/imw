<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_locais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes')->cascadeOnDelete();
            $table->string('nome', 180);
            $table->string('endereco', 180)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['regiao_id', 'ativo'], 'evento_locais_regiao_ativo_index');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->foreignId('evento_local_id')
                ->nullable()
                ->after('instituicao_id')
                ->constrained('evento_locais')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropForeign(['evento_local_id']);
            $table->dropColumn('evento_local_id');
        });

        Schema::dropIfExists('evento_locais');
    }
};
