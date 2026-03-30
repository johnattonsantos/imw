<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Regra;
use App\Models\UserInstituicao;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criação de usuários
        $admin = User::create([
            'name' => 'Usuário Administrador',
            'email' => 'admin@brasmid.com.br',
            'password' => Hash::make('sk8namao'),
        ]);

        $marcos = User::create([
            'name' => 'Marcos Batista',
            'email' => 'prmarcosbatista1@gmail.com',
            'password' => Hash::make('Kadosh1957*'),
        ]);

        // Vinculando o perfil Administrador aos usuários
        $perfilAdmin = Perfil::where('nome', 'Administrador')->first();
        $perfilAdminDistrito = Perfil::where('nome', 'Administrador - Distrito')->first();
        $perfilAdminRegiao = Perfil::where('nome', 'Administrador - Região')->first();
        $perfilSecretario = Perfil::where('nome', 'Secretario')->first();
        $perfilTesoureiro = Perfil::where('nome', 'Tesoureiro')->first();
        $perfilAdminSistema = Perfil::query()
            ->get()
            ->first(fn ($perfil) => Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA));
        $perfilPastor= Perfil::where('nome', 'Pastor')->first();

        $regrasAdmin = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,7,58,59,60,61,62,63,65,66,68];
        /* $regrasAdminDistrito = [];  Nao possui regra ainda*/
        /* $perfilAdminRegiao = [];  Nao possui regra ainda*/
        $regrasSecretario = [1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,51,52,53,54];
        $regrasTesoureiro = [2,3,42,43,44,45,46,47,48,49,50,55,56,7,58,59,60];
        $regrasAdminSistema = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,7,58,59,60,61,62,63,65,66,67,68];
        $regrasPastor = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,7,58,59,60,61,62,63,65,66,68];


        if ($perfilAdmin) {
            $this->attachRegras($perfilAdmin, $regrasAdmin);
        }

        if ($perfilSecretario) {
            $this->attachRegras($perfilSecretario, $regrasSecretario);
        }

        if ($perfilTesoureiro) {
            $this->attachRegras($perfilTesoureiro, $regrasTesoureiro);
        }


        if ($perfilAdminSistema) {
            $this->attachRegras($perfilAdminSistema, $regrasAdminSistema);
        }

        if ($perfilPastor) {
            $this->attachRegras($perfilPastor, $regrasPastor);
        }


        if ($perfilAdminSistema) {
            $admin->perfis()->attach($perfilAdminSistema->id, ['instituicao_id' => 2215]);
            $admin->perfis()->attach($perfilAdminSistema->id, ['instituicao_id' => 2275]);

            $marcos->perfis()->attach($perfilAdminSistema->id, ['instituicao_id' => 2215]);
            $marcos->perfis()->attach($perfilAdminSistema->id, ['instituicao_id' => 2275]);
        }

        // Vinculando as instituições aos usuários
        UserInstituicao::create(['user_id' => $admin->id, 'instituicao_id' => 13]); // região
        UserInstituicao::create(['user_id' => $admin->id, 'instituicao_id' => 1758]); // distrito
        UserInstituicao::create(['user_id' => $admin->id, 'instituicao_id' => 2215]); // igreja

        UserInstituicao::create(['user_id' => $marcos->id, 'instituicao_id' => 13]); // região
        UserInstituicao::create(['user_id' => $marcos->id, 'instituicao_id' => 1758]); // redistritogião
        UserInstituicao::create(['user_id' => $marcos->id, 'instituicao_id' => 2215]); // igreja


    }

    private function attachRegras($perfil, $regras) {
        foreach ($regras as $regra) {
            $perfil->regras()->attach($regra);
        }
    }
}

