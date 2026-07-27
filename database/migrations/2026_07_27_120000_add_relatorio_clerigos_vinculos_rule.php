<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->createRuleWithProfiles(
            'relatorio-clerigos-vinculos',
            'relatorio-clerigos-categoria',
            ['Administrador Região', 'Secretário(a) Região', 'Administrador SRA']
        );
    }

    public function down(): void
    {
        $rule = DB::table('regras')->where('nome', 'relatorio-clerigos-vinculos')->first();

        if (!$rule) {
            return;
        }

        DB::table('perfil_regra')->where('regra_id', $rule->id)->delete();
        DB::table('regras')->where('id', $rule->id)->delete();
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
            ->whereNull('r.deleted_at')
            ->whereNotNull('pr.perfil_id')
            ->pluck('pr.perfil_id')
            ->unique()
            ->values();

        $fallbackProfileIds = DB::table('perfils')
            ->whereIn('nome', $fallbackProfileNames)
            ->pluck('id');

        $profileIds = $profileIds
            ->merge($fallbackProfileIds)
            ->unique()
            ->values();

        foreach ($profileIds as $profileId) {
            DB::table('perfil_regra')->updateOrInsert(
                ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }
};
