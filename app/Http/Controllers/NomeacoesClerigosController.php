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
use App\Traits\RegionalScope;
use Illuminate\Http\Request;

class NomeacoesClerigosController extends Controller
{
    use RegionalScope;

    public function index($id, Request $request)
    {
        $data = app(ListaNomeacoesClerigoService::class)->execute($id, $request->input('status'));
        return view('clerigos.nomeacoes.index', $data);
    }


    public function novo(PessoasPessoa $pessoa)
    {
        $regiaoId = $this->sessionRegiaoId();
        if (!$this->pessoaPertenceRegiao((int) $pessoa->id, $regiaoId)) {
            return redirect()->route('clerigos.index')->with('error', 'Não foi possível abrir nomeações para clérigo de outra região.');
        }

        $instituicoes = InstituicoesInstituicao::whereIn('tipo_instituicao_id', [
            InstituicoesTipoInstituicao::IGREJA_LOCAL,
            InstituicoesTipoInstituicao::DISTRITO,
            InstituicoesTipoInstituicao::REGIAO,
            InstituicoesTipoInstituicao::SECRETARIA,
            InstituicoesTipoInstituicao::SECRETARIA_REGIONAL,
        ])
            ->where(function ($query) use ($regiaoId) {
                $query->where('id', $regiaoId)
                    ->orWhere('regiao_id', $regiaoId)
                    ->orWhere('instituicao_pai_id', $regiaoId)
                    ->orWhereIn('instituicao_pai_id', function ($subquery) use ($regiaoId) {
                        $subquery->select('id')
                            ->from('instituicoes_instituicoes')
                            ->where('instituicao_pai_id', $regiaoId);
                    });
            })
            ->orderBy('nome')
            ->get();

        $funcoes = PessoaFuncaoministerial::orderBy('funcao')->get();

        return view('clerigos.nomeacoes.novo', compact('instituicoes', 'funcoes', 'pessoa'));
    }

    public function store(StoreNomeacoesClerigosRequest $request)
    {
        try {
            app(StoreNomeacoesClerigos::class)->execute($request);
            return redirect()->route('clerigos.nomeacoes.index', ['id' => $request->pessoa_id])->with('success', 'Nomeação criada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Não foi possível salvar a nomeação fora da região do perfil.');
        }
    }


    public function finalizar($clerigoId ,string $id, FinalizarNomeacoesRequest $request)
    {
        try {
            app(FinalizarNomeacoesClerigos::class)->execute($id, $request);
            return redirect()->route('clerigos.nomeacoes.index', ['id' => $clerigoId])->with('success', 'Nomeação finalizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Não foi possível finalizar nomeação fora da região do perfil.');
        }
    }
}
