<?php

namespace App\Http\Controllers;

use App\Services\ServiceIgrejas\CnpjIgreja;
use App\Services\ServiceIgrejas\ContaBancariaIgreja;
use App\Services\ServiceIgrejas\ContatoIgreja;
use App\Services\ServiceIgrejas\GetEstatisticaAnoEclesiasticoTodosService;
use App\Services\ServiceIgrejas\Igrejas;
use App\Services\ServiceRegiaoRelatorios\EstatisticaEscolaridadeService;
use App\Services\ServiceRegiaoRelatorios\EstatisticaEstadoCivilService;
use App\Services\ServiceRegiaoRelatorios\EstatisticaGeneroPorcentagemService;
use App\Services\ServiceRegiaoRelatorios\EstatisticaGeneroService;
use App\Services\ServiceRegiaoRelatorios\EstatisticaTotalMembrosService;
use App\Services\ServiceRegiaoRelatorios\LancamentoIgrejasService;
use App\Services\ServiceRegiaoRelatorios\LivroRazaoGeralService;
use App\Services\ServiceRegiaoRelatorios\FinanceiroPorCategoriaService;
use App\Services\ServiceRegiaoRelatorios\IgrejasPorClerigosService;
use App\Services\ServiceRegiaoRelatorios\IgrejasPorPastoresService;
use App\Services\ServiceRegiaoRelatorios\MembrosMinisterioService;
use App\Services\ServiceRegiaoRelatorios\OrcamentoService;
use App\Services\ServiceRegiaoRelatorios\QuantidadeMembrosService;
use App\Services\ServiceRegiaoRelatorios\SaldoIgrejasService;
use App\Services\ServiceRegiaoRelatorios\VariacaoFinanceiraService;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoAniversariantes;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoCategorias;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoDados;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoEsposas;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoStatus;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class RegiaoRelatorioController extends Controller
{
    //Membresia
    public function membrosministerio(Request $request)
    {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(MembrosMinisterioService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);
        return view('regiao.relatorios.membrosministerio', $data);
    }

    public function membrosministerioPdf(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(MembrosMinisterioService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);

        $pdf = FacadePdf::loadView('regiao.relatorios.membrosministerio_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_membrosministerio.pdf' . date('YmdHis'));
    }


    public function quantidademembros(Request $request)
    {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(QuantidadeMembrosService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);
        return view('regiao.relatorios.quantidademembros', $data);
    }

    public function quantidademembrosPdf(Request $request)
    {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(QuantidadeMembrosService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);
        $pdf = FacadePdf::loadView('regiao.relatorios.quantidademembros_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_quantidademembros.pdf' . date('YmdHis'));
    }


    public function estatisticagenero(Request $request)
    {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(EstatisticaGeneroService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);
        return view('regiao.relatorios.estatisticagenero', $data);
    }

    public function estatisticageneroPdf(Request $request)
    {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $distritoId = $request->input('distrito');

        $data = app(EstatisticaGeneroService::class)->execute($dataInicial, $dataFinal, $tipo, $distritoId);

        $pdf = FacadePdf::loadView('regiao.relatorios.estatisticagenero_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticagenero.pdf' . date('YmdHis'));
    }
    //Escorlaridade
    public function estatisticaescolaridade(Request $request)
    {

        $distritoId = $request->input('distrito');


        $data = app(EstatisticaEscolaridadeService::class)->execute($distritoId);


        return view('regiao.estatisticas.estatisticaescolaridade', $data);
    }

    public function estatisticaescolaridadePdf(Request $request)
    {
        $distritoId = $request->input('distrito');
        $data = app(EstatisticaEscolaridadeService::class)->execute($distritoId);

        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticaescolaridade_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticaescolaridade.pdf' . date('YmdHis'));
    }
    public function estatisticaestadocivil(Request $request)
    {

        $distritoId = $request->input('distrito');


        $data = app(EstatisticaEstadoCivilService::class)->execute($distritoId );


        return view('regiao.estatisticas.estatisticaestadocivil', $data);
    }

    public function estatisticaestadocivilPdf(Request $request)
    {
        $distritoId = $request->input('distrito');


        $data = app(EstatisticaEstadoCivilService::class)->execute($distritoId);

        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticaestadocivil_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticaestadocivil.pdf' . date('YmdHis'));
    }
    public function estatisticageneroporcentagem(Request $request)
    {

        $distritoId = $request->input('distrito');


        $data = app(EstatisticaGeneroPorcentagemService::class)->execute($distritoId );


        return view('regiao.estatisticas.estatisticagenero', $data);
    }

    public function estatisticageneroporcentagemPdf(Request $request)
    {
        $distritoId = $request->input('distrito');


        $data = app(EstatisticaGeneroPorcentagemService::class)->execute($distritoId);

        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticagenero_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticageneroporcentagem.pdf' . date('YmdHis'));
    }
    public function estatisticatotalmembros(Request $request)
    {
        $regiaoId = $request->input('regiao');

        $data = app(EstatisticaTotalMembrosService::class)->execute($regiaoId);
        return view('regiao.estatisticas.estatisticatotalmembros', $data);
    }

    public function estatisticatotalmembrosPdf(Request $request)
    {
        $regiaoId = $request->input('regiao');
        $data = app(EstatisticaTotalMembrosService::class)->execute($regiaoId);

        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticatotalmembros_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_quantidademembros.pdf' . date('YmdHis'));
    }


    //Financeiro
    public function lancamentodasigrejas(Request $request)
    {
        $dt = $request->input('dtano');
        $igrejaId = $request->input('igreja_id');
        $regiao = Identifiable::fetchtSessionRegiao();

        $data = app(LancamentoIgrejasService::class)->execute($dt, $igrejaId, $regiao);
        return view('regiao.relatorios.lancamentodasigrejas', $data);
    }


    public function variacaofinanceira(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $distritoId = $request->input('distrito');

        $data = app(VariacaoFinanceiraService::class)->execute($dataInicial, $dataFinal, $distritoId);

        return view('regiao.relatorios.variacaofinanceira', $data);
    }

    public function variacaofinanceiraPdf(Request $request)
    {

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $distritoId = $request->input('distrito');

        $data = app(VariacaoFinanceiraService::class)->execute($dataInicial, $dataFinal, $distritoId);

        $pdf = FacadePdf::loadView('regiao.relatorios.variacaofinanceira_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_variacaofinanceira.pdf' . date('YmdHis'));
    }

    public function orcamento(Request $request)
    {
        $dt = $request->input('dtano');
        $distritoId = $request->input('distrito');

        $data = app(OrcamentoService::class)->execute($dt, $distritoId);
        return view('regiao.relatorios.orcamento', $data);
    }

    public function orcamentoPdf(Request $request)
    {
        $dt = $request->input('dtano');
        $distritoId = $request->input('distrito');

        $data = app(OrcamentoService::class)->execute($dt, $distritoId);

        $pdf = FacadePdf::loadView('regiao.relatorios.orcamento_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_orcamento.pdf' . date('YmdHis'));
    }


    public function livrorazaogeral(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $distrtoId = $request->input('distrito');
        $igrejaId = $request->input('igreja');

        $data = app(LivroRazaoGeralService::class)->execute($dataInicial, $dataFinal, $distrtoId, $igrejaId);

        return view('regiao.relatorios.livrorazaogeral', $data);
    }

    public function livrorazaogeralPdf(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $distritoId = $request->input('distrito');
        $igrejaId = $request->input('igreja');

        $data = app(LivroRazaoGeralService::class)->execute($dataInicial, $dataFinal, $distritoId, $igrejaId);

        $pdf = FacadePdf::loadView('regiao.relatorios.livrorazaogeral_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_livrorazaogeral.pdf' . date('YmdHis'));
    }

    public function saldodasigrejas(Request $request)
    {
        $dt = $request->input('dt');
        $distrtoId = $request->input('distrito');
        $data = app(SaldoIgrejasService::class)->execute($dt, $distrtoId);
        return view('regiao.relatorios.saldodasigrejas', $data);
    }

    public function saldodasigrejasPdf(Request $request)
    {
        $dt = $request->input('dt');
        $distrtoId = $request->input('distrito');

        $data = app(SaldoIgrejasService::class)->execute($dt, $distrtoId);

        $pdf = FacadePdf::loadView('regiao.relatorios.saldodasigrejas_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_saldodasigrejas.pdf' . date('YmdHis'));
    }

    public function lancamentodasigrejasPdf(Request $request)
    {

        $dt = $request->input('dtano');
        $igrejaId = $request->input('igreja_id');
        $regiao = Identifiable::fetchtSessionRegiao();

        $data = app(LancamentoIgrejasService::class)->execute($dt, $igrejaId, $regiao);

        $pdf = FacadePdf::loadView('regiao.relatorios.lancamentodasigrejas_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_lancamentodasigrejas.pdf' . date('YmdHis'));
    }

    public function clerigoAniversariante(Request $request)
    {
        $data = app(ClerigoAniversariantes::class)->execute($request->all());
        return view('regiao.relatorios.clerigos-prebendas.clerigos-aniversariantes', $data);
    }

    public function clerigoEsposa(Request $request)
    {
        $data = app(ClerigoEsposas::class)->execute($request->all());
        return view('regiao.relatorios.clerigos-prebendas.clerigos-esposas', $data);
    }

    public function clerigoDados(Request $request)
    {
        $data = app(ClerigoDados::class)->execute($request->all());
        return view('regiao.relatorios.clerigos-prebendas.clerigos-dados', $data);
    }

    public function clerigoCategoria(Request $request)
    {
        $data = app(ClerigoCategorias::class)->execute($request->all());
        return view('regiao.relatorios.clerigos-prebendas.clerigos-categorias', $data);
    }

    public function clerigoStatus(Request $request)
    {
        $data = app(ClerigoStatus::class)->execute($request->all());
        return view('regiao.relatorios.clerigos-prebendas.clerigos-status', $data);
    }

    public function CongregacaoPorIgreja(Request $request){
        $data = app(Igrejas::class)->execute($request->all());
        return view('regiao.relatorios.igreja.congregacoes-por-igreja', $data);
    }   

    public function cnpjIgreja(Request $request)
    {
        $data = app(CnpjIgreja::class)->execute($request->all());
 
        return view('regiao.relatorios.igreja.cnpj-igrejas', $data);
    }
       
    public function contatoIgreja(Request $request)
    {
        $data = app(ContatoIgreja::class)->execute($request->all());
        return view('regiao.relatorios.igreja.contato-igrejas', $data);
    }

    public function ContaBancariaIgreja(Request $request)
    {
        $data = app(ContaBancariaIgreja::class)->execute($request->all());
        return view('regiao.relatorios.igreja.conta-bancaria-igrejas', $data);
    }

    public function igrejasPorPastores(Request $request)
    {
        $data = app(IgrejasPorPastoresService::class)->execute($request->all());
        return view('regiao.relatorios.igreja.igrejas-por-pastores', $data);
    }

    public function igrejasPorClerigos(Request $request)
    {
        $data = app(IgrejasPorClerigosService::class)->execute($request->all());
        return view('regiao.relatorios.igreja.igrejas-por-clerigos', $data);
    }

    public function anoEclesiastico(Request $request)
    {
        $dataInicial = isset($request->data_inicial) ? formatDate($request->data_inicial) : '';
        $dataFinal = isset($request->data_final) ? formatDate($request->data_final) : '';
        $data['membresias'] = app(GetEstatisticaAnoEclesiasticoTodosService::class)->execute($request->all());
        $data['titulo'] = "IMW - RELATÓRIO MAPA ESTATÍSTICO DE MEMBROS - DE $dataInicial ATÉ  $dataFinal";
        return view('regiao.relatorios.igreja.membresias', $data);
    }

    public function financeiroPorCategoria(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $categoriaId = $request->input('categoria');

        $data = app(FinanceiroPorCategoriaService::class)->execute($dataInicial, $dataFinal, $categoriaId);

        return view('regiao.relatorios.financeiro-por-categotia', $data);
    }
}
