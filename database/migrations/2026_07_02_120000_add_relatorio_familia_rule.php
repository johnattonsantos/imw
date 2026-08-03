<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $sourceRuleId = DB::table('regras')
            ->where('nome', 'relatorio-conjuges')
            ->whereNull('deleted_at')
            ->value('id');

        $profileIds = $sourceRuleId
            ? DB::table('perfil_regra')->where('regra_id', $sourceRuleId)->pluck('perfil_id')
            : collect();

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', ['Pastor', 'Secretario', 'Secretária Local', 'Administrador do Sistema'])
                ->pluck('id');
        }

        $rule = DB::table('regras')->where('nome', 'relatorio-familia')->first();
        if ($rule) {
            $ruleId = (int) $rule->id;
            DB::table('regras')->where('id', $ruleId)->update([
                'deleted_at' => null,
                'updated_at' => $now,
            ]);
        } else {
            $ruleId = (int) DB::table('regras')->insertGetId([
                'nome' => 'relatorio-familia',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
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
        $ruleId = DB::table('regras')->where('nome', 'relatorio-familia')->value('id');
        if (!$ruleId) {
            return;
        }

        DB::table('perfil_regra')->where('regra_id', $ruleId)->delete();
        DB::table('regras')->where('id', $ruleId)->delete();
    }
};
