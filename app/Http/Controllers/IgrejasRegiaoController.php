<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\DataTables\IgrejasRegiaoDataTable;
use App\Services\ServiceIgrejas\BalanceteService;
use App\Services\ServiceIgrejas\GetEstatisticaAnoEclesiasticoService;
use App\Services\ServiceIgrejas\GetEstatisticaAnoEclesiasticoTodosService;
use App\Services\ServiceIgrejas\LivroRazaoService;
use App\Services\ServiceIgrejas\MovimentoDiarioService;
use App\Traits\LocationUtils;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IgrejasRegiaoController extends Controller
{
    use LocationUtils;

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

    public function pesquisarMembro(Request $request)
    {
        $nome = trim((string) $request->input('nome'));
        $cpf = preg_replace('/\D/', '', (string) $request->input('cpf'));
        $searched = $nome !== '' || $cpf !== '';
        $membros = collect();

        if ($searched) {
            $sessionPerfil = session('session_perfil');
            $regiaoId = (int) data_get($sessionPerfil, 'instituicoes.regiao.id', data_get($sessionPerfil, 'instituicao_id', 0));

            $membros = DB::table('membresia_membros as mm')
                ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'mm.distrito_id')
                ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
                ->where('mm.vinculo', 'M')
                ->whereIn('mm.status', ['A', 'I'])
                ->where(function ($query) use ($regiaoId) {
                    $query->where('mm.regiao_id', $regiaoId)
                        ->orWhere('distrito.instituicao_pai_id', $regiaoId)
                        ->orWhere('igreja.regiao_id', $regiaoId);
                })
                ->when($nome !== '', fn ($query) =>
                    $query->where('mm.nome', 'like', '%' . $nome . '%'))
                ->when($cpf !== '', fn ($query) =>
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(mm.cpf, '.', ''), '-', ''), '/', ''), ' ', '') LIKE ?", ['%' . $cpf . '%']))
                ->select([
                    'mm.id',
                    'mm.nome',
                    'mm.cpf',
                    'mm.status',
                    'distrito.nome as distrito_nome',
                    'igreja.nome as igreja_nome',
                    DB::raw("(SELECT CASE
                        WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                        WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                        ELSE mc.telefone_whatsapp
                    END FROM membresia_contatos mc
                    WHERE mc.membro_id = mm.id AND mc.deleted_at IS NULL
                    ORDER BY mc.id DESC
                    LIMIT 1) as telefone"),
                ])
                ->orderBy('mm.nome')
                ->limit(100)
                ->get();
        }

        return view('igrejas-regiao.pesquisar-membro', compact('membros', 'searched', 'nome', 'cpf'));
    }

    public function estatisticaAnoEclesiastico(Request $request, InstituicoesInstituicao $igreja)
    {
        try {
            $data = app(GetEstatisticaAnoEclesiasticoService::class)->execute($igreja, $request->input('ano'));
            return view('igrejas-regiao.estatistica-ano-eclesiastico', $data);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', __('Erro') . $e->getMessage());
        }
    }

    public function estatisticaAnoEclesiasticoTodos(Request $request, InstituicoesInstituicao $igreja)
    {
        try {
            $data = app(GetEstatisticaAnoEclesiasticoTodosService::class)->execute($igreja, $request->input('ano'));
            return view('igrejas-regiao.estatistica-ano-eclesiastico', $data);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', __('Erro') . $e->getMessage());
        }
    }

    public function balancete(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(BalanceteService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        return view('igrejas-regiao.balancete', $data);
    }

    public function balancetePdf(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(BalanceteService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.balancete_pdf', $data);
        return $pdf->stream('balancete.pdf');
    }

    public function movimentoDiario(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(MovimentoDiarioService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        return view('igrejas.movimentodiario', $data);
    }

    public function movimentoDiarioPdf(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $caixaId = $request->input('caixa_id');

        $data = app(MovimentoDiarioService::class)->execute($dataInicial, $dataFinal, $caixaId, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.movimento-diario-pdf', $data);
        return $pdf->stream('relatorio_movimento_diario.pdf');
    }

    public function livrorazao(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        // Chama o serviço para obter os dados necessários
        $data = app(LivroRazaoService::class)->execute($dataInicial, $dataFinal, $igreja);
        return view('igrejas.livrorazao', $data);
    }

    public function livrorazaoPdf(Request $request, InstituicoesInstituicao $igreja)
    {
        $this->assertIgrejaLocal($igreja);

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        // Chama o serviço para obter os dados necessários
        $data = app(LivroRazaoService::class)->execute($dataInicial, $dataFinal, $igreja);
        $pdf = FacadePdf::loadView('financeiro.relatorios.livrorazao_pdf', $data);
        return $pdf->stream('relatorio_livrorazao.pdf');
    }

    private function assertIgrejaLocal(InstituicoesInstituicao $igreja): void
    {
        abort_if((int) $igreja->tipo_instituicao_id !== InstituicoesTipoInstituicao::IGREJA_LOCAL, 404);
    }
}
