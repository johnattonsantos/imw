<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_funcoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('nome', 'evento_funcoes_nome_unique');
        });

        Schema::table('evento_equipes', function (Blueprint $table) {
            $table->foreignId('evento_funcao_id')
                ->nullable()
                ->after('evento_id')
                ->constrained('evento_funcoes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('evento_equipes', function (Blueprint $table) {
            $table->dropForeign(['evento_funcao_id']);
            $table->dropColumn('evento_funcao_id');
        });

        Schema::dropIfExists('evento_funcoes');
    }
};
