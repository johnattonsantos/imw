<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $profiles = DB::table('perfils')->where('nivel', 'R')->whereNull('deleted_at')
                ->whereIn('nome', ['Administrador Região', 'Administrador - Região', 'Administrador Regiao', 'Administrador - Regiao'])
                ->pluck('id');

            if ($profiles->isEmpty()) {
                return;
            }

            foreach (['menu-financeiro', 'menu-relatorios-financeiro', 'regiao-menu-relatorio', 'regiao-menu-relatorio-financeiro'] as $name) {
                $rules = DB::table('regras')->where('nome', $name)->whereNull('deleted_at')->pluck('id');
                if ($rules->isEmpty()) {
                    $rules = collect([DB::table('regras')->insertGetId([
                        'nome' => $name, 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
                    ])]);
                }

                foreach ($profiles as $profileId) {
                    foreach ($rules as $ruleId) {
                        DB::table('perfil_regra')->updateOrInsert(
                            ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Preserve pre-existing grants: their previous state cannot be reconstructed safely.
    }
};
