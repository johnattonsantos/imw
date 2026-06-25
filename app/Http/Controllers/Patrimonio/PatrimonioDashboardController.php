<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Models\Patrimonio\BemMovel;
use App\Models\Patrimonio\DocumentoPatrimonial;
use App\Models\Patrimonio\Imovel;
use App\Models\Patrimonio\RiscoJuridico;

class PatrimonioDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('seguranca:patrimonio-dashboard');
    }

    public function index()
    {
        $igrejaId = (int) (
            data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id')
            ?? data_get(session('session_perfil'), 'instituicao_id')
            ?? 0
        );

        $cards = [
            'total_imoveis' => 0,
            'total_bens_moveis' => 0,
            'valor_total_imoveis' => 0,
            'valor_total_bens_moveis' => 0,
            'documentos_vencidos' => 0,
            'avcb_vencido' => 0,
            'imoveis_regularizacao_pendente' => 0,
            'bens_depreciados' => 0,
            'riscos_criticos' => 0,
        ];

        $charts = [
            'bens_por_categoria' => ['labels' => [], 'values' => []],
            'imoveis_por_titularidade' => ['labels' => [], 'values' => []],
            'bens_por_conservacao' => ['labels' => [], 'values' => []],
        ];

        $riscosAltosCriticos = collect();

        if ($igrejaId > 0) {
            $cards['total_imoveis'] = Imovel::query()->daIgreja($igrejaId)->count();
            $cards['total_bens_moveis'] = BemMovel::query()->daIgreja($igrejaId)->count();
            $cards['valor_total_imoveis'] = (float) Imovel::query()
                ->daIgreja($igrejaId)
                ->selectRaw('COALESCE(SUM(COALESCE(valor_mercado, valor_venal, valor_historico, 0)), 0) as total')
                ->value('total');
            $cards['valor_total_bens_moveis'] = (float) BemMovel::query()
                ->daIgreja($igrejaId)
                ->selectRaw('COALESCE(SUM(COALESCE(valor_aquisicao, 0)), 0) as total')
                ->value('total');

            $cards['documentos_vencidos'] = DocumentoPatrimonial::query()
                ->daIgreja($igrejaId)
                ->where(function ($query) {
                    $query->where('status', 'vencido')
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('data_validade')
                                ->whereDate('data_validade', '<', now()->toDateString());
                        });
                })
                ->count();

            $avcbDocsVencidos = DocumentoPatrimonial::query()
                ->daIgreja($igrejaId)
                ->whereRaw('LOWER(tipo) LIKE ?', ['%avcb%'])
                ->where(function ($query) {
                    $query->where('status', 'vencido')
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('data_validade')
                                ->whereDate('data_validade', '<', now()->toDateString());
                        });
                })
                ->count();

            $avcbImoveisVencidos = Imovel::query()
                ->daIgreja($igrejaId)
                ->whereNotNull('avcb_validade')
                ->whereDate('avcb_validade', '<', now()->toDateString())
                ->count();

            $cards['avcb_vencido'] = $avcbDocsVencidos > 0 ? $avcbDocsVencidos : $avcbImoveisVencidos;

            $cards['imoveis_regularizacao_pendente'] = Imovel::query()
                ->daIgreja($igrejaId)
                ->where('regularizacao_pendente', true)
                ->count();

            $cards['bens_depreciados'] = BemMovel::query()
                ->daIgreja($igrejaId)
                ->where('status', 'depreciado')
                ->count();

            $cards['riscos_criticos'] = RiscoJuridico::query()
                ->daIgreja($igrejaId)
                ->where('nivel_risco', 'critico')
                ->count();

            $bensPorCategoria = BemMovel::query()
                ->daIgreja($igrejaId)
                ->selectRaw("COALESCE(NULLIF(TRIM(categoria), ''), 'Não informado') as label")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();

            $imoveisPorTitularidade = Imovel::query()
                ->daIgreja($igrejaId)
                ->selectRaw("COALESCE(NULLIF(TRIM(status_titularidade), ''), 'Não informado') as label")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();

            $bensPorConservacao = BemMovel::query()
                ->daIgreja($igrejaId)
                ->selectRaw("COALESCE(NULLIF(TRIM(estado_conservacao), ''), 'Não informado') as label")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();

            $charts['bens_por_categoria'] = [
                'labels' => $bensPorCategoria->pluck('label')->values(),
                'values' => $bensPorCategoria->pluck('total')->map(fn ($item) => (int) $item)->values(),
            ];

            $charts['imoveis_por_titularidade'] = [
                'labels' => $imoveisPorTitularidade->pluck('label')->values(),
                'values' => $imoveisPorTitularidade->pluck('total')->map(fn ($item) => (int) $item)->values(),
            ];

            $charts['bens_por_conservacao'] = [
                'labels' => $bensPorConservacao->pluck('label')->values(),
                'values' => $bensPorConservacao->pluck('total')->map(fn ($item) => (int) $item)->values(),
            ];

            $riscosAltosCriticos = RiscoJuridico::query()
                ->daIgreja($igrejaId)
                ->altosECriticos()
                ->with('imovel:id,nome')
                ->orderByRaw("FIELD(nivel_risco, 'critico', 'alto')")
                ->orderByDesc('data_identificacao')
                ->limit(10)
                ->get();
        }

        return view('patrimonio.dashboard', compact('cards', 'charts', 'riscosAltosCriticos'));
    }
}
