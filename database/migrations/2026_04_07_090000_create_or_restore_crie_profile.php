<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('perfils')->updateOrInsert(
                ['nome' => 'crie'],
                ['nivel' => 'R']
            );

            $perfilCrie = DB::table('perfils')->where('nome', 'crie')->first();
            if (!$perfilCrie) {
                return;
            }

            $regrasCrie = [
                'admin-index',
                'menu-usuarios-instituicao',
                'usuarios-index',
                'usuarios-cadastrar',
                'usuarios-atualizar',
                'usuarios-editar',
                'usuarios-excluir',
                'usuarios-pesquisar',
            ];

            $regraIds = DB::table('regras')
                ->whereIn('nome', $regrasCrie)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            foreach ($regraIds as $regraId) {
                DB::table('perfil_regra')->updateOrInsert(
                    ['perfil_id' => (int) $perfilCrie->id, 'regra_id' => $regraId],
                    []
                );
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $perfilCrie = DB::table('perfils')->where('nome', 'crie')->first();
            if (!$perfilCrie) {
                return;
            }

            DB::table('perfil_regra')
                ->where('perfil_id', (int) $perfilCrie->id)
                ->delete();

            DB::table('perfils')
                ->where('id', (int) $perfilCrie->id)
                ->delete();
        });
    }
};

