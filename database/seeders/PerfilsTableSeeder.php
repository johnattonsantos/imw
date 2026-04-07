<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perfis = [
            ['nome' => 'Administrador', 'nivel' => 'I'],
            ['nome' => 'Administrador Distrito', 'nivel' => 'D'],
            ['nome' => 'Administrador Região', 'nivel' => 'R'],
            ['nome' => Perfil::CODIGO_CRIE, 'nivel' => 'R'],
            ['nome' => 'Secretario', 'nivel' => 'I'],
            ['nome' => 'Tesoureiro', 'nivel' => 'I'],
            ['nome' => Perfil::CODIGO_ADMINISTRADOR_SISTEMA, 'nivel' => 'S'],
            ['nome' => 'Pastor', 'nivel' => 'I'],
        ];

        foreach ($perfis as $perfil) {
            DB::table('perfils')->updateOrInsert(
                ['nome' => $perfil['nome']],
                ['nivel' => $perfil['nivel']]
            );
        }
    }
}
