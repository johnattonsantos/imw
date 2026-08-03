<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rules = [
        'evento-funcao',
        'evento-funcao-novo',
        'evento-funcao-editar',
        'evento-funcao-excluir',
    ];

    public function up(): void
    {
        $now = now();
        $eventoRule = DB::table('regras')->where('nome', 'evento')->first();
        $profileIds = collect();

        if ($eventoRule) {
            $profileIds = DB::table('perfil_regra')
                ->where('regra_id', $eventoRule->id)
                ->pluck('perfil_id')
                ->unique()
                ->values();
        }

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', ['Administrador do Sistema', 'Pastor', 'Secretario', 'Secretária Local'])
                ->pluck('id');
        }

        foreach ($this->rules as $ruleName) {
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
        $rules = DB::table('regras')->whereIn('nome', $this->rules)->get();

        foreach ($rules as $rule) {
            DB::table('perfil_regra')->where('regra_id', $rule->id)->delete();
            DB::table('regras')->where('id', $rule->id)->delete();
        }
    }
};
