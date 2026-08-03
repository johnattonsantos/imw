<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $ruleNames = [
            'regiao-menu-estatistica',
            'regiao-estatistica-estado-civl',
        ];

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Administrador Região', 'Administrador SRA', 'Secretário(a) Região'])
            ->pluck('id');

        foreach ($ruleNames as $ruleName) {
            $ruleId = DB::table('regras')
                ->where('nome', $ruleName)
                ->whereNull('deleted_at')
                ->value('id');

            if (!$ruleId) {
                continue;
            }

            foreach ($profileIds as $profileId) {
                DB::table('perfil_regra')->updateOrInsert(
                    ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        $ruleIds = DB::table('regras')
            ->whereIn('nome', ['regiao-menu-estatistica', 'regiao-estatistica-estado-civl'])
            ->pluck('id');

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Administrador Região', 'Administrador SRA', 'Secretário(a) Região'])
            ->pluck('id');

        DB::table('perfil_regra')
            ->whereIn('regra_id', $ruleIds)
            ->whereIn('perfil_id', $profileIds)
            ->delete();
    }
};
