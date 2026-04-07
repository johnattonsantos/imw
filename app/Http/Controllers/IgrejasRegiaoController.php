<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use App\DataTables\IgrejasRegiaoDataTable;
use App\Services\ServiceIgrejas\BalanceteService;
use App\Services\ServiceIgrejas\GetEstatisticaAnoEclesiasticoService;
use App\Services\ServiceIgrejas\GetEstatisticaAnoEclesiasticoTodosService;
use App\Services\ServiceIgrejas\LivroRazaoService;
use App\Services\ServiceIgrejas\MovimentoDiarioService;
use App\Traits\LocationUtils;
use App\Traits\RegionalScope;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;

class IgrejasRegiaoController extends Controller
{
    use LocationUtils;
    use RegionalScope;

    public function index()
    {
        return view('igrejas-regiao.index');
    }

    public function list(Request $request)
    {
        try {
            return app(IgrejasRegiaoDataTable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível listar as igrejas'], 500);
        }
    }
    public function estatisticaAnoEclesiastico(Request $request, InstituicoesInstituicao $igreja)
    {
        try {
            if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
                return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
            }
            $data = app(GetEstatisticaAnoEclesiasticoService::class)->execute($igreja, $request->input('ano'));
            return view('igrejas-regiao.estatistica-ano-eclesiastico', $data);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Erro' . $e->getMessage());
        }
    }

    public function estatisticaAnoEclesiasticoTodos(Request $request, InstituicoesInstituicao $igreja)
    {
        try {
            if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
                return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
            }
            $data = app(GetEstatisticaAnoEclesiasticoTodosService::class)->execute($igreja, $request->input('ano'));
            return view('igrejas-regiao.estatistica-ano-eclesiastico', $data);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Erro' . $e->getMessage());
        }
    }

    public function balancete(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(BalanceteService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        return view('igrejas-regiao.balancete', $data);
    }

    public function balancetePdf(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(BalanceteService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.balancete_pdf', $data);
        return $pdf->stream('balancete.pdf');
    }

    public function movimentoDiario(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(MovimentoDiarioService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        return view('igrejas.movimentodiario', $data);
    }

    public function movimentoDiarioPdf(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(MovimentoDiarioService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.movimento-diario-pdf', $data);
        return $pdf->stream('relatorio_movimento_diario.pdf');
    }

    public function livrorazao(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        // Chama o serviço para obter os dados necessários
        $data = app(LivroRazaoService::class)->execute($dataInicial, $dataFinal, $igreja);
        return view('igrejas.livrorazao', $data);
    }

    public function livrorazaoPdf(Request $request, InstituicoesInstituicao $igreja)
    {
        if (!$this->instituicaoPertenceRegiao((int) $igreja->id, $this->sessionRegiaoId())) {
            return redirect()->back()->with('error', 'Igreja fora da região do perfil.');
        }

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        // Chama o serviço para obter os dados necessários
        $data = app(LivroRazaoService::class)->execute($dataInicial, $dataFinal, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.livrorazao_pdf', $data);
        return $pdf->stream('relatorio_livrorazao.pdf');
    }
}
