<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rules = [
        'juridico-regiao',
        'juridico-regiao-acoes',
        'juridico-regiao-acoes-novo',
        'juridico-regiao-acoes-editar',
        'juridico-regiao-acoes-excluir',
        'juridico-regiao-advogados',
        'juridico-regiao-advogados-novo',
        'juridico-regiao-advogados-editar',
        'juridico-regiao-advogados-excluir',
        'juridico-regiao-relatorios',
    ];

    public function up(): void
    {
        $now = now();
        $profileIds = $this->profileIds();

        foreach ($this->rules as $ruleName) {
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
        // Mantem as permissoes para evitar remover configuracoes feitas manualmente em producao.
    }

    private function profileIds()
    {
        $profileIds = DB::table('perfil_regra as pr')
            ->join('regras as r', 'r.id', '=', 'pr.regra_id')
            ->whereIn('r.nome', ['regiao-menu-relatorio'])
            ->whereNull('r.deleted_at')
            ->pluck('pr.perfil_id')
            ->unique()
            ->values();

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', ['Administrador do Sistema', 'Administrador Região', 'Administrador SRA', 'Secretário(a) Região'])
                ->pluck('id');
        }

        return $profileIds;
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
