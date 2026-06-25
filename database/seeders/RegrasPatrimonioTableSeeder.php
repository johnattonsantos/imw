<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegrasPatrimonioTableSeeder extends Seeder
{
    public function run(): void
    {
        $regrasPorPerfil = [
            'patrimonio-dashboard' => [7],
            'patrimonio-bens-imoveis' => [7],
            'patrimonio-bens-moveis' => [7],
            'patrimonio-documentos' => [7],
            'patrimonio-benfeitoria' => [7],
            'patrimonio-baixa' => [7],
            'patrimonio-relatorios' => [7],
            'regiao-menu-relatorio-patrimonio' => [3],
        ];

        foreach ($regrasPorPerfil as $nomeRegra => $perfilIds) {
            DB::table('regras')->updateOrInsert(
                ['nome' => $nomeRegra],
                ['updated_at' => now(), 'created_at' => now()]
            );

            $regraId = DB::table('regras')->where('nome', $nomeRegra)->value('id');

            foreach ($perfilIds as $perfilId) {
                DB::table('perfil_regra')->updateOrInsert(
                    ['perfil_id' => $perfilId, 'regra_id' => $regraId],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
