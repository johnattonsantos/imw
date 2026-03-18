<?php

namespace App\Http\Controllers;

use App\Services\ServiceRelatorio\IdentificaDadosRelatorioAniversariantesService;
use App\Services\ServiceRelatorio\IdentificaDadosRelatorioFuncoesEclesiasticasService;
use App\Services\ServiceRelatorio\IdentificaDadosRelatorioHistoricoEclesiasticoService;
use App\Services\ServiceRelatorio\IdentificaDadosRelatorioMembresiaService;
use App\Services\ServiceRelatorio\IdentificaDadosRelatorioMembrosDisciplinadosService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    use Identifiable;

    public function membresia(Request $request)
    {
        try {
            $data = app(IdentificaDadosRelatorioMembresiaService::class)->execute($request->all());

            if($data['render'] == 'view') {
                return view('relatorios.membresia', $data);
            }

            $pdf = PDF::loadView('relatorios.pdf.membresia', $data);
            return $pdf->inline('RELATORIO_MEMBROS_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membresia, escolha um vínculo: Membro, Congregado ou Visitante');
        }
    }

    public function aniversariantes(Request $request)
    {
        try {
            $data = app(IdentificaDadosRelatorioAniversariantesService::class)->execute($request->all());
            if($data['render'] == 'view') {
                return view('relatorios.aniversariantes', $data);
            }

            $pdf = PDF::loadView('relatorios.pdf.aniversariantes', $data);
            return $pdf->inline('RELATORIO_MEMBROS_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de aniversariantes');
        }
    }

    public function historicoEclesiastico(Request $request)
    {
        try {
            $data = app(IdentificaDadosRelatorioHistoricoEclesiasticoService::class)->execute($request->all());
            if($data['render'] == 'view') {
                return view('relatorios.historico-eclesiastico', $data);
            }
            $pdf = PDF::loadView('relatorios.pdf.historico-eclesiastico', $data);
            return $pdf->inline('RELATORIO_MEMBROS_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de histórico eclesiástico');
        }
    }

    public function membrosDisciplinados(Request $request)
    {
        try {
            $data = app(IdentificaDadosRelatorioMembrosDisciplinadosService::class)->execute($request->all());
            if($data['render'] == 'view') {
                return view('relatorios.membros-disciplinados', $data);
            }

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membros disciplinados');
        }
    }

    public function funcaoEclesiastica(Request $request)
    {
        try {
            $data = app(IdentificaDadosRelatorioFuncoesEclesiasticasService::class)->execute($request->all());
            if($data['render'] == 'view') {
                return view('relatorios.funcao-eclesiastica', $data);
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membros disciplinados');
        }
    }

    public function esposasDePastores()
    {
        try {
            $igrejaId = self::fetchSessionIgrejaLocal()->id;

            $esposas = DB::table('pessoas_pessoas as pp')
                ->join('pessoas_dependentes as pd', 'pd.pessoa_id', '=', 'pp.id')
                ->select(
                    'pd.nome as esposa_nome',
                    'pd.cpf as esposa_cpf',
                    'pd.data_nascimento as esposa_data_nascimento',
                    'pp.nome as pastor_nome',
                    'pp.telefone_preferencial as pastor_telefone'
                )
                ->where('pp.igreja_id', $igrejaId)
                ->where('pp.categoria', 'pastor')
                ->where('pd.parentesco', 'Cônjuge')
                ->orderBy('pd.nome')
                ->get();

            return view('relatorios.esposas-de-pastores', compact('esposas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de esposas de pastores');
        }
    }
    
}
