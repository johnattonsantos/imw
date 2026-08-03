<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* $this->call(CreateViewsSeeder::class);
        $this->call(MembresiaTipoAtuacaoSeeder::class);
        $this->call(MembresiaSituacaoSeeder::class);
        $this->call(MembresiaSetorSeeder::class);
        $this->call(MembresiaFuncaoEclesiasticaSeeder::class);
        $this->call(MembresiaCursoSeeder::class);
        $this->call(InstituicoesTipoInstituicaoSeeder::class);
        $this->call(MembresiaFormacaoSeeder::class);
        $this->call(ControleImportacaoCsvSeeder::class);
        $this->call(ImportacoesCsvSeeder::class);
        $this->call(FornecedoresTableSeeder::class);
        $this->call(FinanceiroTiposPagantesFavorecidosSeeder::class);
        $this->call(PerfilsTableSeeder::class);
        $this->call(RegrasTableSeeder::class);
        $this->call(RegrasExtrasTableSeeder::class);
        $this->call(PerfilRegraTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(FinanceiroPlanoContaTipoInstituicaoSeeder::class);
        $this->call(RegrasDistritoTableSeeder::class); */
        $this->call(RegrasRegiaoEstatisticaTableSeeder::class);
        $this->call(RegrasPatrimonioTableSeeder::class);
    }
}
