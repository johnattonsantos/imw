<?php

namespace App\Http\Controllers;

use App\DataTables\GCeuDatatable;
use App\Http\Requests\StoreGCeuCartaPastoralRequest;
use App\Http\Requests\StoreGCeuRequest;
use App\Http\Requests\UpdateGCeuCartaPastoralRequest;
use App\Http\Requests\UpdateGCeuRequest;
use App\Models\GCeu;
use App\Models\MembresiaMembro;
use App\Models\PessoasPessoa;
use App\Services\ServiceGCeu\CartaPastoralGCeuDistritoService;
use App\Services\ServiceGCeu\CartaPastoralGCeuRegiaoService;
use App\Services\ServiceGCeu\CartaPastoralGCeuService;
use App\Services\ServiceGCeu\DeletarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\DeletarGCeuService;
use App\Services\ServiceGCeu\EditarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\EditarGCeuService;
use App\Services\ServiceGCeu\GCeuDiarioPresencaFaltaService;
use App\Services\ServiceGCeu\GCeuDiarioService;
use App\Services\ServiceGCeu\GCeuMembrosService;
use App\Services\ServiceGCeu\GCeuRelatorioAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoFuncoesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoGceuService;
use App\Services\ServiceGCeu\GCeuRelatorioFuncoesService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoFuncoesService;
use App\Services\ServiceGCeu\GCeuService;
use App\Services\ServiceGCeu\GCeuRelatorioGceuService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoGceuService;
use App\Services\ServiceGCeu\GCeuUpdateMembroService;
use App\Services\ServiceGCeu\StoreGCeuCartaPastoralService;
use App\Services\ServiceGCeu\StoreGCeuService;
use App\Services\ServiceGCeu\VisualizarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\VisualizarGCeuService;
use App\Services\ServiceVisitantes\IdentificaDadosIndexService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class GceuController extends Controller
{
    use Identifiable;
    ////////////////////////////GCEU IGREJA/////////////////////////////
    public function index(Request $request)
    {
        $data = app(IdentificaDadosIndexService::class)->execute($request->all());
        return view('gceu.index', $data);
    }

    public function list(Request $request)
    {
        try {
            return app(GCeuDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'erro ao carregar os dados GCEU'], 500);
        }
    }

    public function editar($id)
    {
        $gceu = app(EditarGCeuService::class)->findOne($id);
        $congregacoes = Identifiable::fetchCongregacoes();
        if (!$gceu) {
            return redirect()->route('gceu.index')->with('error', 'GCEU não encontrado.');
        }
        return view('gceu.editar', compact('gceu', 'congregacoes'));
    }

    public function update(UpdateGCeuRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(EditarGCeuService::class)->execute($id, $request->all());
            DB::commit();
            return redirect()->route('gceu.editar', ['id' => $id])->with('success', 'GCEU atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->route('gceu.editar', ['id' => $id])->with('error', 'Falha ao atualizar o GCEU.');
        }
    }

    public function deletar($id)
    {
        $congregacoes = Identifiable::fetchCongregacoes();
        try {
            $existe = GCeu::join('gceu_membros', 'gceu_membros.gceu_cadastro_id','gceu_cadastros.id')->where('gceu_cadastros.id',$id)->first();
            if($existe){
                return back()->with('error', 'Não poderá remover esse CGEU, pois existe membros vinculados.');
            }else{
                app(DeletarGCeuService::class)->execute($id);
                return redirect()->route('gceu.index')->with('success', 'GCEU deletado com sucesso.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao deletar o GCEU.');
        }
    }

    public function novo()
    {
        try {
            return view('gceu.create', ['congregacoes' => Identifiable::fetchCongregacoes(), 'instituicao_id' => Identifiable::fetchSessionIgrejaLocal()->id]);
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao abrir a página de novo visitante');
        }
    }

    public function store(StoreGCeuRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreGCeuService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('gceu.index')->with('success', 'GCEU cadastrado com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('gceu.index')->with('error', $e->getMessage());
        }
    }

    public function visualizarHtml($id)
    {
        $gceu = app(VisualizarGCeuService::class)->findOne($id);
        if (!$gceu) {
            return redirect()->route('gceu.index')->with('error', 'GCEU não encontrado.');
        }
        return view('gceu.visualizar', ['gceu' =>  $gceu]);
    }

    public function membros(Request $request)
    {
        ///dd($request->all());
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuMembrosService::class)->getList($igrejaId, $data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.membros.index', $data);
    }

    public function updateMembro(Request $request, $id)
    {
        DB::beginTransaction();
        app(GCeuUpdateMembroService::class)->execute($request->all(), $id);
        DB::commit();
        return back()->with('success', 'Registro atualizado.');

    }

    public function cartaPastoral()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(CartaPastoralGCeuService::class)->getList($igrejaId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.index', $data);
    }

    public function cartaPastoralRelatorio()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(CartaPastoralGCeuService::class)->getList($igrejaId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.relatorio', $data);
    }
    
    public function cartaPastoralEditar($id)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data['pastores'] = PessoasPessoa::select('pessoas_pessoas.id', 'pessoas_pessoas.nome')
                ->join('pessoas_nomeacoes', 'pessoas_nomeacoes.pessoa_id', 'pessoas_pessoas.id')
                ->join('pessoas_funcaoministerial', 'pessoas_funcaoministerial.id', 'pessoas_nomeacoes.funcao_ministerial_id')
                ->where(['pessoas_nomeacoes.instituicao_id' => $igrejaId])->whereIn('pessoas_funcaoministerial.ordem', [3,4,5])->whereNull('pessoas_nomeacoes.data_termino')->get();
        $data['cartaPastoral'] = app(EditarGCeuCartaPastoralService::class)->findOne($id);
        if (!$data['cartaPastoral']) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta Pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.editar', $data);
    }

    public function cartaPastoralUpdate(UpdateGCeuCartaPastoralRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(EditarGCeuCartaPastoralService::class)->execute($id, $request->all());
            DB::commit();
            return redirect()->route('gceu.carta-pastoral', ['id' => $id])->with('success', 'Carta Pastoral atualizada com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->route('gceu.carta-pastoral.editar', ['id' => $id])->with('error', 'Falha ao atualizar a Carta Pastoral.');
        }
    }

    public function cartaPastoralDeletar($id)
    {
        try {
            app(DeletarGCeuCartaPastoralService::class)->execute($id);
            return redirect()->route('gceu.carta-pastoral')->with('success', 'Carta pastoral deletada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao deletar a carta pastoral.');
        }
    }

    public function cartaPastoralNovo()
    {
        try {
            $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
            $data['pastores'] = PessoasPessoa::select('pessoas_pessoas.id', 'pessoas_pessoas.nome')
                    ->join('pessoas_nomeacoes', 'pessoas_nomeacoes.pessoa_id', 'pessoas_pessoas.id')
                    ->join('pessoas_funcaoministerial', 'pessoas_funcaoministerial.id', 'pessoas_nomeacoes.funcao_ministerial_id')
                    ->where(['pessoas_nomeacoes.instituicao_id' => $igrejaId])->whereIn('pessoas_funcaoministerial.ordem', [3,4,5])->whereNull('pessoas_nomeacoes.data_termino')->get();
            $data['instituicao_id'] = $igrejaId;
            $data['instituicao'] = Identifiable::fetchSessionIgrejaLocal()->nome;
            return view('gceu.carta-pastoral.create', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao abrir a página nova carta pastoral');
        }
    }

    public function cartaPastoralStore(StoreGCeuCartaPastoralRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreGCeuCartaPastoralService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('gceu.carta-pastoral')->with('success', 'Carta pastoral cadastrada com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('gceu.carta-pastoral')->with('error', $e->getMessage());
        }
    }

    public function cartaPastoralVisualizarHtml($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdf($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function diario(Request $request)
    {
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuDiarioService::class)->getList($igrejaId, $data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.diario.index', $data);
    }

     public function diarioRelatorio(Request $request)
    {
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuDiarioService::class)->getListRelatorio($igrejaId, $data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Diário não encontrada.');
        }
        return view('gceu.diario.relatorio', $data);
    }
    

    public function diarioPresencaFalta(Request $request)
    {
        $data = $request->all();
        $data = app(GCeuDiarioPresencaFaltaService::class)->salvarDiario($data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.diario.index', $data);
    }

    public function gceuRelatorioFuncoes()
    {

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $funcao = app(GCeuRelatorioFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioFuncoesService::class)->getList($igrejaId, request()->funcao_id, request()->gceu_id);
        $data['igreja'] = Identifiable::fetchSessionIgrejaLocal()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU da Igreja: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU da Igreja: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório líderes GCEU não encontradd.');
        }
        return view('gceu.relatorio-igreja.funcoes', $data);
    }

    public function gceuRelatorioGceu()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;        
        $data = app(GCeuRelatorioGceuService::class)->getList($igrejaId);
        $data['titulo'] =  "Relatório de GCEU da Igreja: ".Identifiable::fetchSessionIgrejaLocal()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-igreja.gceu', $data);
    }

    public function gceuRelatorioAniversariantes()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuRelatorioAniversariantesService::class)->getList($igrejaId);
        $data['titulo'] =  "Relatório de Aniversariantes GCEU da Igreja: ".Identifiable::fetchSessionIgrejaLocal()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-igreja.aniversariantes', $data);
    }

    ////////////////////////////GCEU DISTRITO/////////////////////////////
    public function gceuRelatorioDistritoGceu()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;    
        $data = app(GCeuRelatorioDistritoGceuService::class)->getList($distritoId);
        $data['titulo'] =  "Relatório de GCEU do Distrito: ".Identifiable::fetchtSessionDistrito()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-distrito.gceu', $data);
    }

    public function cartaPastoralDistrito()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $data = app(CartaPastoralGCeuDistritoService::class)->getList($distritoId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-distrito.relatorio', $data);
    }


    public function cartaPastoralVisualizarHtmlDistrito($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-distrito.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdfDistrito($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral-distrito.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function gceuRelatorioDistritoFuncoes(Request $request)
    {

        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $funcao = app(GCeuRelatorioDistritoFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioDistritoFuncoesService::class)->getList($distritoId, request()->funcao_id, request()->gceu_id);
        
        $data['igreja'] = Identifiable::fetchtSessionDistrito()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU do Distrito: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU do Distrito: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório funções GCEU não encontradd.');
        }
        return view('gceu.relatorio-distrito.funcoes', $data);
    }

    public function gceuRelatorioDistritoAniversariantes()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $data = app(GCeuRelatorioDistritoAniversariantesService::class)->getList($distritoId);

        $data['titulo'] =  "Relatório de Aniversariantes GCEU do Distrito: ".Identifiable::fetchtSessionDistrito()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-distrito.aniversariantes', $data);
    }
    
     ////////////////////////////GCEU REGIAO/////////////////////////////
    public function gceuRelatorioRegiaoGceu()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;  
        $data = app(GCeuRelatorioRegiaoGceuService::class)->getList($regiaoId);
        $data['titulo'] =  "Relatório de GCEU da região: ".Identifiable::fetchtSessionRegiao()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-regiao.gceu', $data);
    }

    public function cartaPastoralRegiao()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $data = app(CartaPastoralGCeuRegiaoService::class)->getList($regiaoId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-regiao.relatorio', $data);
    }


    public function cartaPastoralVisualizarHtmlRegiao($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-regiao.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdfRegiao($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral-regiao.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function gceuRelatorioRegiaoFuncoes(Request $request)
    {

        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $funcao = app(GCeuRelatorioRegiaoFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioRegiaoFuncoesService::class)->getList($regiaoId, request()->distrito_id, request()->igreja_id, request()->funcao_id, request()->gceu_id);
        
        $data['igreja'] = Identifiable::fetchtSessionRegiao()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU da Região: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU da Região: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório funções GCEU não encontradd.');
        }
        return view('gceu.relatorio-regiao.funcoes', $data);
    }
    
    public function gceuRelatorioRegiaoAniversariantes()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $data = app(GCeuRelatorioRegiaoAniversariantesService::class)->getList($regiaoId);

        $data['titulo'] =  "Relatório de Aniversariantes GCEU da Região: ".Identifiable::fetchtSessionRegiao()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-regiao.aniversariantes', $data);
    }
}