<?php

namespace App\Http\Controllers\Patrimonio;

use App\Exports\PatrimonioRelatorioExport;
use App\Http\Controllers\Controller;
use App\Models\InstituicoesInstituicao;
use App\Models\Patrimonio\BaixaPatrimonial;
use App\Models\Patrimonio\BemMovel;
use App\Models\Patrimonio\DocumentoPatrimonial;
use App\Models\Patrimonio\Imovel;
use App\Services\Patrimonio\DepreciacaoService;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PatrimonioRelatoriosController extends Controller
{
    public function __construct(private readonly DepreciacaoService $depreciacaoService)
    {
        $this->middleware('seguranca:patrimonio.relatorios');
    }

    public function index(Request $request)
    {
        $filters = $this->enforceIgrejaScope($this->validateFilters($request));
        $reportOptions = $this->reportOptions();

        if (empty($filters['relatorio'])) {
            $filters['relatorio'] = array_key_first($reportOptions);
        }

        $report = $this->buildReport((string) $filters['relatorio'], $filters);

        $igrejas = InstituicoesInstituicao::query()
            ->whereKey((int) $filters['igreja_id'])
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $categorias = BemMovel::query()
            ->where('igreja_id', (int) $filters['igreja_id'])
            ->selectRaw("COALESCE(NULLIF(TRIM(categoria), ''), 'Não informado') as categoria")
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $statusOptions = [
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'baixado' => 'Baixado',
            'em_manutencao' => 'Em manutenção',
            'depreciado' => 'Depreciado',
            'vigente' => 'Vigente (documento)',
            'vencido' => 'Vencido (documento)',
            'cancelado' => 'Cancelado (documento)',
            'aberto' => 'Aberto (risco)',
            'encerrado' => 'Encerrado (risco)',
        ];

        return view('patrimonio.relatorios.index', compact(
            'filters',
            'report',
            'reportOptions',
            'igrejas',
            'categorias',
            'statusOptions'
        ));
    }

    public function exportXlsx(Request $request)
    {
        $filters = $this->enforceIgrejaScope($this->validateFilters($request));
        $reportOptions = $this->reportOptions();

        $reportKey = (string) ($filters['relatorio'] ?? array_key_first($reportOptions));
        $report = $this->buildReport($reportKey, $filters);

        return Excel::download(
            new PatrimonioRelatorioExport($report['headings'], $report['rows']),
            'patrimonio_' . $reportKey . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->enforceIgrejaScope($this->validateFilters($request));
        $reportOptions = $this->reportOptions();

        $reportKey = (string) ($filters['relatorio'] ?? array_key_first($reportOptions));
        $report = $this->buildReport($reportKey, $filters);

        $pdf = FacadePdf::loadView('patrimonio.relatorios.pdf', [
            'report' => $report,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('patrimonio_' . $reportKey . '_' . now()->format('Ymd_His') . '.pdf');
    }

    private function reportOptions(): array
    {
        return [
            'imoveis_cadastrados' => 'Imóveis cadastrados',
            'bens_moveis_cadastrados' => 'Bens móveis cadastrados',
            'imoveis_regularizacao_pendente' => 'Imóveis com regularização pendente',
            'documentos_vencidos' => 'Documentos vencidos',
            'avcb_vencido' => 'AVCB vencido',
            'bens_depreciados' => 'Bens depreciados',
            'baixas_patrimoniais' => 'Baixas patrimoniais',
            'valor_total_por_categoria' => 'Valor total por categoria',
            'bens_por_igreja_unidade' => 'Bens por igreja/unidade',
        ];
    }

    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'relatorio' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->reportOptions()))],
            'igreja_id' => ['nullable', 'integer', 'exists:instituicoes_instituicoes,id'],
            'categoria' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'max:80'],
            'periodo_inicio' => ['nullable', 'date'],
            'periodo_fim' => ['nullable', 'date', 'after_or_equal:periodo_inicio'],
        ]);
    }

    private function enforceIgrejaScope(array $filters): array
    {
        $igrejaIdSessao = $this->resolveIgrejaId();

        if (empty($filters['igreja_id'])) {
            $filters['igreja_id'] = $igrejaIdSessao;

            return $filters;
        }

        if ((int) $filters['igreja_id'] !== $igrejaIdSessao) {
            abort(403, 'Não é permitido gerar relatórios para outra igreja/unidade.');
        }

        return $filters;
    }

    private function resolveIgrejaId(): int
    {
        $igrejaId = (int) (
            data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id')
            ?? data_get(session('session_perfil'), 'instituicao_id')
            ?? 0
        );

        if ($igrejaId <= 0) {
            abort(403, 'Igreja não identificada na sessão.');
        }

        return $igrejaId;
    }

    private function buildReport(string $reportKey, array $filters): array
    {
        return match ($reportKey) {
            'bens_moveis_cadastrados' => $this->reportBensMoveisCadastrados($filters),
            'imoveis_regularizacao_pendente' => $this->reportImoveisRegularizacaoPendente($filters),
            'documentos_vencidos' => $this->reportDocumentosVencidos($filters),
            'avcb_vencido' => $this->reportAvcbVencido($filters),
            'bens_depreciados' => $this->reportBensDepreciados($filters),
            'baixas_patrimoniais' => $this->reportBaixasPatrimoniais($filters),
            'valor_total_por_categoria' => $this->reportValorTotalPorCategoria($filters),
            'bens_por_igreja_unidade' => $this->reportBensPorIgrejaUnidade($filters),
            default => $this->reportImoveisCadastrados($filters),
        };
    }

    private function reportImoveisCadastrados(array $filters): array
    {
        $query = Imovel::query()->with('igreja:id,nome')->orderBy('nome');

        $this->applyIgrejaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status_titularidade', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'created_at', $filters);

        $rows = $query->get()->map(function (Imovel $imovel) {
            return [
                $imovel->id,
                $imovel->igreja?->nome ?? '-',
                $imovel->nome ?: ('Imóvel #' . $imovel->id),
                $imovel->status_titularidade ?: '-',
                $imovel->regularizacao_pendente ? 'Sim' : 'Não',
                $this->money($imovel->valor_historico),
                $this->money($imovel->valor_mercado),
                optional($imovel->created_at)->format('d/m/Y H:i'),
            ];
        })->all();

        return [
            'title' => 'Relatório de Imóveis Cadastrados',
            'headings' => ['ID', 'Igreja/Unidade', 'Imóvel', 'Status de titularidade', 'Regularização pendente', 'Valor histórico', 'Valor de mercado', 'Cadastrado em'],
            'rows' => $rows,
        ];
    }

    private function reportBensMoveisCadastrados(array $filters): array
    {
        $query = BemMovel::query()->with('igreja:id,nome')->orderBy('nome');

        $this->applyIgrejaFilter($query, $filters);
        $this->applyCategoriaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'created_at', $filters);

        $rows = $query->get()->map(function (BemMovel $bem) {
            return [
                $bem->id,
                $bem->igreja?->nome ?? '-',
                $bem->codigo_patrimonial ?: '-',
                $bem->nome,
                $bem->categoria ?: 'Não informado',
                ucfirst(str_replace('_', ' ', (string) $bem->status)),
                $bem->estado_conservacao ?: '-',
                $this->money($bem->valor_aquisicao),
                optional($bem->created_at)->format('d/m/Y H:i'),
            ];
        })->all();

        return [
            'title' => 'Relatório de Bens Móveis Cadastrados',
            'headings' => ['ID', 'Igreja/Unidade', 'Código', 'Nome', 'Categoria', 'Status', 'Conservação', 'Valor aquisição', 'Cadastrado em'],
            'rows' => $rows,
        ];
    }

    private function reportImoveisRegularizacaoPendente(array $filters): array
    {
        $query = Imovel::query()
            ->with('igreja:id,nome')
            ->where('regularizacao_pendente', true)
            ->orderBy('nome');

        $this->applyIgrejaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status_titularidade', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'created_at', $filters);

        $rows = $query->get()->map(function (Imovel $imovel) {
            return [
                $imovel->id,
                $imovel->igreja?->nome ?? '-',
                $imovel->nome ?: ('Imóvel #' . $imovel->id),
                $imovel->status_titularidade ?: '-',
                $this->money($imovel->valor_historico),
                optional($imovel->created_at)->format('d/m/Y H:i'),
            ];
        })->all();

        return [
            'title' => 'Relatório de Imóveis com Regularização Pendente',
            'headings' => ['ID', 'Igreja/Unidade', 'Imóvel', 'Status de titularidade', 'Valor histórico', 'Cadastrado em'],
            'rows' => $rows,
        ];
    }

    private function reportDocumentosVencidos(array $filters): array
    {
        $query = DocumentoPatrimonial::query()
            ->with(['igreja:id,nome', 'documentavel'])
            ->where(function (Builder $builder) {
                $builder->where('status', 'vencido')
                    ->orWhere(function (Builder $sub) {
                        $sub->whereNotNull('data_validade')->whereDate('data_validade', '<', now()->toDateString());
                    });
            })
            ->orderBy('data_validade');

        $this->applyIgrejaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'data_validade', $filters);

        $rows = $query->get()->map(function (DocumentoPatrimonial $documento) {
            return [
                $documento->id,
                $documento->igreja?->nome ?? '-',
                $documento->nome,
                $documento->tipo,
                class_basename((string) $documento->documentavel_type),
                optional($documento->data_validade)->format('d/m/Y') ?: '-',
                ucfirst((string) $documento->status),
            ];
        })->all();

        return [
            'title' => 'Relatório de Documentos Vencidos',
            'headings' => ['ID', 'Igreja/Unidade', 'Nome', 'Tipo', 'Vínculo', 'Validade', 'Status'],
            'rows' => $rows,
        ];
    }

    private function reportAvcbVencido(array $filters): array
    {
        $docQuery = DocumentoPatrimonial::query()
            ->with('igreja:id,nome')
            ->whereRaw('LOWER(tipo) LIKE ?', ['%avcb%'])
            ->where(function (Builder $builder) {
                $builder->where('status', 'vencido')
                    ->orWhere(function (Builder $sub) {
                        $sub->whereNotNull('data_validade')->whereDate('data_validade', '<', now()->toDateString());
                    });
            });

        $this->applyIgrejaFilter($docQuery, $filters);
        $this->applyPeriodoFilter($docQuery, 'data_validade', $filters);

        $docsRows = $docQuery->orderBy('data_validade')->get()->map(function (DocumentoPatrimonial $documento) {
            return [
                'Documento AVCB',
                $documento->igreja?->nome ?? '-',
                $documento->nome,
                optional($documento->data_validade)->format('d/m/Y') ?: '-',
                ucfirst((string) $documento->status),
            ];
        });

        $imovelQuery = Imovel::query()
            ->with('igreja:id,nome')
            ->whereNotNull('avcb_validade')
            ->whereDate('avcb_validade', '<', now()->toDateString());

        $this->applyIgrejaFilter($imovelQuery, $filters);
        $this->applyPeriodoFilter($imovelQuery, 'avcb_validade', $filters);

        $imoveisRows = $imovelQuery->orderBy('avcb_validade')->get()->map(function (Imovel $imovel) {
            return [
                'Imóvel (campo AVCB)',
                $imovel->igreja?->nome ?? '-',
                $imovel->nome ?: ('Imóvel #' . $imovel->id),
                optional($imovel->avcb_validade)->format('d/m/Y') ?: '-',
                'Vencido',
            ];
        });

        return [
            'title' => 'Relatório de AVCB Vencido',
            'headings' => ['Origem', 'Igreja/Unidade', 'Descrição', 'Data validade', 'Status'],
            'rows' => $docsRows->concat($imoveisRows)->values()->all(),
        ];
    }

    private function reportBensDepreciados(array $filters): array
    {
        $query = BemMovel::query()->with('igreja:id,nome')->orderBy('nome');

        $this->applyIgrejaFilter($query, $filters);
        $this->applyCategoriaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'created_at', $filters);

        $rows = $query->get()->map(function (BemMovel $bem) {
            $dep = $this->depreciacaoService->calcular($bem);

            if (! $dep['status_depreciado']) {
                return null;
            }

            return [
                $bem->id,
                $bem->igreja?->nome ?? '-',
                $bem->codigo_patrimonial ?: '-',
                $bem->nome,
                $bem->categoria ?: 'Não informado',
                $this->money($dep['depreciacao_anual']),
                $this->money($dep['depreciacao_acumulada']),
                $this->money($dep['valor_contabil_atual']),
                number_format((float) $dep['percentual_depreciado'], 2, ',', '.') . '%',
                'Depreciado',
            ];
        })->filter()->values()->all();

        return [
            'title' => 'Relatório de Bens Depreciados',
            'headings' => ['ID', 'Igreja/Unidade', 'Código', 'Nome', 'Categoria', 'Depreciação anual', 'Depreciação acumulada', 'Valor contábil atual', '% depreciado', 'Status'],
            'rows' => $rows,
        ];
    }

    private function reportBaixasPatrimoniais(array $filters): array
    {
        $query = BaixaPatrimonial::query()
            ->with(['igreja:id,nome', 'bemMovel:id,nome,codigo_patrimonial,categoria,status'])
            ->orderByDesc('data_baixa');

        $this->applyIgrejaFilter($query, $filters);
        if (! empty($filters['categoria'])) {
            $categoria = trim((string) $filters['categoria']);
            $query->whereHas('bemMovel', function (Builder $builder) use ($categoria) {
                $builder->whereRaw("COALESCE(NULLIF(TRIM(categoria), ''), 'Não informado') = ?", [$categoria]);
            });
        }

        if (! empty($filters['status'])) {
            $query->whereHas('bemMovel', function (Builder $builder) use ($filters) {
                $builder->where('status', $filters['status']);
            });
        }

        $this->applyPeriodoFilter($query, 'data_baixa', $filters);

        $rows = $query->get()->map(function (BaixaPatrimonial $baixa) {
            return [
                $baixa->id,
                $baixa->igreja?->nome ?? '-',
                $baixa->bemMovel?->codigo_patrimonial ?: '-',
                $baixa->bemMovel?->nome ?: ('Bem móvel #' . $baixa->bem_movel_id),
                $baixa->motivo ?: '-',
                optional($baixa->data_baixa)->format('d/m/Y') ?: '-',
                $baixa->responsavel ?: '-',
                ucfirst(str_replace('_', ' ', (string) ($baixa->bemMovel?->status ?? ''))),
            ];
        })->all();

        return [
            'title' => 'Relatório de Baixas Patrimoniais',
            'headings' => ['ID', 'Igreja/Unidade', 'Código do bem', 'Bem móvel', 'Motivo', 'Data baixa', 'Responsável', 'Status do bem'],
            'rows' => $rows,
        ];
    }

    private function reportValorTotalPorCategoria(array $filters): array
    {
        $query = BemMovel::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(categoria), ''), 'Não informado') as categoria")
            ->selectRaw('COUNT(*) as quantidade')
            ->selectRaw('COALESCE(SUM(COALESCE(valor_aquisicao, 0)), 0) as valor_total')
            ->groupBy('categoria')
            ->orderByDesc('valor_total');

        $this->applyIgrejaFilter($query, $filters);
        $this->applyCategoriaFilter($query, $filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'created_at', $filters);

        $rows = $query->get()->map(function ($item) {
            return [
                $item->categoria,
                (int) $item->quantidade,
                $this->money($item->valor_total),
            ];
        })->all();

        return [
            'title' => 'Relatório de Valor Total por Categoria',
            'headings' => ['Categoria', 'Quantidade de bens', 'Valor total'],
            'rows' => $rows,
        ];
    }

    private function reportBensPorIgrejaUnidade(array $filters): array
    {
        $query = BemMovel::query()
            ->join('instituicoes_instituicoes as inst', 'inst.id', '=', 'patrimonio_bens_moveis.igreja_id')
            ->selectRaw('inst.id as igreja_id')
            ->selectRaw('inst.nome as igreja_nome')
            ->selectRaw('COUNT(patrimonio_bens_moveis.id) as quantidade')
            ->selectRaw('COALESCE(SUM(COALESCE(patrimonio_bens_moveis.valor_aquisicao, 0)), 0) as valor_total')
            ->groupBy('inst.id', 'inst.nome')
            ->orderBy('inst.nome');

        if (! empty($filters['igreja_id'])) {
            $query->where('patrimonio_bens_moveis.igreja_id', (int) $filters['igreja_id']);
        }

        $this->applyCategoriaFilter($query, $filters, 'patrimonio_bens_moveis');
        if (! empty($filters['status'])) {
            $query->where('patrimonio_bens_moveis.status', $filters['status']);
        }
        $this->applyPeriodoFilter($query, 'patrimonio_bens_moveis.created_at', $filters);

        $rows = $query->get()->map(function ($item) {
            return [
                (int) $item->igreja_id,
                $item->igreja_nome,
                (int) $item->quantidade,
                $this->money($item->valor_total),
            ];
        })->all();

        return [
            'title' => 'Relatório de Bens por Igreja/Unidade',
            'headings' => ['ID Igreja/Unidade', 'Igreja/Unidade', 'Quantidade de bens', 'Valor total'],
            'rows' => $rows,
        ];
    }

    private function applyIgrejaFilter(Builder $query, array $filters): void
    {
        if (! empty($filters['igreja_id'])) {
            $query->where('igreja_id', (int) $filters['igreja_id']);
        }
    }

    private function applyCategoriaFilter(Builder $query, array $filters, string $tablePrefix = ''): void
    {
        if (empty($filters['categoria'])) {
            return;
        }

        $categoria = trim((string) $filters['categoria']);
        $column = $tablePrefix !== '' ? $tablePrefix . '.categoria' : 'categoria';

        $query->whereRaw("COALESCE(NULLIF(TRIM({$column}), ''), 'Não informado') = ?", [$categoria]);
    }

    private function applyPeriodoFilter(Builder $query, string $column, array $filters): void
    {
        if (! empty($filters['periodo_inicio'])) {
            $query->whereDate($column, '>=', $filters['periodo_inicio']);
        }

        if (! empty($filters['periodo_fim'])) {
            $query->whereDate($column, '<=', $filters['periodo_fim']);
        }
    }

    private function money(mixed $value): string
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }
}
