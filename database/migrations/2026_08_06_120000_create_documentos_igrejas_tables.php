<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_igrejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regiao_id')->constrained('instituicoes_instituicoes');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('titulo', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->index('regiao_id');
        });

        Schema::create('documentos_igrejas_arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_igreja_id')->constrained('documentos_igrejas')->cascadeOnDelete();
            $table->string('nome_original', 255);
            $table->string('caminho', 500);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('documento_igreja_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_igrejas_arquivos');
        Schema::dropIfExists('documentos_igrejas');
    }
};
