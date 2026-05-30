<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioBemMovelRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioBemMovelRequest;
use App\Models\Patrimonio\BemMovel;
use App\Models\Patrimonio\Imovel;
use App\Models\Patrimonio\PatrimonioConfiguracao;
use App\Services\Patrimonio\DepreciacaoService;
use Illuminate\Support\Facades\Log;

class PatrimonioBensMoveisController extends Controller
{
    public function __construct(private readonly DepreciacaoService $depreciacaoService)
    {
        // $this->middleware('seguranca:patrimonio.visualizar')->only(['index', 'show']);
        // $this->middleware('seguranca:patrimonio.criar')->only(['create', 'store']);
        // $this->middleware('seguranca:patrimonio.editar')->only(['edit', 'update']);
        // $this->middleware('seguranca:patrimonio.excluir')->only(['destroy']);
    }

    public function index()
    {
        $igrejaId = $this->resolveIgrejaId();

        $bensMoveis = BemMovel::query()
            ->daIgreja($igrejaId)
            ->with('imovel:id,nome')
            ->orderByDesc('id')
            ->paginate(20);

        $bensMoveis->getCollection()->transform(function (BemMovel $bemMovel) {
            $bemMovel->depreciacao = $this->depreciacaoService->aplicarNoBemMovel($bemMovel);

            return $bemMovel;
        });

        return view('patrimonio.bens-moveis.index', compact('bensMoveis'));
    }

    public function create()
    {
        $igrejaId = $this->resolveIgrejaId();
        $imoveis = Imovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();

        return view('patrimonio.bens-moveis.create', [
            'imoveis' => $imoveis,
            'categorias' => $this->configuracoesAtivasPorTipo('categoria'),
            'comprobatorios' => $this->configuracoesAtivasPorTipo('comprobatorio'),
        ]);
    }

    public function store(StorePatrimonioBemMovelRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();
        $payload = $request->validated();

        $this->assertImovelSeInformado($payload['imovel_id'] ?? null, $igrejaId);

        $bemMovel = BemMovel::create([
            ...$payload,
            'igreja_id' => $igrejaId,
        ]);

        $this->depreciacaoService->aplicarNoBemMovel($bemMovel);

        $this->logAcao('bens_moveis.store', [
            'bem_movel_id' => $bemMovel->id,
            'igreja_id' => $igrejaId,
            'codigo_patrimonial' => $bemMovel->codigo_patrimonial,
        ]);

        return redirect()->route('patrimonio.bens-moveis.index')->with('success', 'Bem móvel cadastrado com sucesso.');
    }

    public function show(BemMovel $bemMovel)
    {
        $this->authorizeByIgreja($bemMovel);

        $bemMovel->load('imovel:id,nome');
        $depreciacao = $this->depreciacaoService->aplicarNoBemMovel($bemMovel);

        return view('patrimonio.bens-moveis.show', compact('bemMovel', 'depreciacao'));
    }

    public function edit(BemMovel $bemMovel)
    {
        $this->authorizeByIgreja($bemMovel);

        $igrejaId = $this->resolveIgrejaId();
        $imoveis = Imovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();

        return view('patrimonio.bens-moveis.edit', [
            'bemMovel' => $bemMovel,
            'imoveis' => $imoveis,
            'categorias' => $this->configuracoesAtivasPorTipo('categoria'),
            'comprobatorios' => $this->configuracoesAtivasPorTipo('comprobatorio'),
        ]);
    }

    public function update(UpdatePatrimonioBemMovelRequest $request, BemMovel $bemMovel)
    {
        $this->authorizeByIgreja($bemMovel);

        $payload = $request->validated();
        $this->assertImovelSeInformado($payload['imovel_id'] ?? null, $this->resolveIgrejaId());

        $before = $bemMovel->only(['nome', 'status', 'imovel_id', 'valor_aquisicao', 'valor_residual', 'vida_util']);

        $bemMovel->update($payload);
        $this->depreciacaoService->aplicarNoBemMovel($bemMovel);

        $this->logAcao('bens_moveis.update', [
            'bem_movel_id' => $bemMovel->id,
            'before' => $before,
            'after' => $bemMovel->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.bens-moveis.index')->with('success', 'Bem móvel atualizado com sucesso.');
    }

    public function destroy(BemMovel $bemMovel)
    {
        $this->authorizeByIgreja($bemMovel);

        $payload = $bemMovel->only(['id', 'igreja_id', 'codigo_patrimonial', 'nome', 'status']);
        $bemMovel->delete();

        $this->logAcao('bens_moveis.destroy', ['bem_movel' => $payload]);

        return redirect()->route('patrimonio.bens-moveis.index')->with('success', 'Bem móvel excluído com sucesso.');
    }

    private function assertImovelSeInformado(?int $imovelId, int $igrejaId): void
    {
        if (empty($imovelId)) {
            return;
        }

        $imovel = Imovel::query()->findOrFail($imovelId);

        if ((int) $imovel->igreja_id !== $igrejaId) {
            abort(403, 'Imóvel vinculado não pertence à igreja ativa.');
        }
    }

    private function authorizeByIgreja(BemMovel $bemMovel): void
    {
        if ((int) $bemMovel->igreja_id !== (int) $this->resolveIgrejaId()) {
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
