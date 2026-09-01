<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $ruleName = 'regiao-estatistica-gceu';
    private string $sourceRuleName = 'regiao-menu-estatistica';

    public function up(): void
    {
        $now = now();
        $ruleId = $this->ensureRule($this->ruleName, $now);

        $sourceRuleId = DB::table('regras')
            ->where('nome', $this->sourceRuleName)
            ->whereNull('deleted_at')
            ->value('id');

        $profileIds = collect();

        if ($sourceRuleId) {
            $profileIds = DB::table('perfil_regra')
                ->where('regra_id', $sourceRuleId)
                ->pluck('perfil_id');
        }

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', ['Administrador Região', 'Secretário(a) Região', 'Administrador SRA'])
                ->pluck('id');
        }

        foreach ($profileIds as $profileId) {
            DB::table('perfil_regra')->updateOrInsert(
                ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        $ruleId = DB::table('regras')
            ->where('nome', $this->ruleName)
            ->value('id');

        if (!$ruleId) {
            return;
        }

        DB::table('perfil_regra')
            ->where('regra_id', $ruleId)
            ->delete();
    }

    private function ensureRule(string $ruleName, $now): int
    {
        $rule = DB::table('regras')->where('nome', $ruleName)->first();

        if ($rule) {
            DB::table('regras')->where('id', $rule->id)->update([
                'deleted_at' => null,
                'updated_at' => $now,
            ]);

            return (int) $rule->id;
        }

        return (int) DB::table('regras')->insertGetId([
            'nome' => $ruleName,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);
    }
};
