<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Destructive migration: back up these four tables before running in production.
        Schema::dropIfExists('gestao_acessos_sessao_perfis');
        Schema::dropIfExists('gestao_acessos_sessoes');
        Schema::dropIfExists('gestao_acessos');
        Schema::dropIfExists('gestao_acessos_configuracao');
    }

    public function down(): void
    {
        throw new \RuntimeException('A remoção do monitoramento não é reversível por migration. Restaure o backup das tabelas.');
    }
};
