<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $eventRules = [
        'evento',
        'evento-novo',
        'evento-editar',
        'evento-excluir',
    ];

    private array $sourceRules = [
        'menu-secretaria',
        'menu-relatorios-secretaria',
        'membros-index',
        'distrito-menu-relatorio',
        'regiao-menu-relatorio',
    ];

    private array $fallbackProfileNames = [
        'Administrador do Sistema',
        'Pastor',
        'Secretario',
        'Secretária Local',
        'Administrador Distrito',
        'Administrador Região',
        'Secretário(a) Região',
        'Administrador SRA',
    ];

    public function up(): void
    {
        $now = now();
        $profileIds = $this->profileIds();

        foreach ($this->eventRules as $ruleName) {
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
        // Mantem as permissoes para evitar remover acessos configurados manualmente em producao.
    }

    private function profileIds()
    {
        $profileIds = DB::table('perfil_regra as pr')
            ->join('regras as r', 'r.id', '=', 'pr.regra_id')
            ->whereIn('r.nome', $this->sourceRules)
            ->whereNull('r.deleted_at')
            ->pluck('pr.perfil_id')
            ->unique()
            ->values();

        if ($profileIds->isEmpty()) {
            $profileIds = DB::table('perfils')
                ->whereIn('nome', $this->fallbackProfileNames)
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
