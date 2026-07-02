<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegrasEbdTableSeeder extends Seeder
{
    public function run(): void
    {
        $regras = [
            'ebd-dashboard',
            'ebd-liderancas',
            'ebd-professores',
            'ebd-alunos',
            'ebd-classes',
            'ebd-turmas',
            'ebd-diarios',
            'ebd-agendas',
            'ebd-buscar-membro',
            'ebd-cadastrar-visitante',
        ];

        $perfilIdsPadrao = [1, 4, 7];

        foreach ($regras as $nomeRegra) {
            DB::table('regras')->updateOrInsert(
                ['nome' => $nomeRegra],
                ['updated_at' => now(), 'created_at' => now()]
            );

            $regraId = DB::table('regras')->where('nome', $nomeRegra)->value('id');

            foreach ($perfilIdsPadrao as $perfilId) {
                DB::table('perfil_regra')->updateOrInsert(
                    ['perfil_id' => $perfilId, 'regra_id' => $regraId],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
