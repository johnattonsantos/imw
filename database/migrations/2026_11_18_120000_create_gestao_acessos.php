<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestao_acessos_configuracao', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->dateTime('inicio_monitoramento');
        });
        DB::table('gestao_acessos_configuracao')->insert(['id' => 1, 'inicio_monitoramento' => now()]);

        Schema::create('gestao_acessos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('regiao_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('instituicao_id');
            $table->date('data_acesso');
            $table->dateTime('primeiro_acesso');
            $table->dateTime('ultimo_acesso');
            $table->unique(['user_id', 'perfil_id', 'instituicao_id', 'data_acesso'], 'gestao_acessos_presenca_unica');
            $table->index(['regiao_id', 'data_acesso']);
        });

        $id = DB::table('regras')->where('nome', 'regiao-gestao-acessos')->value('id');
        if (!$id) {
            $id = DB::table('regras')->insertGetId(['nome' => 'regiao-gestao-acessos', 'created_at' => now(), 'updated_at' => now()]);
        }
        DB::table('regras')->where('id', $id)->update(['deleted_at' => null]);
        $profiles = DB::table('perfils')->where('nivel', 'R')->whereNull('deleted_at')
            ->whereIn('nome', ['Administrador Região', 'Administrador - Região', 'Administrador Regiao', 'Administrador - Regiao'])->pluck('id');
        foreach ($profiles as $profile) {
            DB::table('perfil_regra')->updateOrInsert(['perfil_id' => $profile, 'regra_id' => $id], ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Access history is intentionally retained on rollback.
    }
};
