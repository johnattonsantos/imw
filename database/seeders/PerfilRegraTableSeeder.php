<?php

namespace Database\Seeders;

use App\Models\Perfil;
use App\Models\Regra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilRegraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /*  $perfilAdminIgreja = Perfil::where('nome', 'Administrador - Igreja')->first();
        $perfilPastorIgreja = Perfil::where('nome', 'Pastor - Igreja')->first();
        $perfilTesoureiroIgreja = Perfil::where('nome', 'Tesoureiro - Igreja')->first(); */
        $perfilAdministradordoSistema = Perfil::query()
            ->get()
            ->first(fn ($perfil) => Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA));

        $perfilCrie = Perfil::query()
            ->get()
            ->first(fn ($perfil) => Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE));

        $regras = Regra::all();


        if ($perfilAdministradordoSistema) {

            foreach ($regras as $regra) {
                // Insere o relacionamento na tabela pivot
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $perfilAdministradordoSistema->id,
                    'regra_id' => $regra->id
                ]);
            }
        }

        if ($perfilCrie) {
            $regrasCrie = Regra::whereIn('nome', [
                'admin-index',
                'usuarios-index',
                'usuarios-cadastrar',
                'usuarios-atualizar',
                'usuarios-editar',
                'usuarios-excluir',
                'usuarios-pesquisar',
            ])->get();

            foreach ($regrasCrie as $regra) {
                DB::table('perfil_regra')->updateOrInsert(
                    ['perfil_id' => $perfilCrie->id, 'regra_id' => $regra->id],
                    []
                );
            }
        }


     /*    if ($perfilperfilAdminIgrejaAdmin) {
            $regras = Regra::all();
            foreach ($regras as $regra) {
                // Insere o relacionamento na tabela pivot
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $perfilAdmin->id,
                    'regra_id' => $regra->id
                ]);
            }
        }


        if($perfilPastorIgreja) {
            $regrasIds = [1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,51,52,53,54,68];

            foreach ($regrasIds as $regraId) {
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $perfilPastorIgreja->id,
                    'regra_id' => $regraId
                ]);
            }
        }

        if($perfilTesoureiroIgreja) {
            $regrasIds = [2,3,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60];

            foreach ($regrasIds as $regraId) {
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $perfilTesoureiroIgreja->id,
                    'regra_id' => $regraId
                ]);
            }
        }
   */




    }
}
