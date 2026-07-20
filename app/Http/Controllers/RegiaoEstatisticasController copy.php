<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MembresiaMembro;
use App\Services\EstatisticaClerigosService\HistoricoNomeacoes;
use App\Services\EstatisticaClerigosService\TotalTicketMedio;
use App\Services\ServiceEstatisticas\TotalMembresiaServices;
use App\Services\ServiceRegiaoRelatorios\IdentificaDadosRegiaoRelatorioMembresiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use App\Traits\Identifiable;
use Yajra\DataTables\Facades\DataTables;

class RegiaoEstatisticasController extends Controller
{
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
        $anoinicio = request('anoinicio', date('Y') - 4);
        $anofinal = request('anofinal', date('Y'));
        //$regiao_id = auth()->user()->pessoa->regiao_id;
        $regiao = Identifiable::fetchtSessionRegiao();
        $regiao_id = $regiao->id;
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
        $instituicoes_pais = DB::select("
            SELECT
                inst.id,
                inst.nome AS instituicao,
                inst.instituicao_pai_id,
                $colunasAnoSQLPais,
                (SELECT COUNT(*) FROM membresia_rolpermanente WHERE distrito_id = inst.id and dt_exclusao is null and lastrec = 1) AS total_membros
            FROM instituicoes_instituicoes inst
            WHERE inst.instituicao_pai_id = ?
            ORDER BY inst.nome
        ", [$regiao_id]);

        // 🔹 PEGAR IDS DOS PAIS ENCONTRADOS PARA BUSCAR FILHOS
        $ids_pais = array_column($instituicoes_pais, 'id');

        // ===========================
        // 🔹 Buscar os FILHOS (instituições que pertencem aos pais encontrados)
        // ===========================
        if (!empty($ids_pais)) {
            $instituicoes_filhos = DB::select("
                SELECT
                    inst.id,
                    inst.nome AS instituicao,
                    inst.instituicao_pai_id,
                    $colunasAnoSQLFilhos,
                    (SELECT COUNT(*) FROM membresia_rolpermanente WHERE igreja_id = inst.id and dt_exclusao is null and lastrec = 1) AS total_membros
                FROM instituicoes_instituicoes inst
                WHERE inst.instituicao_pai_id IN (" . implode(',', $ids_pais) . ")
                ORDER BY inst.nome
            ");
        } else {
            $instituicoes_filhos = [];
        }

        return view('regiao.estatisticas.evolucao', compact('instituicoes_pais', 'instituicoes_filhos', 'anoinicio', 'anofinal'));
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
        $params = $request->all();

        ;

        try {
            $data = app(IdentificaDadosRegiaoRelatorioMembresiaService::class)->execute($request->all());
            //dd($data['membros']);
            //dd($request->wantsJson());
            if ($request->ajax()) {


                //dd( $params);



                //$dtInicial = $params['dt_inicial'];
            //$dtFinal = $params['dt_final'];
            //dd($params);
            $membresiaMembro =  MembresiaMembro::query()->select('membresia_membros.*', 'distrito.nome as distrito_nome', 'igreja.nome as igreja_nome', 'congregacao.nome as congregacao_nome', 'recepcao_modo.nome as recepcao_modo', 'exclusao_modo.nome as exclusao_modo', 'membresia_rolpermanente.dt_recepcao','membresia_rolpermanente.dt_exclusao',
                DB::raw("(SELECT CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE telefone_whatsapp END contato FROM membresia_contatos WHERE membro_id = membresia_membros.id) AS telefone") )

            ->join('instituicoes_instituicoes as distrito', 'distrito.id', 'membresia_membros.distrito_id')
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', 'membresia_membros.igreja_id')
            ->leftJoin('congregacoes_congregacoes as congregacao', 'congregacao.id', 'igreja.id')
            ->join('membresia_rolpermanente', 'membresia_rolpermanente.membro_id', 'membresia_membros.id')
            ->leftJoin('membresia_situacoes as recepcao_modo', 'recepcao_modo.id', 'membresia_rolpermanente.modo_recepcao_id')
            ->leftJoin('membresia_situacoes as exclusao_modo', 'exclusao_modo.id', 'membresia_rolpermanente.modo_exclusao_id')
            // ->when($params['situacao'] == 'ativos', function ($query) {
            //     $query->where(function ($query) {
            //         $query->withoutGlobalScopes();
            //         $query->where('membresia_rolpermanente.status', 'A');
            //         $query->where('membresia_membros.status', 'A');
            //     });
            // })
            // ->when($params['situacao'] == 'inativos', function ($query) {
            //     $query->where(function ($query) {
            //         $query->withoutGlobalScopes();
            //         $query->where('membresia_rolpermanente.status', 'I');
            //         $query->where('membresia_membros.status', 'I');
            //     });
            // })
            // ->when($params['dt_filtro'] == 'dt_recepcao', function  ($query) use( $dtInicial, $dtFinal) {
            //     $query->where(function ($query) {
            //         $query->withoutGlobalScopes();
            //         $query->where('membresia_rolpermanente.status', 'A');
            //     });
            //     $query->when($dtInicial, fn ($query) => $query->where('membresia_rolpermanente.dt_recepcao', '>=' , $dtInicial));
            //     $query->when($dtFinal, fn ($query) => $query->where('membresia_rolpermanente.dt_recepcao', '<=' , $dtFinal));
            // })
            // ->when($params['dt_filtro'] == 'dt_exclusao', function ($query) use( $dtInicial, $dtFinal) {
            //     $query->where(function ($query) {
            //         $query->withoutGlobalScopes();
            //         $query->where('membresia_rolpermanente.status', 'I');
            //     });
            //     $query->when($dtInicial, fn ($query) => $query->where('membresia_rolpermanente.dt_exclusao', '>=' , $dtInicial));
            //     $query->when($dtFinal, fn ($query) => $query->where('membresia_rolpermanente.dt_exclusao', '<=' , $dtFinal));
            // })
            //->when($params['congregacao_id'], fn ($query) => $query->where('membresia_membros.congregacao_id', $params['congregacao_id']))
            // ->when($params['dt_filtro'], function ($query) use ($params) {
            //     if ($params['dt_filtro'] == 'data_nascimento') {
            //         return $this->handleFilterDtNascimento($query, $params['dt_inicial'], $params['dt_final']);
            //     } else {
            //         return $this->handleRolDates($query, $params['dt_filtro'], $params['dt_inicial'], $params['dt_final']);
            //     }
            // })
            // ->when($params['distrito_id'], fn ($query) => $query->where('distrito.id', $params['distrito_id']))
            // ->where('membresia_membros.vinculo', $params['vinculo'])
            // ->where('membresia_membros.igreja_id', $igrejaId)
            // ->where('membresia_rolpermanente.igreja_id', $igrejaId)
            ->where('distrito.instituicao_pai_id', 23)
            ->orderBy('membresia_rolpermanente.dt_recepcao', 'DESC');




//dd($membresiaMembro);






                //dd($membresiaMembro);
               return DataTables::eloquent($membresiaMembro)->make(true);
            }
            return view('regiao.membresia', $data);

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', __('Não foi possível abrir a página de relatórios de membresia, escolha um vínculo: Membro, Congregado ou Visitante'));
        }
    }
}
