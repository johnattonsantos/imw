<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $reports = [
        'regiao-menu-relatorio',
        'regiao-menu-relatorio-ebd',
        'regiao-ebd-dashboard',
        'regiao-ebd-estatisticas',
        'regiao-ebd-turmas',
        'regiao-ebd-alunos',
        'regiao-ebd-professores',
        'regiao-ebd-liderancas',
        'regiao-ebd-classes',
        'regiao-ebd-diarios',
        'regiao-ebd-agendas',
        'regiao-ebd-geral',
        'regiao-mapao',
        'regiao-relatorio-membros-ministerio',
        'regiao-relatorio-quantidade-membros',
        'regiao-menu-relatorio-conjuges',
        'regiao-menu-relatorio-congregados',
        'regiao-relatorio-perfil-membros-recebidos',
        'regiao-relatorio-perfil-membros-excluidos',
        'regiao-relatorio-estatistica-genero',
        'regiao-relatorio-ano-eclesiastico',
        'menu-relatorios-instituicoes-igrejas',
        'relatorio-clerigos-aniversariantes',
        'relatorio-clerigos-dados',
        'relatorio-clerigos-esposas',
        'relatorio-clerigos-categoria',
        'relatorio-clerigos-status',
        'relatorio-clerigos-vinculos',
        'regiao-estatistica-hitorico-nomeacoes',
        'menu-relatorios-congregacoes-igrejas',
        'regiao-relatorio-congregacoes-igrejas',
        'regiao-relatorio-aspirantes-igrejas',
        'regiao-relatorio-cnpj-igreja',
        'regiao-relatorio-contato-igreja',
        'regiao-relatorio-conta-bancaria-igreja',
        'regiao-menu-relatorio-patrimonio',
        'gceu-regiao-relatorios',
        'regiao-estatistica-gceu',
        'gceu-regiao-lista-gceu',
        'gceu-regiao-lista-funcoes',
        'gceu-regiao-lista-aniversariantes',
        'gceu-regiao-relatorio-carta-pastoral',
    ];

    private array $financialReports = [
        'regiao-menu-relatorio-financeiro',
        'regiao-relatorio-lancamento-das-igrejas',
        'regiao-ebd-lancamentodasigrejas',
        'regiao-ebd-relatorio',
        'financeiro-relatorio-balancete',
        'regiao-relatorio-saldo-das-igrejas',
        'regiao-relatorio-livro-razao-geral',
        'regiao-relatorio-orcamento',
        'regiao-relatorio-variacao-financeira',
        'financeiro-por-categoria',
        'regiao-cota-orcamentaria',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $profiles = DB::table('perfils')
                ->where('nivel', 'R')
                ->whereIn('nome', ['Secretário(a) Região', 'Secretário Região', 'Secretario Região', 'Secretario Regiao'])
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($profiles->isEmpty()) {
                return;
            }

            $rules = DB::table('regras')->whereIn('nome', $this->reports)
                ->whereNull('deleted_at')->pluck('id');

            foreach ($profiles as $profileId) {
                foreach ($rules as $ruleId) {
                    DB::table('perfil_regra')->updateOrInsert(
                        ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            $financialRules = DB::table('regras')->whereIn('nome', $this->financialReports)->pluck('id');
            DB::table('perfil_regra')->whereIn('perfil_id', $profiles)
                ->whereIn('regra_id', $financialRules)->delete();
        });
    }

    public function down(): void
    {
        // Forward-only permission repair: previous grants cannot be reconstructed safely.
    }
};
