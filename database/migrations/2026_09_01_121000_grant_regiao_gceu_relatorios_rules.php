<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $ruleNames = [
        'gceu-regiao-relatorios',
        'gceu-regiao-lista-gceu',
    ];

    private string $sourceRuleName = 'regiao-menu-estatistica';

    public function up(): void
    {
        $now = now();
        $profileIds = $this->profileIds();

        foreach ($this->ruleNames as $ruleName) {
            $ruleId = $this->ensureRule($ruleName, $now);

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
            ->whereIn('nome', $this->ruleNames)
            ->pluck('id');

        if ($ruleIds->isEmpty()) {
            return;
        }

        DB::table('perfil_regra')
            ->whereIn('regra_id', $ruleIds)
            ->whereIn('perfil_id', $this->profileIds())
            ->delete();
    }

    private function profileIds()
    {
        $sourceRuleId = DB::table('regras')
            ->where('nome', $this->sourceRuleName)
            ->whereNull('deleted_at')
            ->value('id');

        if ($sourceRuleId) {
            $profileIds = DB::table('perfil_regra')
                ->where('regra_id', $sourceRuleId)
                ->pluck('perfil_id');

            if ($profileIds->isNotEmpty()) {
                return $profileIds;
            }
        }

        return DB::table('perfils')
            ->whereIn('nome', ['Administrador Região', 'Secretário(a) Região', 'Administrador SRA'])
            ->pluck('id');
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
