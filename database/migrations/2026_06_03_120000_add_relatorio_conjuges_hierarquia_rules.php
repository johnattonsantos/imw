<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $this->createRuleWithProfiles(
            'distrito-menu-relatorio-conjuges',
            'distrito-menu-relatorio',
            ['Administrador Distrito']
        );

        $this->createRuleWithProfiles(
            'regiao-menu-relatorio-conjuges',
            'regiao-menu-relatorio',
            ['Administrador Região', 'Secretário(a) Região', 'Administrador SRA']
        );
    }

    public function down()
    {
        $this->deleteRule('distrito-menu-relatorio-conjuges');
        $this->deleteRule('regiao-menu-relatorio-conjuges');
    }

    private function createRuleWithProfiles(string $ruleName, string $sourceRuleName, array $fallbackProfileNames): void
    {
        $now = now();
        $rule = DB::table('regras')->where('nome', $ruleName)->first();

        if ($rule) {
            $ruleId = $rule->id;
            DB::table('regras')->where('id', $ruleId)->update([
                'deleted_at' => null,
                'updated_at' => $now,
            ]);
        } else {
            $ruleId = DB::table('regras')->insertGetId([
                'nome' => $ruleName,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        $profileIds = DB::table('perfil_regra as pr')
            ->join('regras as r', 'r.id', '=', 'pr.regra_id')
            ->where('r.nome', $sourceRuleName)
            ->whereNotNull('pr.perfil_id')
            ->pluck('pr.perfil_id')
            ->unique()
            ->values();

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', $fallbackProfileNames)
                ->pluck('id');
        }

        foreach ($profileIds as $profileId) {
            $exists = DB::table('perfil_regra')
                ->where('perfil_id', $profileId)
                ->where('regra_id', $ruleId)
                ->exists();

            if (!$exists) {
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $profileId,
                    'regra_id' => $ruleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function deleteRule(string $ruleName): void
    {
        $rule = DB::table('regras')->where('nome', $ruleName)->first();

        if (!$rule) {
            return;
        }

        DB::table('perfil_regra')->where('regra_id', $rule->id)->delete();
        DB::table('regras')->where('id', $rule->id)->delete();
    }
};
