<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use App\Models\FinanceiroPlanoConta;
use App\Models\MembresiaMembro;
use App\Models\PerfilUser;
use App\Services\ServicePerfil\IdentificaPerfilService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $igrejaId = session()->get('session_perfil')->instituicao_id;
        $instituicao = InstituicoesInstituicao::where('id', $igrejaId)->first();
        $anoAtual = (int) Carbon::now()->year;
        $sanitizeAno = fn ($value) => $this->sanitizeAno($value, $anoAtual);

        $anoVisitantes = $sanitizeAno($request->input('ano_visitantes', $anoAtual));
        $anoRol = $sanitizeAno($request->input('ano_rol', $anoAtual));
        $anoFinanceiro = $sanitizeAno($request->input('ano_financeiro', $anoAtual));
        $anoDistrito = $sanitizeAno($request->input('ano_distrito', $anoAtual));
        $sexoMembresia = $this->sanitizeSexo($request->input('sexo_membresia'));
        $statusMembresia = $this->sanitizeStatus($request->input('status_membresia')) ?? 'A';
        $anosDisponiveis = range($anoAtual, $anoAtual - 10);

        $activeMembrosCount = MembresiaMembro::join('membresia_rolpermanente as mr', 'membresia_membros.id', '=', 'mr.membro_id')
        ->where('membresia_membros.vinculo', 'M')
        ->where('membresia_membros.igreja_id', $igrejaId)
        ->where('mr.status', 'A')
        ->where('mr.lastrec', 1)
        ->count();

        $activeCongregadosCount =  MembresiaMembro::where('membresia_membros.vinculo', 'C')
            ->where('membresia_membros.igreja_id', $igrejaId)
            ->count();

        
        $activeVisitantesCount = DB::table('membresia_membros as mm')
            ->where('mm.vinculo', 'V')
            ->where('mm.igreja_id', $igrejaId)
            ->count();

        $totalAtivos = DB::table('membresia_membros as mm')
            ->join('membresia_rolpermanente as mr', 'mm.id', '=', 'mr.membro_id')
            ->where('mm.vinculo', 'M')
            ->where('mm.igreja_id', $igrejaId)
            ->where('mr.igreja_id', $igrejaId)
            ->where('mr.status', 'A')
            ->where('mr.lastrec', 1)
            ->count();


        $totalInativos = DB::table('membresia_membros as mm')
            ->join('membresia_rolpermanente as mr', 'mm.id', '=', 'mr.membro_id')
            ->where('mm.vinculo', 'M')
            ->where('mm.igreja_id', $igrejaId)
            ->where('mr.igreja_id', $igrejaId)
            ->where('mr.status', 'I')
            ->where('mr.lastrec', 1)
            ->count();

        $visitantesPorMesCompleto = $this->buildVinculoPorMes($igrejaId, $anoVisitantes, 'V', $sexoMembresia, $statusMembresia);
        $congregadosPorMesCompleto = $this->buildVinculoPorMes($igrejaId, $anoVisitantes, 'C', $sexoMembresia, $statusMembresia);
        $membrosPorMesCompleto = $this->buildVinculoPorMes($igrejaId, $anoVisitantes, 'M', $sexoMembresia, $statusMembresia);
        $rolData = $this->buildRolPorMes($igrejaId, $anoRol);
        $financeiroData = $this->buildFinanceiroPorMes($igrejaId, $anoFinanceiro);

        $distritoEvolucaoDatasets = [];
        $distritoEntradasPorMes = array_fill(1, 12, 0);
        $distritoSaidasPorMes = array_fill(1, 12, 0);
        $distritoTopIgrejasLabels = [];
        $distritoTopIgrejasTotais = [];
        $distritoVinculosTotais = [0, 0, 0];
        $distritoSexoTotais = [0, 0, 0];
        $distritoStatusRolTotais = [0, 0];
        $distritoCrescimentoAcumulado = array_fill(1, 12, 0);
        $distritoCrescimentoIgrejasLabels = [];
        $distritoCrescimentoIgrejasTotais = [];
        $distritoEntradasIgrejasLabels = [];
        $distritoEntradasIgrejasTotais = [];
        $distritoSaidasIgrejasLabels = [];
        $distritoSaidasIgrejasTotais = [];
        $distritoIgrejas = collect();

        if (($instituicao->tipoInstituicao->sigla ?? null) === 'D') {
            $distritoIgrejas = DB::table('instituicoes_instituicoes as ii')
                ->join('membresia_membros as mm', 'mm.igreja_id', '=', 'ii.id')
                ->where('mm.distrito_id', $igrejaId)
                ->select('ii.id', 'ii.nome')
                ->distinct()
                ->orderBy('ii.nome')
                ->get();

            $topIgrejas = DB::table('membresia_membros as mm')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mm.igreja_id')
                ->select('ii.id', 'ii.nome', DB::raw('COUNT(mm.id) as total'))
                ->where('mm.distrito_id', $igrejaId)
                ->where('mm.vinculo', 'M')
                ->whereYear('mm.created_at', $anoDistrito)
                ->groupBy('ii.id', 'ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $distritoTopIgrejasLabels = $topIgrejas->pluck('nome')->values()->all();
            $distritoTopIgrejasTotais = $topIgrejas->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $topIgrejaIdsEvolucao = $topIgrejas->take(5)->pluck('id')->values()->all();
            if (!empty($topIgrejaIdsEvolucao)) {
                $evolucaoRows = DB::table('membresia_membros as mm')
                    ->select(
                        'mm.igreja_id',
                        DB::raw('MONTH(mm.created_at) as mes'),
                        DB::raw('COUNT(mm.id) as total')
                    )
                    ->whereIn('mm.igreja_id', $topIgrejaIdsEvolucao)
                    ->where('mm.vinculo', 'M')
                    ->whereYear('mm.created_at', $anoDistrito)
                    ->groupBy('mm.igreja_id', DB::raw('MONTH(mm.created_at)'))
                    ->get();

                foreach ($topIgrejaIdsEvolucao as $igrejaChartId) {
                    $igrejaNome = (string) optional($topIgrejas->firstWhere('id', $igrejaChartId))->nome;
                    $serie = array_fill(1, 12, 0);
                    foreach ($evolucaoRows as $row) {
                        if ((int) $row->igreja_id === (int) $igrejaChartId) {
                            $serie[(int) $row->mes] = (int) $row->total;
                        }
                    }

                    $distritoEvolucaoDatasets[] = [
                        'label' => $igrejaNome ?: ('Igreja #' . $igrejaChartId),
                        'data' => array_values($serie),
                    ];
                }
            }

            $entradasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_recepcao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereYear('dt_recepcao', $anoDistrito)
                ->groupBy(DB::raw('MONTH(dt_recepcao)'))
                ->pluck('total', 'mes')
                ->toArray();

            foreach ($entradasRows as $mes => $total) {
                $distritoEntradasPorMes[(int) $mes] = (int) $total;
            }

            $saidasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_exclusao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereNotNull('dt_exclusao')
                ->whereYear('dt_exclusao', $anoDistrito)
                ->groupBy(DB::raw('MONTH(dt_exclusao)'))
                ->pluck('total', 'mes')
                ->toArray();

            foreach ($saidasRows as $mes => $total) {
                $distritoSaidasPorMes[(int) $mes] = (int) $total;
            }

            $vinculoAgg = DB::table('membresia_membros as mm')
                ->select(
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'M' THEN 1 ELSE 0 END) as membros"),
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'C' THEN 1 ELSE 0 END) as congregados"),
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'V' THEN 1 ELSE 0 END) as visitantes")
                )
                ->where('mm.distrito_id', $igrejaId)
                ->whereYear('mm.created_at', $anoDistrito)
                ->first();

            $distritoVinculosTotais = [
                (int) ($vinculoAgg->membros ?? 0),
                (int) ($vinculoAgg->congregados ?? 0),
                (int) ($vinculoAgg->visitantes ?? 0),
            ];

            $sexoAgg = DB::table('membresia_membros as mm')
                ->select(
                    DB::raw("SUM(CASE WHEN mm.sexo = 'M' THEN 1 ELSE 0 END) as masculino"),
                    DB::raw("SUM(CASE WHEN mm.sexo = 'F' THEN 1 ELSE 0 END) as feminino"),
                    DB::raw("SUM(CASE WHEN mm.sexo IS NULL OR mm.sexo = '' OR mm.sexo NOT IN ('M','F') THEN 1 ELSE 0 END) as nao_informado")
                )
                ->where('mm.distrito_id', $igrejaId)
                ->where('mm.vinculo', 'M')
                ->whereYear('mm.created_at', $anoDistrito)
                ->first();

            $distritoSexoTotais = [
                (int) ($sexoAgg->masculino ?? 0),
                (int) ($sexoAgg->feminino ?? 0),
                (int) ($sexoAgg->nao_informado ?? 0),
            ];

            $statusRolAgg = DB::table('membresia_rolpermanente as mr')
                ->select(
                    DB::raw("SUM(CASE WHEN mr.status = 'A' THEN 1 ELSE 0 END) as ativos"),
                    DB::raw("SUM(CASE WHEN mr.status = 'I' THEN 1 ELSE 0 END) as inativos")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->where('mr.lastrec', 1)
                ->whereYear('mr.dt_recepcao', $anoDistrito)
                ->first();

            $distritoStatusRolTotais = [
                (int) ($statusRolAgg->ativos ?? 0),
                (int) ($statusRolAgg->inativos ?? 0),
            ];

            $saldoAcumulado = 0;
            for ($mes = 1; $mes <= 12; $mes++) {
                $saldoAcumulado += ((int) $distritoEntradasPorMes[$mes]) - ((int) $distritoSaidasPorMes[$mes]);
                $distritoCrescimentoAcumulado[$mes] = $saldoAcumulado;
            }

            $rankingCrescimento = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN YEAR(mr.dt_recepcao) = {$anoDistrito} THEN 1 ELSE 0 END) - SUM(CASE WHEN mr.dt_exclusao IS NOT NULL AND YEAR(mr.dt_exclusao) = {$anoDistrito} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $distritoCrescimentoIgrejasLabels = $rankingCrescimento->pluck('nome')->values()->all();
            $distritoCrescimentoIgrejasTotais = $rankingCrescimento->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $rankingEntradas = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN YEAR(mr.dt_recepcao) = {$anoDistrito} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $distritoEntradasIgrejasLabels = $rankingEntradas->pluck('nome')->values()->all();
            $distritoEntradasIgrejasTotais = $rankingEntradas->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $rankingSaidas = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN mr.dt_exclusao IS NOT NULL AND YEAR(mr.dt_exclusao) = {$anoDistrito} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $distritoSaidasIgrejasLabels = $rankingSaidas->pluck('nome')->values()->all();
            $distritoSaidasIgrejasTotais = $rankingSaidas->pluck('total')->map(fn ($v) => (int) $v)->values()->all();
        }

        
        return view('dashboard', [
            'activeMembrosCount' => $activeMembrosCount,
            'activeCongregadosCount' => $activeCongregadosCount,
            'activeVisitantesCount' => $activeVisitantesCount,
            'totalAtivos' => $totalAtivos,
            'totalInativos' => $totalInativos,
            'visitantesPorMes' => $visitantesPorMesCompleto,
            'congregadosPorMes' => $congregadosPorMesCompleto,
            'membrosPorMes' => $membrosPorMesCompleto,
            'entradasRolPorMes' => $rolData['entradas'],
            'saidasRolPorMes' => $rolData['saidas'],
            'crescimentoLiquidoRolPorMes' => $rolData['crescimento'],
            'entradasFinanceiroPorMes' => $financeiroData['entradas'],
            'saidasFinanceiroPorMes' => $financeiroData['saidas'],
            'anoVisitantes' => $anoVisitantes,
            'anoRol' => $anoRol,
            'anoFinanceiro' => $anoFinanceiro,
            'anoDistrito' => $anoDistrito,
            'sexoMembresia' => $sexoMembresia,
            'statusMembresia' => $statusMembresia,
            'anosDisponiveis' => $anosDisponiveis,
            'distritoEvolucaoDatasets' => $distritoEvolucaoDatasets,
            'distritoEntradasPorMes' => $distritoEntradasPorMes,
            'distritoSaidasPorMes' => $distritoSaidasPorMes,
            'distritoTopIgrejasLabels' => $distritoTopIgrejasLabels,
            'distritoTopIgrejasTotais' => $distritoTopIgrejasTotais,
            'distritoVinculosTotais' => $distritoVinculosTotais,
            'distritoSexoTotais' => $distritoSexoTotais,
            'distritoStatusRolTotais' => $distritoStatusRolTotais,
            'distritoCrescimentoAcumulado' => $distritoCrescimentoAcumulado,
            'distritoCrescimentoIgrejasLabels' => $distritoCrescimentoIgrejasLabels,
            'distritoCrescimentoIgrejasTotais' => $distritoCrescimentoIgrejasTotais,
            'distritoEntradasIgrejasLabels' => $distritoEntradasIgrejasLabels,
            'distritoEntradasIgrejasTotais' => $distritoEntradasIgrejasTotais,
            'distritoSaidasIgrejasLabels' => $distritoSaidasIgrejasLabels,
            'distritoSaidasIgrejasTotais' => $distritoSaidasIgrejasTotais,
            'distritoIgrejas' => $distritoIgrejas,
            'instituicao' => $instituicao
        ]);
    }

    public function chartData(Request $request)
    {
        $igrejaId = (int) session()->get('session_perfil')->instituicao_id;
        $instituicao = InstituicoesInstituicao::with('tipoInstituicao')->find($igrejaId);
        $perfilSigla = $instituicao->tipoInstituicao->sigla ?? null;
        $anoAtual = (int) Carbon::now()->year;
        $ano = $this->sanitizeAno($request->input('ano', $anoAtual), $anoAtual);
        $sexo = $this->sanitizeSexo($request->input('sexo'));
        $status = $this->sanitizeStatus($request->input('status'));
        $chart = (string) $request->input('chart', '');

        $allowedCharts = [
            'visitantes',
            'rol_entradas_saidas',
            'rol_crescimento',
            'financeiro',
            'distrito_evolucao',
            'distrito_entradas_saidas',
            'distrito_top_igrejas',
            'distrito_vinculos',
            'distrito_sexo_membros',
            'distrito_status_rol',
            'distrito_crescimento_acumulado',
            'distrito_crescimento_igrejas',
            'distrito_entradas_igrejas',
            'distrito_saidas_igrejas',
        ];
        if (!in_array($chart, $allowedCharts, true)) {
            return response()->json(['message' => 'Grafico invalido.'], 422);
        }

        if (str_starts_with($chart, 'distrito_') && $perfilSigla !== 'D') {
            return response()->json(['message' => 'Grafico indisponivel para este perfil.'], 403);
        }
        $igrejaDistritoId = str_starts_with($chart, 'distrito_')
            ? $this->sanitizeIgrejaDistritoId($request->input('igreja_id'), $igrejaId)
            : null;

        $labelsMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        if ($chart === 'visitantes') {
            $visitantes = $this->buildVinculoPorMes($igrejaId, $ano, 'V', $sexo, $status);
            $congregados = $this->buildVinculoPorMes($igrejaId, $ano, 'C', $sexo, $status);
            $membros = $this->buildVinculoPorMes($igrejaId, $ano, 'M', $sexo, $status);

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'sexo' => $sexo,
                'status' => $status,
                'labels' => $labelsMeses,
                'datasets' => [
                    [
                        'label' => 'Visitantes',
                        'data' => array_values($visitantes),
                    ],
                    [
                        'label' => 'Congregados',
                        'data' => array_values($congregados),
                    ],
                    [
                        'label' => 'Membros',
                        'data' => array_values($membros),
                    ],
                ],
            ]);
        }

        if ($chart === 'rol_entradas_saidas') {
            $rolData = $this->buildRolPorMes($igrejaId, $ano);

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $labelsMeses,
                'datasets' => [
                    [
                        'label' => 'Entradas',
                        'data' => array_values($rolData['entradas']),
                    ],
                    [
                        'label' => 'Saidas',
                        'data' => array_values($rolData['saidas']),
                    ],
                ],
            ]);
        }

        if ($chart === 'rol_crescimento') {
            $rolData = $this->buildRolPorMes($igrejaId, $ano);

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $labelsMeses,
                'datasets' => [
                    [
                        'label' => 'Crescimento Liquido',
                        'data' => array_values($rolData['crescimento']),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_evolucao') {
            $topIgrejas = DB::table('membresia_membros as mm')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mm.igreja_id')
                ->select('ii.id', 'ii.nome', DB::raw('COUNT(mm.id) as total'))
                ->where('mm.distrito_id', $igrejaId)
                ->where('mm.vinculo', 'M')
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mm.igreja_id', $igrejaDistritoId))
                ->groupBy('ii.id', 'ii.nome')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $topIgrejaIds = $topIgrejas->pluck('id')->values()->all();
            $datasets = [];

            if (!empty($topIgrejaIds)) {
                $evolucaoRows = DB::table('membresia_membros as mm')
                    ->select(
                        'mm.igreja_id',
                        DB::raw('MONTH(mm.created_at) as mes'),
                        DB::raw('COUNT(mm.id) as total')
                    )
                    ->whereIn('mm.igreja_id', $topIgrejaIds)
                    ->where('mm.vinculo', 'M')
                    ->whereYear('mm.created_at', $ano)
                    ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mm.igreja_id', $igrejaDistritoId))
                    ->groupBy('mm.igreja_id', DB::raw('MONTH(mm.created_at)'))
                    ->get();

                foreach ($topIgrejaIds as $igrejaChartId) {
                    $igrejaNome = (string) optional($topIgrejas->firstWhere('id', $igrejaChartId))->nome;
                    $serie = array_fill(1, 12, 0);
                    foreach ($evolucaoRows as $row) {
                        if ((int) $row->igreja_id === (int) $igrejaChartId) {
                            $serie[(int) $row->mes] = (int) $row->total;
                        }
                    }

                    $datasets[] = [
                        'label' => $igrejaNome ?: ('Igreja #' . $igrejaChartId),
                        'data' => array_values($serie),
                    ];
                }
            }

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $labelsMeses,
                'datasets' => $datasets,
            ]);
        }

        if ($chart === 'distrito_entradas_saidas') {
            $entradasPorMes = array_fill(1, 12, 0);
            $saidasPorMes = array_fill(1, 12, 0);

            $entradasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_recepcao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereYear('dt_recepcao', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('igreja_id', $igrejaDistritoId))
                ->groupBy(DB::raw('MONTH(dt_recepcao)'))
                ->pluck('total', 'mes')
                ->toArray();

            foreach ($entradasRows as $mes => $total) {
                $entradasPorMes[(int) $mes] = (int) $total;
            }

            $saidasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_exclusao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereNotNull('dt_exclusao')
                ->whereYear('dt_exclusao', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('igreja_id', $igrejaDistritoId))
                ->groupBy(DB::raw('MONTH(dt_exclusao)'))
                ->pluck('total', 'mes')
                ->toArray();

            foreach ($saidasRows as $mes => $total) {
                $saidasPorMes[(int) $mes] = (int) $total;
            }

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $labelsMeses,
                'datasets' => [
                    [
                        'label' => 'Entradas',
                        'data' => array_values($entradasPorMes),
                    ],
                    [
                        'label' => 'Saidas',
                        'data' => array_values($saidasPorMes),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_top_igrejas') {
            $topIgrejas = DB::table('membresia_membros as mm')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mm.igreja_id')
                ->select('ii.nome', DB::raw('COUNT(mm.id) as total'))
                ->where('mm.distrito_id', $igrejaId)
                ->where('mm.vinculo', 'M')
                ->whereYear('mm.created_at', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mm.igreja_id', $igrejaDistritoId))
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $topIgrejas->pluck('nome')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Membros',
                        'data' => $topIgrejas->pluck('total')->map(fn ($v) => (int) $v)->values()->all(),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_vinculos') {
            $vinculoAgg = DB::table('membresia_membros as mm')
                ->select(
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'M' THEN 1 ELSE 0 END) as membros"),
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'C' THEN 1 ELSE 0 END) as congregados"),
                    DB::raw("SUM(CASE WHEN mm.vinculo = 'V' THEN 1 ELSE 0 END) as visitantes")
                )
                ->where('mm.distrito_id', $igrejaId)
                ->whereYear('mm.created_at', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mm.igreja_id', $igrejaDistritoId))
                ->first();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => ['Membros', 'Congregados', 'Visitantes'],
                'datasets' => [
                    [
                        'label' => 'Cadastros',
                        'data' => [
                            (int) ($vinculoAgg->membros ?? 0),
                            (int) ($vinculoAgg->congregados ?? 0),
                            (int) ($vinculoAgg->visitantes ?? 0),
                        ],
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_sexo_membros') {
            $sexoAgg = DB::table('membresia_membros as mm')
                ->select(
                    DB::raw("SUM(CASE WHEN mm.sexo = 'M' THEN 1 ELSE 0 END) as masculino"),
                    DB::raw("SUM(CASE WHEN mm.sexo = 'F' THEN 1 ELSE 0 END) as feminino"),
                    DB::raw("SUM(CASE WHEN mm.sexo IS NULL OR mm.sexo = '' OR mm.sexo NOT IN ('M','F') THEN 1 ELSE 0 END) as nao_informado")
                )
                ->where('mm.distrito_id', $igrejaId)
                ->where('mm.vinculo', 'M')
                ->whereYear('mm.created_at', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mm.igreja_id', $igrejaDistritoId))
                ->first();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => ['Masculino', 'Feminino', 'Não informado'],
                'datasets' => [
                    [
                        'label' => 'Membros',
                        'data' => [
                            (int) ($sexoAgg->masculino ?? 0),
                            (int) ($sexoAgg->feminino ?? 0),
                            (int) ($sexoAgg->nao_informado ?? 0),
                        ],
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_status_rol') {
            $statusRolAgg = DB::table('membresia_rolpermanente as mr')
                ->select(
                    DB::raw("SUM(CASE WHEN mr.status = 'A' THEN 1 ELSE 0 END) as ativos"),
                    DB::raw("SUM(CASE WHEN mr.status = 'I' THEN 1 ELSE 0 END) as inativos")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->where('mr.lastrec', 1)
                ->whereYear('mr.dt_recepcao', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mr.igreja_id', $igrejaDistritoId))
                ->first();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => ['Ativos', 'Inativos'],
                'datasets' => [
                    [
                        'label' => 'Membros no Rol',
                        'data' => [
                            (int) ($statusRolAgg->ativos ?? 0),
                            (int) ($statusRolAgg->inativos ?? 0),
                        ],
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_crescimento_acumulado') {
            $entradasPorMes = array_fill(1, 12, 0);
            $saidasPorMes = array_fill(1, 12, 0);
            $crescimentoAcumulado = array_fill(1, 12, 0);

            $entradasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_recepcao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereYear('dt_recepcao', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('igreja_id', $igrejaDistritoId))
                ->groupBy(DB::raw('MONTH(dt_recepcao)'))
                ->pluck('total', 'mes')
                ->toArray();
            foreach ($entradasRows as $mes => $total) {
                $entradasPorMes[(int) $mes] = (int) $total;
            }

            $saidasRows = DB::table('membresia_rolpermanente')
                ->select(DB::raw('MONTH(dt_exclusao) as mes'), DB::raw('COUNT(id) as total'))
                ->where('distrito_id', $igrejaId)
                ->whereNotNull('dt_exclusao')
                ->whereYear('dt_exclusao', $ano)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('igreja_id', $igrejaDistritoId))
                ->groupBy(DB::raw('MONTH(dt_exclusao)'))
                ->pluck('total', 'mes')
                ->toArray();
            foreach ($saidasRows as $mes => $total) {
                $saidasPorMes[(int) $mes] = (int) $total;
            }

            $saldoAcumulado = 0;
            for ($mes = 1; $mes <= 12; $mes++) {
                $saldoAcumulado += ((int) $entradasPorMes[$mes]) - ((int) $saidasPorMes[$mes]);
                $crescimentoAcumulado[$mes] = $saldoAcumulado;
            }

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $labelsMeses,
                'datasets' => [
                    [
                        'label' => 'Crescimento Líquido Acumulado',
                        'data' => array_values($crescimentoAcumulado),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_crescimento_igrejas') {
            $rankingCrescimento = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN YEAR(mr.dt_recepcao) = {$ano} THEN 1 ELSE 0 END) - SUM(CASE WHEN mr.dt_exclusao IS NOT NULL AND YEAR(mr.dt_exclusao) = {$ano} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mr.igreja_id', $igrejaDistritoId))
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $rankingCrescimento->pluck('nome')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Saldo',
                        'data' => $rankingCrescimento->pluck('total')->map(fn ($v) => (int) $v)->values()->all(),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_entradas_igrejas') {
            $rankingEntradas = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN YEAR(mr.dt_recepcao) = {$ano} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mr.igreja_id', $igrejaDistritoId))
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $rankingEntradas->pluck('nome')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Entradas',
                        'data' => $rankingEntradas->pluck('total')->map(fn ($v) => (int) $v)->values()->all(),
                    ],
                ],
            ]);
        }

        if ($chart === 'distrito_saidas_igrejas') {
            $rankingSaidas = DB::table('membresia_rolpermanente as mr')
                ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
                ->select(
                    'ii.nome',
                    DB::raw("SUM(CASE WHEN mr.dt_exclusao IS NOT NULL AND YEAR(mr.dt_exclusao) = {$ano} THEN 1 ELSE 0 END) as total")
                )
                ->where('mr.distrito_id', $igrejaId)
                ->when($igrejaDistritoId !== null, fn ($query) => $query->where('mr.igreja_id', $igrejaDistritoId))
                ->groupBy('ii.nome')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            return response()->json([
                'chart' => $chart,
                'ano' => $ano,
                'labels' => $rankingSaidas->pluck('nome')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Saídas',
                        'data' => $rankingSaidas->pluck('total')->map(fn ($v) => (int) $v)->values()->all(),
                    ],
                ],
            ]);
        }

        $financeiroData = $this->buildFinanceiroPorMes($igrejaId, $ano);

        return response()->json([
            'chart' => $chart,
            'ano' => $ano,
            'labels' => $labelsMeses,
            'datasets' => [
                [
                    'label' => 'Entradas (R$)',
                    'data' => array_values($financeiroData['entradas']),
                ],
                [
                    'label' => 'Saidas (R$)',
                    'data' => array_values($financeiroData['saidas']),
                ],
            ],
        ]);
    }


    public function selecionarPerfil()
    {
        // Obter o ID do usuário autenticado
        $userID = Auth::id();

        // Consultar as Instituicoes dos Usuários Autenticados
        $perfils = PerfilUser::where('user_id', $userID)
            ->join('instituicoes_instituicoes', 'instituicoes_instituicoes.id', '=', 'perfil_user.instituicao_id')
            ->join('perfils', 'perfils.id', '=', 'perfil_user.perfil_id')
            ->select(
                'instituicoes_instituicoes.id as instituicao_id',
                'instituicoes_instituicoes.nome as instituicao_nome',
                'perfils.id as perfil_id',
                'perfils.nome as perfil_nome'
            )
            ->get();

        return view('selecionarPerfil', ['perfils' => $perfils]);
    }

    public function postPerfil(Request $request)
    {
        if ($request->has('instituicao_id')) {

            $perfil = app(IdentificaPerfilService::class)->execute(
                $request->instituicao_id,
                $request->instituicao_nome,
                $request->perfil_id,
                $request->perfil_nome,
            );

            session(['session_perfil' => $perfil]);

            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'A seleção de um perfil é obrigatória. Por favor, selecione.');
        }
    }

    private function sanitizeAno($value, int $anoAtual): int
    {
        $ano = (int) $value;
        if ($ano < 2000 || $ano > ($anoAtual + 1)) {
            return $anoAtual;
        }

        return $ano;
    }

    private function sanitizeSexo($value): ?string
    {
        $sexo = strtoupper(trim((string) $value));
        if ($sexo === 'M' || $sexo === 'F') {
            return $sexo;
        }

        return null;
    }

    private function sanitizeStatus($value): ?string
    {
        $status = strtoupper(trim((string) $value));
        if ($status === 'A' || $status === 'I') {
            return $status;
        }

        return null;
    }

    private function sanitizeIgrejaDistritoId($value, int $distritoId): ?int
    {
        $igrejaId = (int) $value;
        if ($igrejaId <= 0) {
            return null;
        }

        $exists = DB::table('membresia_membros')
            ->where('distrito_id', $distritoId)
            ->where('igreja_id', $igrejaId)
            ->exists();

        return $exists ? $igrejaId : null;
    }

    private function buildVinculoPorMes(int $igrejaId, int $ano, string $vinculo, ?string $sexo = null, ?string $status = null): array
    {
        $query = MembresiaMembro::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('vinculo', $vinculo)
            ->where('igreja_id', $igrejaId)
            ->whereYear('created_at', $ano)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'));

        if ($sexo !== null) {
            $query->where('sexo', $sexo);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $resultByMonth = $query->get()
            ->pluck('count', 'month')
            ->toArray();

        $fullResult = array_fill(1, 12, 0);
        foreach ($resultByMonth as $month => $count) {
            $fullResult[(int) $month] = (int) $count;
        }

        return $fullResult;
    }

    private function buildRolPorMes(int $igrejaId, int $ano): array
    {
        $entradasRolPorMes = DB::table('membresia_rolpermanente')
            ->select(
                DB::raw('MONTH(dt_recepcao) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('igreja_id', $igrejaId)
            ->whereYear('dt_recepcao', $ano)
            ->groupBy(DB::raw('MONTH(dt_recepcao)'))
            ->orderBy(DB::raw('MONTH(dt_recepcao)'))
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $saidasRolPorMes = DB::table('membresia_rolpermanente')
            ->select(
                DB::raw('MONTH(dt_exclusao) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('igreja_id', $igrejaId)
            ->whereNotNull('dt_exclusao')
            ->whereYear('dt_exclusao', $ano)
            ->groupBy(DB::raw('MONTH(dt_exclusao)'))
            ->orderBy(DB::raw('MONTH(dt_exclusao)'))
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $entradas = array_fill(1, 12, 0);
        foreach ($entradasRolPorMes as $month => $count) {
            $entradas[(int) $month] = (int) $count;
        }

        $saidas = array_fill(1, 12, 0);
        foreach ($saidasRolPorMes as $month => $count) {
            $saidas[(int) $month] = (int) $count;
        }

        $crescimento = array_fill(1, 12, 0);
        for ($mes = 1; $mes <= 12; $mes++) {
            $crescimento[$mes] = $entradas[$mes] - $saidas[$mes];
        }

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'crescimento' => $crescimento,
        ];
    }

    private function buildFinanceiroPorMes(int $igrejaId, int $ano): array
    {
        $entradasFinanceiroPorMes = DB::table('financeiro_lancamentos')
            ->select(
                DB::raw('MONTH(data_lancamento) as month'),
                DB::raw('SUM(valor) as total')
            )
            ->where('instituicao_id', $igrejaId)
            ->where('tipo_lancamento', FinanceiroPlanoConta::TP_ENTRADA)
            ->whereYear('data_lancamento', $ano)
            ->groupBy(DB::raw('MONTH(data_lancamento)'))
            ->orderBy(DB::raw('MONTH(data_lancamento)'))
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $saidasFinanceiroPorMes = DB::table('financeiro_lancamentos')
            ->select(
                DB::raw('MONTH(data_lancamento) as month'),
                DB::raw('SUM(valor) as total')
            )
            ->where('instituicao_id', $igrejaId)
            ->where('tipo_lancamento', FinanceiroPlanoConta::TP_SAIDA)
            ->whereYear('data_lancamento', $ano)
            ->groupBy(DB::raw('MONTH(data_lancamento)'))
            ->orderBy(DB::raw('MONTH(data_lancamento)'))
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $entradas = array_fill(1, 12, 0);
        foreach ($entradasFinanceiroPorMes as $month => $total) {
            $entradas[(int) $month] = (float) $total;
        }

        $saidas = array_fill(1, 12, 0);
        foreach ($saidasFinanceiroPorMes as $month => $total) {
            $saidas[(int) $month] = (float) $total;
        }

        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
        ];
    }
}
