<?php

namespace App\Http\Controllers;

use App\Exports\MembresiaExport;
use App\Http\Controllers\Controller;
use App\Services\EstatisticaClerigosService\HistoricoNomeacoes;
use App\Services\EstatisticaClerigosService\TotalTicketMedio;
use App\Services\ServiceEstatisticas\TotalMembresiaServices;
use App\Services\ServiceRegiaoRelatorios\IdentificaDadosRegiaoRelatorioMembresiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
class RegiaoEstatisticasController extends Controller
{
    protected $excel;
     public function __construct(\Maatwebsite\Excel\Exporter $excel)
    {
        $this->excel = $excel;
    }
    public function totalMembresia(Request $request)
    {
        $checkIgreja = $request->input('checkIgreja', 'distrito'); // Padrão para 'distrito' se nada for selecionado

        $dados = app(TotalMembresiaServices::class)->execute($checkIgreja);

        return view('regiao.estatisticas.totalmembresia', [
            'regiao' => $dados['regiao'],
            'dados' => $dados['resultado'],
            'totalGeral' => $dados['totalGeral'],
            'tipo' => $checkIgreja // Passa o tipo para a view
        ]);
    }



    public function estatisticaEvolucao(Request $request)
    {
        // Capturar os anos do request
        $anoinicio = (int) $request->input('anoinicio', date('Y') - 4);
        $anofinal = (int) $request->input('anofinal', date('Y'));
        $distritoId = $request->input('distrito_id', 'all');
        $igrejaId = $request->input('igreja_id', 'all');
        //$regiao_id = auth()->user()->pessoa->regiao_id;
        $regiao = Identifiable::fetchtSessionRegiao();
        $regiao_id = $regiao->id;
        $distritos = Identifiable::fetchDistritosByRegiao($regiao_id)
            ->filter(function ($instituicao) {
                return is_null($instituicao->data_abertura)
                    || is_null($instituicao->deleted_at)
                    || (int) ($instituicao->ativo ?? 0) === 1;
            })
            ->values();
        $igrejas = (is_numeric($distritoId) && (int) $distritoId > 0)
            ? Identifiable::fetchIgrejasByDistrito((int) $distritoId)
                ->filter(function ($instituicao) {
                    return is_null($instituicao->data_abertura)
                        || is_null($instituicao->deleted_at)
                        || (int) ($instituicao->ativo ?? 0) === 1;
                })
                ->values()
            : collect();
        // ===========================
        // 🔹 Criar colunas de contagem de membros por ano
        // ===========================
        $colunasAnoPais = [];
        $colunasAnoFilhos = [];

        for ($ano = $anoinicio; $ano <= $anofinal; $ano++) {
            // Para os PAIS (distrito_id)
            $colunasAnoPais[] = "
                (SELECT COUNT(*) FROM membresia_rolpermanente
                 WHERE distrito_id = inst.id
                 AND dt_exclusao is null
                 AND YEAR(dt_recepcao) <= $ano
                ) AS `$ano`
            ";

            // Para os FILHOS (igreja_id)
            $colunasAnoFilhos[] = "
                (SELECT COUNT(*) FROM membresia_rolpermanente
                 WHERE igreja_id = inst.id
                 AND dt_exclusao is null
                 AND YEAR(dt_recepcao) <= $ano
                ) AS `$ano`
            ";
        }

        $colunasAnoSQLPais = implode(", ", $colunasAnoPais);
        $colunasAnoSQLFilhos = implode(", ", $colunasAnoFilhos);

        // ===========================
        // 🔹 Buscar os PAIS (instituições na região do usuário)
        // ===========================
        $sqlPais = "
            SELECT
                inst.id,
                inst.nome AS instituicao,
                inst.instituicao_pai_id,
                $colunasAnoSQLPais,
                (SELECT COUNT(*) FROM membresia_rolpermanente WHERE distrito_id = inst.id and dt_exclusao is null and lastrec = 1) AS total_membros
            FROM instituicoes_instituicoes inst
            WHERE inst.instituicao_pai_id = ?
            AND inst.tipo_instituicao_id = 2
            AND inst.data_encerramento IS NULL
            AND (inst.data_abertura IS NULL OR inst.deleted_at IS NULL OR inst.ativo = 1)
        ";
        $bindingsPais = [$regiao_id];
        if (is_numeric($distritoId) && (int) $distritoId > 0) {
            $sqlPais .= " AND inst.id = ? ";
            $bindingsPais[] = (int) $distritoId;
        }
        $sqlPais .= " ORDER BY inst.nome ";
        $instituicoes_pais = DB::select($sqlPais, $bindingsPais);

        // 🔹 PEGAR IDS DOS PAIS ENCONTRADOS PARA BUSCAR FILHOS
        $ids_pais = array_column($instituicoes_pais, 'id');

        // ===========================
        // 🔹 Buscar os FILHOS (instituições que pertencem aos pais encontrados)
        // ===========================
        if (!empty($ids_pais)) {
            $sqlFilhos = "
                SELECT
                    inst.id,
                    inst.nome AS instituicao,
                    inst.instituicao_pai_id,
                    $colunasAnoSQLFilhos,
                    (SELECT COUNT(*) FROM membresia_rolpermanente WHERE igreja_id = inst.id and dt_exclusao is null and lastrec = 1) AS total_membros
                FROM instituicoes_instituicoes inst
                WHERE inst.instituicao_pai_id IN (" . implode(',', $ids_pais) . ")
                AND inst.tipo_instituicao_id = 1
                AND inst.data_encerramento IS NULL
                AND (inst.data_abertura IS NULL OR inst.deleted_at IS NULL OR inst.ativo = 1)
            ";
            $bindingsFilhos = [];
            if (is_numeric($igrejaId) && (int) $igrejaId > 0) {
                $sqlFilhos .= " AND inst.id = ? ";
                $bindingsFilhos[] = (int) $igrejaId;
            }
            $sqlFilhos .= " ORDER BY inst.nome ";
            $instituicoes_filhos = DB::select($sqlFilhos, $bindingsFilhos);
        } else {
            $instituicoes_filhos = [];
        }

        return view('regiao.estatisticas.evolucao', compact(
            'instituicoes_pais',
            'instituicoes_filhos',
            'anoinicio',
            'anofinal',
            'distritos',
            'igrejas',
            'distritoId',
            'igrejaId'
        ));
    }


    public function historiconomeacoes(Request $request)
    {
        $visao = $request->input('visao');
        $data = app(HistoricoNomeacoes::class)->execute($visao);
        return view('regiao.estatisticas.estatisticanomeacoes', $data);
    }


    public function historiconomeacoesPdf(Request $request)
    {
        $visao = $request->input('visao');
        $data = app(HistoricoNomeacoes::class)->execute($visao);

        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticanomeacoes_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticanomeacoes.pdf' . date('YmdHis'));
    }
    public function ticketmedio(Request $request)
    {
        $anoinicio =  $request->input('anoinicio', date('Y') - 4);
        $anofinal =  $request->input('anofinal', date('Y'));
        $data = app(TotalTicketMedio::class)->execute($anoinicio, $anofinal);
        return view('regiao.estatisticas.clerigos.estatisticaticketmedio', data: $data);
    }

    public function ticketmedioPdf(Request $request)
    {
        $anoinicio =  $request->input('anoinicio', date('Y') - 4);
        $anofinal =  $request->input('anofinal', date('Y'));
        $data = app(TotalTicketMedio::class)->execute($anoinicio, $anofinal);
        $pdf = FacadePdf::loadView('regiao.estatisticas.estatisticaticketmedio_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_estatisticaticketmedio.pdf' . date('YmdHis'));
    }

    public function membresia(Request $request)
    {
        try {
            $data = app(IdentificaDadosRegiaoRelatorioMembresiaService::class)->execute($request->all());
            
            if ($request->ajax()) {
                return view('regiao.ajax.membresia', $data);
            }
            return view('regiao.membresia', $data);

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membresia, escolha um vínculo: Membro, Congregado ou Visitante');
        }
    }

    public function membresiaExportar(Request $request)
    {
        try {
            $params = $request->all();
            $regiao = Identifiable::fetchtSessionRegiao();
            $txt = 'membresia-'.$regiao->nome.'-'.Carbon::now();
            $params['regiao_nome'] = $regiao->nome;
            return Excel::download(new MembresiaExport($params), slugDoc($txt).".xlsx");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membresia, escolha um vínculo: Membro, Congregado ou Visitante');
        }
    }

    public function membresiaExportarPdf(Request $request)
    {
        try {
            $regiao = Identifiable::fetchtSessionRegiao();
            $txt = 'membresia-'.$regiao->nome.'-'.Carbon::now();
            $data['regiao_nome'] = $regiao->nome;

            $data = app(IdentificaDadosRegiaoRelatorioMembresiaService::class)->exportarPdf($request->all());
    
            $pdf = PDF::loadView('regiao.pdf.membresia', $data)->setPaper('a4', 'landscape');
            return $pdf->stream('RELATORIO_MEMBRESIA_' . date('YmdHis') . '.pdf');
            //return Excel::download(new MembresiaExport($params), slugDoc($txt).".xlsx");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de relatórios de membresia, escolha um vínculo: Membro, Congregado ou Visitante');
        }
    }
    
}
