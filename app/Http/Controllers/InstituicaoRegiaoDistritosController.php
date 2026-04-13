<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinalizarNomeacoesRequest;
use App\Http\Requests\StoreNomeacoesClerigosInstiruicoesRequest;
use App\Http\Requests\StoreReceberNovoRequest;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\PessoaFuncaoministerial;
use App\Models\PessoasPessoa;
use App\Services\ServiceClerigosRegiao\FinalizarNomeacoesClerigos;
use App\Services\ServiceClerigosRegiao\ListaClerigosService;
use App\Services\ServiceClerigosRegiao\ListaNomeacoesClerigoService;
use App\Services\ServiceInstituicaoRegiao\UpdateRegiaoService;
use App\Services\ServiceInstituicaoRegiao\ListarRegiaoServices;
use App\Services\ServiceInstituicaoRegiao\DeletarRegiaoService;
use App\Services\ServiceInstituicaoRegiao\AtivarRegiaoService;
use App\Services\ServiceInstituicaoRegiao\DetalhesRegiaoService;
use App\Services\ServiceInstituicaoRegiao\StoreRegiaoService;
use App\Services\ServiceNomeacoes\StoreNomeacoesClerigos;
use App\Traits\LocationUtils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstituicaoRegiaoDistritosController extends Controller
{
    use LocationUtils;
    public function index(Request $request)
    {
        $tipoInstituicaoId = $request->get('tipo_instituicao_id');
        $searchTerm = $request->input('search');
        $ativo = $request->input('ativo');
        $parameters = [
            'search' => $searchTerm,
            'ativo' => $ativo,
        ];
        $instituicoes = app(ListarRegiaoServices::class)->execute($parameters, $tipoInstituicaoId);

        return view('instituicoes.index', compact('instituicoes'));
    }


    public function novo()
    {
        //Enviar Lista de insituicões pai, todas da regiao_id exceto igrejas
        $ufs = $this->fetchUFs();
        $instituicoes_pai = InstituicoesInstituicao::select('instituicoes_instituicoes.*', 'ii_pai.nome as instituicao_pai_nome') // Selecionando apenas os nomes
            ->where(function ($query) {
                $query->where('instituicoes_instituicoes.tipo_instituicao_id', 3);
            })
            ->orWhere(function ($query) {
                $query->where('instituicoes_instituicoes.regiao_id', session()->get('session_perfil')->instituicao_id)
                    ->where('instituicoes_instituicoes.tipo_instituicao_id', '!=', 1);
            })
            ->join('instituicoes_instituicoes as ii_pai', 'ii_pai.id', '=', 'instituicoes_instituicoes.instituicao_pai_id') // Realiza o JOIN
            ->get();

        return view('instituicoes.novo', compact('instituicoes_pai', 'ufs'));
    }

    public function store(StoreReceberNovoRequest $request)
    {

        app(StoreRegiaoService::class)->execute($request);

        return redirect()->route('instituicoes-regiao.index')->with('success', 'Instituição criado com sucesso!');
    }

    public function editar(string $id)
    {
        //Enviar Lista de insituicões pai, todas da regiao_id exceto igrejas
        $instituicoes_pai = InstituicoesInstituicao::select('instituicoes_instituicoes.*', 'ii_pai.nome as instituicao_pai_nome') // Selecionando apenas os nomes
            ->where(function ($query) {
                $query->where('instituicoes_instituicoes.tipo_instituicao_id', 3);
            })
            ->orWhere(function ($query) {
                $query->where('instituicoes_instituicoes.regiao_id', session()->get('session_perfil')->instituicao_id)
                    ->where('instituicoes_instituicoes.tipo_instituicao_id', '!=', 1);
            })
            ->join('instituicoes_instituicoes as ii_pai', 'ii_pai.id', '=', 'instituicoes_instituicoes.instituicao_pai_id') // Realiza o JOIN
            ->get();

        $instituicao = InstituicoesInstituicao::findOrFail($id);
        $ufs = $this->fetchUFs();
        return view('instituicoes.editar', compact('instituicao', 'instituicoes_pai', 'ufs'));
    }


    public function update(StoreReceberNovoRequest $request, string $id)
    {
        try {
            app(UpdateRegiaoService::class)->execute($request, $id);
        } catch (ValidationException $e) {
            $primeiraMensagem = collect($e->errors())->flatten()->first() ?? 'Não foi possível inativar a instituição.';
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', $primeiraMensagem);
        }

        return redirect()->route('instituicoes-regiao.index')->with('success', 'Instituição editado com sucesso!');
    }



    public function deletar($id, Request $request)
    {
        $searchTerm = $request->input('search');
        // Lógica para inativar a instituição
        app(DeletarRegiaoService::class)->execute($id);

        return redirect()->route('instituicoes-regiao.index', ['search' => $searchTerm])->with('success', 'Instituição inativada com sucesso.');
    }

    public function ativar($id, Request $request)
    {
        $searchTerm = $request->input('search');
        // Lógica para ativar a instituição
        app(AtivarRegiaoService::class)->execute($id);

        return redirect()->route('instituicoes-regiao.index', ['search' => $searchTerm])->with('success', 'Instituição ativada com sucesso.');
    }

    public function detalhes($id)
    {
        $instituicao = app(DetalhesRegiaoService::class)->execute($id);
        return response()->json($instituicao);
    }

    public function nomeacoes($id, Request $request)
    {
        $data = app(ListaNomeacoesClerigoService::class)->instituicao($id);
        if (!$data['instituicao']) {
            return redirect()->back()->with('error', 'Essa instituição não está ativa. Por favor ative ela e tente novamente');
        }
        
        return view('instituicoes.nomeacoes.index', $data);
    }

    public function novaNomeacao($id)
    {
        $instituicao = InstituicoesInstituicao::where('id', $id)->first();

        $funcoes = PessoaFuncaoministerial::orderBy('funcao')->get();

        $clerigos = app(ListaClerigosService::class)->totalClerigo();
        return view('instituicoes.nomeacoes.novo', compact('instituicao', 'funcoes', 'clerigos'));
    }

    public function storeNomeacao(StoreNomeacoesClerigosInstiruicoesRequest $request)
    {
        app(StoreNomeacoesClerigos::class)->execute($request);

        return redirect()->route('instituicoes-regiao.nomeacoes', $request->instituicao_id)->with('success', 'Nomeação criada com sucesso!');
    }


    public function finalizarNomeacao($instituicao_id ,string $id, FinalizarNomeacoesRequest $request)
    {
        app(FinalizarNomeacoesClerigos::class)->execute($id, $request);
        return redirect()->route('instituicoes-regiao.nomeacoes', $instituicao_id)->with('success', 'Nomeação finalizada com sucesso!');
    }
}
