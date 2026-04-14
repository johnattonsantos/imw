<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfil_user', function (Blueprint $table) {
            $table->dropForeign(['instituicao_id']);
        });

        DB::statement('ALTER TABLE perfil_user MODIFY instituicao_id BIGINT UNSIGNED NULL');

        Schema::table('perfil_user', function (Blueprint $table) {
            $table->foreign('instituicao_id')
                ->references('id')
                ->on('instituicoes_instituicoes')
                ->nullOnDelete();
        });

        $perfilAdminSistemaIds = DB::table('perfils')
            ->whereRaw("LOWER(REPLACE(REPLACE(nome, '-', ' '), '_', ' ')) IN (?, ?)", [
                'administrador sistema',
                'administrador do sistema',
            ])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (!empty($perfilAdminSistemaIds)) {
            DB::table('perfil_user')
                ->whereIn('perfil_id', $perfilAdminSistemaIds)
                ->update(['instituicao_id' => null]);
        }

        DB::statement("
            DELETE pu1
            FROM perfil_user pu1
            JOIN perfil_user pu2
              ON pu1.user_id = pu2.user_id
             AND pu1.perfil_id = pu2.perfil_id
             AND (pu1.instituicao_id <=> pu2.instituicao_id)
             AND pu1.id > pu2.id
        ");
    }

    public function down(): void
    {
        $fallbackInstituicaoId = (int) DB::table('instituicoes_instituicoes')->min('id');
        if ($fallbackInstituicaoId > 0) {
            DB::table('perfil_user')
                ->whereNull('instituicao_id')
                ->update(['instituicao_id' => $fallbackInstituicaoId]);
        }

        Schema::table('perfil_user', function (Blueprint $table) {
            $table->dropForeign(['instituicao_id']);
        });

        DB::statement('ALTER TABLE perfil_user MODIFY instituicao_id BIGINT UNSIGNED NOT NULL');

        Schema::table('perfil_user', function (Blueprint $table) {
            $table->foreign('instituicao_id')
                ->references('id')
                ->on('instituicoes_instituicoes');
        });
    }
};
