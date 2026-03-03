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
        $anoAtual = (int) Carbon::now()->year;
        $sanitizeAno = fn ($value) => $this->sanitizeAno($value, $anoAtual);

        $anoVisitantes = $sanitizeAno($request->input('ano_visitantes', $anoAtual));
        $anoRol = $sanitizeAno($request->input('ano_rol', $anoAtual));
        $anoFinanceiro = $sanitizeAno($request->input('ano_financeiro', $anoAtual));
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

        $instituicao = InstituicoesInstituicao::where('id', session()->get('session_perfil')->instituicao_id)->first();

        
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
            'sexoMembresia' => $sexoMembresia,
            'statusMembresia' => $statusMembresia,
            'anosDisponiveis' => $anosDisponiveis,
            'instituicao' => $instituicao
        ]);
    }

    public function chartData(Request $request)
    {
        $igrejaId = (int) session()->get('session_perfil')->instituicao_id;
        $anoAtual = (int) Carbon::now()->year;
        $ano = $this->sanitizeAno($request->input('ano', $anoAtual), $anoAtual);
        $sexo = $this->sanitizeSexo($request->input('sexo'));
        $status = $this->sanitizeStatus($request->input('status'));
        $chart = (string) $request->input('chart', '');

        $allowedCharts = ['visitantes', 'rol_entradas_saidas', 'rol_crescimento', 'financeiro'];
        if (!in_array($chart, $allowedCharts, true)) {
            return response()->json(['message' => 'Grafico invalido.'], 422);
        }

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
