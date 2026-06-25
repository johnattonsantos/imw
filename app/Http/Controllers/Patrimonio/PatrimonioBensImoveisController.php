<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioBemImovelRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioBemImovelRequest;
use App\Models\Patrimonio\Imovel;
use App\Models\Patrimonio\PatrimonioConfiguracao;
use Illuminate\Support\Facades\Log;

class PatrimonioBensImoveisController extends Controller
{
    public function __construct()
    {
        // $this->middleware('seguranca:patrimonio.visualizar')->only(['index', 'show']);
        // $this->middleware('seguranca:patrimonio.criar')->only(['create', 'store']);
        // $this->middleware('seguranca:patrimonio.editar')->only(['edit', 'update']);
        // $this->middleware('seguranca:patrimonio.excluir')->only(['destroy']);
    }

    public function index()
    {
        $imoveis = Imovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('nome')
            ->orderByDesc('id')
            ->paginate(20);

        return view('patrimonio.bens-imoveis.index', compact('imoveis'));
    }

    public function create()
    {
        return view('patrimonio.bens-imoveis.create', [
            'naturezas' => $this->naturezasImovel(),
            'statusTitularidades' => $this->statusTitularidades(),
            'iptus' => $this->iptuOptions(),
        ]);
    }

    public function store(StorePatrimonioBemImovelRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();

        $imovel = Imovel::create([
            ...$request->validated(),
            'igreja_id' => $igrejaId,
            'possui_escritura_registrada' => $request->boolean('possui_escritura_registrada'),
        ]);

        $this->logAcao('imoveis.store', [
            'imovel_id' => $imovel->id,
            'igreja_id' => $igrejaId,
            'codigo_patrimonial' => $imovel->codigo_patrimonial,
        ]);

        return redirect()->route('patrimonio.bens-imoveis.index')->with('success', 'Bem imóvel cadastrado com sucesso.');
    }

    public function show(Imovel $bemImovel)
    {
        $this->authorizeByIgreja($bemImovel);

        return view('patrimonio.bens-imoveis.show', ['imovel' => $bemImovel]);
    }

    public function edit(Imovel $bemImovel)
    {
        $this->authorizeByIgreja($bemImovel);

        return view('patrimonio.bens-imoveis.edit', [
            'imovel' => $bemImovel,
            'naturezas' => $this->naturezasImovel(),
            'statusTitularidades' => $this->statusTitularidades(),
            'iptus' => $this->iptuOptions(),
        ]);
    }

    public function update(UpdatePatrimonioBemImovelRequest $request, Imovel $bemImovel)
    {
        $this->authorizeByIgreja($bemImovel);

        $before = $bemImovel->only([
            'codigo_patrimonial',
            'nome',
            'status_titularidade',
            'numero_matricula',
            'possui_escritura_registrada',
            'regularizacao_pendente',
        ]);

        $bemImovel->update([
            ...$request->validated(),
            'possui_escritura_registrada' => $request->boolean('possui_escritura_registrada'),
        ]);

        $this->logAcao('imoveis.update', [
            'imovel_id' => $bemImovel->id,
            'igreja_id' => $bemImovel->igreja_id,
            'before' => $before,
            'after' => $bemImovel->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.bens-imoveis.index')->with('success', 'Bem imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $bemImovel)
    {
        $this->authorizeByIgreja($bemImovel);

        $payload = $bemImovel->only(['id', 'igreja_id', 'codigo_patrimonial', 'nome']);
        $bemImovel->delete();

        $this->logAcao('imoveis.destroy', ['imovel' => $payload]);

        return redirect()->route('patrimonio.bens-imoveis.index')->with('success', 'Bem imóvel excluído com sucesso.');
    }

    private function authorizeByIgreja(Imovel $imovel): void
    {
        if ((int) $imovel->igreja_id !== $this->resolveIgrejaId()) {
            abort(403);
        }
    }

    private function resolveIgrejaId(): int
    {
        $igrejaId = (int) (
            data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id')
            ?? data_get(session('session_perfil'), 'instituicao_id')
            ?? 0
        );

        if ($igrejaId <= 0) {
            abort(403, 'Igreja não identificada na sessão.');
        }

        return $igrejaId;
    }

    private function logAcao(string $acao, array $contexto = []): void
    {
        Log::info('patrimonio.' . $acao, array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ], $contexto));
    }

    private function naturezasImovel()
    {
        return $this->configuracoesAtivasPorTipo('natureza');
    }

    private function statusTitularidades()
    {
        return $this->configuracoesAtivasPorTipo('status');
    }

    private function iptuOptions()
    {
        return $this->configuracoesAtivasPorTipo('iptu');
    }

    private function configuracoesAtivasPorTipo(string $tipo)
    {
        return PatrimonioConfiguracao::query()
            ->doTipo($tipo)
            ->ativos()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get(['id', 'nome']);
    }
}
