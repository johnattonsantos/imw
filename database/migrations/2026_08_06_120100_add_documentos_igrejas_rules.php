<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rules = [
        'documentos-igrejas-gerenciar' => ['Administrador Região'],
        'documentos-igrejas-visualizar' => ['Pastor', 'Secretario', 'Secretária Local'],
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->rules as $ruleName => $profileNames) {
            $ruleId = $this->ensureRule($ruleName, $now);
            $profileIds = DB::table('perfils')->whereIn('nome', $profileNames)->pluck('id');

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
        $ruleNames = array_keys($this->rules);
        $rules = DB::table('regras')->whereIn('nome', $ruleNames)->get();

        foreach ($rules as $rule) {
            DB::table('perfil_regra')->where('regra_id', $rule->id)->delete();
            DB::table('regras')->where('id', $rule->id)->delete();
        }
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
