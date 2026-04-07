<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ModuloGeralExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $data)
    {
    }

    public function headings(): array
    {
        return ['Seção', 'Campo 1', 'Campo 2', 'Campo 3'];
    }

    public function collection(): Collection
    {
        $rows = collect();

        $rows->push(['Resumo', 'Período de auditoria', (string) ($this->data['periodoResumo'] ?? '-'), '']);
        $rows->push(['Resumo', 'Total de Usuários', (string) ($this->data['totalUsuarios'] ?? 0), '']);
        $rows->push(['Resumo', 'Total de Instituições', (string) ($this->data['totalInstituicoes'] ?? 0), '']);
        $rows->push(['Resumo', 'Total de Clérigos', (string) ($this->data['totalClerigos'] ?? 0), '']);
        $rows->push(['Resumo', 'Nomeações Ativas', (string) ($this->data['totalNomeacoesAtivas'] ?? 0), '']);
        $rows->push(['Resumo', 'Usuários Admin Sistema', (string) ($this->data['totalUsuariosAdminSistema'] ?? 0), '']);
        $rows->push(['Resumo', 'Usuários CRIE', (string) ($this->data['totalUsuariosCrie'] ?? 0), '']);
        $rows->push(['Resumo', 'Usuários Sem Região', (string) ($this->data['totalUsuariosSemRegiao'] ?? 0), '']);
        $rows->push(['Resumo', 'Auditorias no Período', (string) ($this->data['totalAuditoriasPeriodo'] ?? 0), '']);
        $rows->push(['Resumo', 'Auditorias Hoje', (string) ($this->data['totalAuditoriasHoje'] ?? 0), '']);
        $rows->push(['Resumo', 'Login Falho (Período)', (string) ($this->data['totalAuditoriasLoginFalho'] ?? 0), '']);

        $this->appendSimpleList($rows, 'Usuários por Região', $this->data['usuariosPorRegiao'] ?? collect(), 'regiao_nome', 'total');
        $this->appendSimpleList($rows, 'Instituições por Região', $this->data['instituicoesPorRegiao'] ?? collect(), 'regiao_nome', 'total');
        $this->appendSimpleList($rows, 'Clérigos por Região', $this->data['clerigosPorRegiao'] ?? collect(), 'regiao_nome', 'total');
        $this->appendSimpleList($rows, 'Nomeações Ativas por Região', $this->data['nomeacoesAtivasPorRegiao'] ?? collect(), 'regiao_nome', 'total');
        $this->appendSimpleList($rows, 'Top Nomeações por Instituição', $this->data['nomeacoesPorInstituicao'] ?? collect(), 'instituicao_nome', 'total');
        $this->appendSimpleList($rows, 'Auditorias por Região', $this->data['auditoriasPorRegiao'] ?? collect(), 'regiao_nome', 'total');
        $this->appendSimpleList($rows, 'Auditorias por Evento', $this->data['auditoriasPorEvento'] ?? collect(), 'evento', 'total');
        $this->appendSimpleList($rows, 'Auditorias por Usuário', $this->data['auditoriasPorUsuario'] ?? collect(), 'usuario_nome', 'total');

        $rows->push(['', '', '', '']);
        $rows->push(['Perfis Estratégicos por Região', 'Região', 'Admin Sistema', 'CRIE']);
        foreach (($this->data['perfisEstrategicosPorRegiao'] ?? collect()) as $item) {
            $rows->push([
                'Perfis Estratégicos por Região',
                (string) ($item->regiao_nome ?? '-'),
                (string) ($item->total_admin_sistema ?? 0),
                (string) ($item->total_crie ?? 0),
            ]);
        }

        $rows->push(['', '', '', '']);
        $rows->push(['Eventos Recentes de Auditoria', 'Data/Hora | Evento', 'Usuário', 'Instituição/Região']);
        foreach (($this->data['auditoriasRecentes'] ?? collect()) as $item) {
            $rows->push([
                'Eventos Recentes de Auditoria',
                $this->formatAuditDate($item->created_at ?? null) . ' | ' . strtoupper((string) ($item->event ?? '-')),
                (string) ($item->usuario_nome ?? 'Sistema'),
                (string) ($item->instituicao_nome ?? '-') . ' / ' . (string) ($item->regiao_nome ?? '-'),
            ]);
        }

        return $rows;
    }

    private function appendSimpleList(Collection $rows, string $section, iterable $items, string $labelField, string $valueField): void
    {
        $rows->push(['', '', '', '']);
        $rows->push([$section, 'Descrição', 'Total', '']);

        foreach ($items as $item) {
            $rows->push([
                $section,
                (string) ($item->{$labelField} ?? '-'),
                (string) ($item->{$valueField} ?? 0),
                '',
            ]);
        }
    }

    private function formatAuditDate($value): string
    {
        if (empty($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
