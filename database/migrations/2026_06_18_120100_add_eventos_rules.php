<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rules = [
        'evento',
        'evento-novo',
        'evento-editar',
        'evento-excluir',
    ];

    public function up(): void
    {
        $now = now();
        $sourceRule = DB::table('regras')->where('nome', 'comunicacao')->first();
        $profileIds = collect();

        if ($sourceRule) {
            $profileIds = DB::table('perfil_regra')
                ->where('regra_id', $sourceRule->id)
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
