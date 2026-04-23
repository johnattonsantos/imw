<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gceu_reuniao_pessoas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gceu_cadastro_id');
            $table->unsignedBigInteger('instituicao_id');
            $table->string('nome', 150);
            $table->string('contato', 20)->nullable();
            $table->char('tipo', 1)->default('V'); // V = Visitante, N = Novo Convertido
            $table->date('data_reuniao');
            $table->timestamps();
            $table->softDeletes();

            $table->index('gceu_cadastro_id');
            $table->index('instituicao_id');
            $table->index('tipo');
            $table->index('data_reuniao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gceu_reuniao_pessoas');
    }
};
