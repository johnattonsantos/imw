<?php

namespace App\Http\Controllers;

use App\Services\ServiceDistritoRelatorios\AspirantesIgrejasService;
use App\Services\ServiceDistritoRelatorios\EstatisticaGeneroService;
use App\Services\ServiceDistritoRelatorios\EstatisticasGceuService;
use App\Services\ServiceDistritoRelatorios\IgrejasService;
use App\Services\ServiceDistritoRelatorios\LancamentoIgrejasService;
use App\Services\ServiceDistritoRelatorios\LivroRazaoGeralService;
use App\Services\ServiceDistritoRelatorios\MembrosMinisterioService;
use App\Services\ServiceDistritoRelatorios\OrcamentoService;
use App\Services\ServiceDistritoRelatorios\QuantidadeMembrosService;
use App\Services\ServiceDistritoRelatorios\SaldoIgrejasService;
use App\Services\ServiceDistritoRelatorios\VariacaoFinanceiraService;
use App\Services\ServiceRelatorioClerigoPrebendas\ClerigoAniversariantesDistrito;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class DistritoRelatorioController extends Controller
{
    //Membresia
    public function membrosministerio(Request $request) {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $data = app(MembrosMinisterioService::class)->execute($dataInicial, $dataFinal, $tipo);
        return view('distrito.relatorios.membrosministerio', $data);
    }

    public function membrosministerioPdf(Request $request) {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');
        $tipo = $request->input('tipo');
        $data = app(MembrosMinisterioService::class)->execute($dataInicial, $dataFinal, $tipo);
        
        $pdf = FacadePdf::loadView('distrito.relatorios.membrosministerio_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_membrosministerio.pdf' . date('YmdHis'));
    }


    public function quantidademembros(Request $request) {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $data = app(QuantidadeMembrosService::class)->execute($dataInicial, $dataFinal, $tipo);
        return view('distrito.relatorios.quantidademembros', $data);
    }

    public function quantidademembrosPdf(Request $request) {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $data = app(QuantidadeMembrosService::class)->execute($dataInicial, $dataFinal, $tipo);
        
        $pdf = FacadePdf::loadView('distrito.relatorios.quantidademembros_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_quantidademembros.pdf' . date('YmdHis'));
    }


    public function estatisticagenero(Request $request) {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');

        $data = app(EstatisticaGeneroService::class)->execute($dataInicial, $dataFinal, $tipo);
        return view('distrito.relatorios.estatisticagenero', $data);
    }

    public function estatisticageneroPdf(Request $request) {
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');
        $tipo = $request->input('tipo');
        $data = app(EstatisticaGeneroService::class)->execute($dataInicial, $dataFinal, $tipo);
        
        $pdf = FacadePdf::loadView('distrito.relatorios.estatisticagenero_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticagenero.pdf' . date('YmdHis'));
    }

    public function estatisticasGceu()
    {
        $data = app(EstatisticasGceuService::class)->execute();

        return view('distrito.relatorios.estatisticas-gceu', $data);
    }


    //Financeiro
    public function lancamentodasigrejas(Request $request)
    {
        $dt = $request->input('dtano');
        $igrejasID = $request->input('igrejas', []);

        $data = app(LancamentoIgrejasService::class)->execute($dt, $igrejasID);
        return view('distrito.relatorios.lancamentodasigrejas', $data);
    }


    public function variacaofinanceira(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        $data = app(VariacaoFinanceiraService::class)->execute($dataInicial, $dataFinal);

        return view('distrito.relatorios.variacaofinanceira', $data);
    }

    public function variacaofinanceiraPdf(Request $request)
    {

        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        $data = app(VariacaoFinanceiraService::class)->execute($dataInicial, $dataFinal);

        $pdf = FacadePdf::loadView('distrito.relatorios.variacaofinanceira_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_variacaofinanceira.pdf' . date('YmdHis'));
    }

    public function orcamento(Request $request)
    {
        $dt = $request->input('dtano');

        $data = app(OrcamentoService::class)->execute($dt);
        return view('distrito.relatorios.orcamento', $data);
    }

    public function orcamentoPdf(Request $request)
    {

        $dt = $request->input('dtano');

        $data = app(OrcamentoService::class)->execute($dt);

        $pdf = FacadePdf::loadView('distrito.relatorios.orcamento_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_orcamento.pdf' . date('YmdHis'));
    }


    public function livrorazaogeral(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        $data = app(LivroRazaoGeralService::class)->execute($dataInicial, $dataFinal);

        return view('distrito.relatorios.livrorazaogeral', $data);
    }

    public function livrorazaogeralPdf(Request $request)
    {
        $dataInicial = $request->input('dt_inicial');
        $dataFinal = $request->input('dt_final');

        $data = app(LivroRazaoGeralService::class)->execute($dataInicial, $dataFinal);

        $pdf = FacadePdf::loadView('distrito.relatorios.livrorazaogeral_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_livrorazaogeral.pdf' . date('YmdHis'));
    }

    public function saldodasigrejas(Request $request)
    {
        $dt = $request->input('dt');

        $data = app(SaldoIgrejasService::class)->execute($dt);
        return view('distrito.relatorios.saldodasigrejas', $data);
    }

    public function saldodasigrejasPdf(Request $request)
    {
        $dt = $request->input('dt');
        $data = app(SaldoIgrejasService::class)->execute($dt);

        $pdf = FacadePdf::loadView('distrito.relatorios.saldodasigrejas_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_saldodasigrejas.pdf' . date('YmdHis'));
    }

    public function lancamentodasigrejasPdf(Request $request)
    {

        $dt = $request->input('dtano');
        $igrejasID = json_decode($request->input('igrejas'), true);

        $data = app(LancamentoIgrejasService::class)->execute($dt, $igrejasID);

        $pdf = FacadePdf::loadView('distrito.relatorios.lancamentodasigrejas_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_lancamentodasigrejas.pdf' . date('YmdHis'));
    }

    public function CongregacaoPorIgreja(Request $request){
        $data = app(IgrejasService::class)->execute($request->all());
        return view('distrito.relatorios.igreja.congregacoes-por-igreja', $data);
    }

    public function AspirantePorIgreja(Request $request){
        $data = app(AspirantesIgrejasService::class)->execute($request->all());
        return view('distrito.relatorios.igreja.aspirantes', $data);
    }

    public function clerigoAniversariante(Request $request)
    {
        
        $data = app(ClerigoAniversariantesDistrito::class)->execute($request->all());
        return view('distrito.relatorios.clerigos-prebendas.clerigos-aniversariantes', $data);
    }
}
