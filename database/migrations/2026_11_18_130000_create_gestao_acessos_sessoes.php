<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestao_acessos_sessoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('login_em')->nullable();
            $table->dateTime('observada_em');
            $table->dateTime('logout_em')->nullable();
        });
        Schema::create('gestao_acessos_sessao_perfis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('sessao_id');
            $table->unsignedBigInteger('regiao_id');
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('instituicao_id');
            $table->string('usuario_nome');
            $table->string('perfil_nome');
            $table->string('instituicao_nome');
            $table->dateTime('primeiro_uso');
            $table->dateTime('ultimo_uso');
            $table->unique(['sessao_id', 'perfil_id', 'instituicao_id'], 'gestao_sessao_perfil_unico');
            $table->index(['regiao_id', 'ultimo_uso']);
            $table->foreign('sessao_id')->references('id')->on('gestao_acessos_sessoes');
        });
        Schema::table('gestao_acessos_configuracao', function (Blueprint $table) {
            $table->dateTime('inicio_sessoes')->nullable();
        });
        DB::table('gestao_acessos_configuracao')->where('id', 1)->update(['inicio_sessoes' => now()]);
    }

    public function down(): void
    {
        // Preserve session history on rollback.
    }
};
