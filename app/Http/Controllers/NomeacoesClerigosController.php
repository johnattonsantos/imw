<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinalizarNomeacoesRequest;
use App\Http\Requests\StoreNomeacoesClerigosRequest;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\PessoaFuncaoministerial;
use App\Models\PessoasPessoa;
use App\Services\ServiceClerigosRegiao\DeletarNomeacoesClerigos;
use App\Services\ServiceClerigosRegiao\FinalizarNomeacoesClerigos;
use App\Services\ServiceClerigosRegiao\ListaNomeacoesClerigoService;
use App\Services\ServiceNomeacoes\StoreNomeacoesClerigos;
use Illuminate\Http\Request;

class NomeacoesClerigosController extends Controller
{
    public function index($id, Request $request)
    {
        $data = app(ListaNomeacoesClerigoService::class)->execute($id, $request->input('status'));
        return view('clerigos.nomeacoes.index', $data);
    }


    public function novo(PessoasPessoa $pessoa)
    {
        $regiaoId = $this->regiaoLogadaId();

        $instituicoes = InstituicoesInstituicao::with('instituicaoPai')
            ->whereIn('tipo_instituicao_id', [
            InstituicoesTipoInstituicao::IGREJA_LOCAL,
            InstituicoesTipoInstituicao::DISTRITO,
            InstituicoesTipoInstituicao::REGIAO,
            InstituicoesTipoInstituicao::SECRETARIA,
            InstituicoesTipoInstituicao::SECRETARIA_REGIONAL,
        ])
            ->where(function ($query) use ($regiaoId) {
                $query->where('instituicoes_instituicoes.id', $regiaoId)
                    ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId)
                    ->orWhereIn('instituicoes_instituicoes.instituicao_pai_id', function ($subQuery) use ($regiaoId) {
                        $subQuery->select('id')
                            ->from('instituicoes_instituicoes')
                            ->where('regiao_id', $regiaoId)
                            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
                    });
            })
            ->orderBy('nome')
            ->get();

        $funcoes = PessoaFuncaoministerial::orderBy('funcao')->get();

        return view('clerigos.nomeacoes.novo', compact('instituicoes', 'funcoes', 'pessoa'));
    }

    public function store(StoreNomeacoesClerigosRequest $request)
    {

        app(StoreNomeacoesClerigos::class)->execute($request);

        return redirect()->route('clerigos.nomeacoes.index', ['id' => $request->pessoa_id])->with('success', 'Nomeação criada com sucesso!');
    }


    public function finalizar($clerigoId ,string $id, FinalizarNomeacoesRequest $request)
    {
        app(FinalizarNomeacoesClerigos::class)->execute($id, $request);
        return redirect()->route('clerigos.nomeacoes.index', ['id' => $clerigoId])->with('success', 'Nomeação finalizada com sucesso!');
    }

    private function regiaoLogadaId(): int
    {
        return (int) session()->get('session_perfil')->instituicao_id;
    }
}
