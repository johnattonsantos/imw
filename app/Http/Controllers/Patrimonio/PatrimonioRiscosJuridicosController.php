<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioRiscoJuridicoRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioRiscoJuridicoRequest;
use App\Models\Patrimonio\Imovel;
use App\Models\Patrimonio\RiscoJuridico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PatrimonioRiscosJuridicosController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.visualizar'])->only(['index', 'show']);
        // $this->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.criar'])->only(['create', 'store']);
        // $this->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.editar'])->only(['edit', 'update']);
        // $this->middleware(['seguranca:patrimonio.juridico', 'seguranca:patrimonio.excluir'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $igrejaId = $this->resolveIgrejaId();

        $riscos = RiscoJuridico::query()
            ->daIgreja($igrejaId)
            ->with('imovel:id,nome')
            ->when($request->filled('nivel_risco'), function ($query) use ($request) {
                $query->where('nivel_risco', (string) $request->string('nivel_risco'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', (string) $request->string('status'));
            })
            ->orderByRaw("FIELD(nivel_risco, 'critico', 'alto', 'medio', 'baixo')")
            ->orderByDesc('data_identificacao')
            ->paginate(20)
            ->withQueryString();

        return view('patrimonio.riscos-juridicos.index', compact('riscos'));
    }

    public function create()
    {
        $imoveis = Imovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('nome')
            ->get();

        return view('patrimonio.riscos-juridicos.create', compact('imoveis'));
    }

    public function store(StorePatrimonioRiscoJuridicoRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();
        $this->assertImovelDaIgreja((int) $request->input('imovel_id'), $igrejaId);

        $risco = RiscoJuridico::create([
            ...$request->validated(),
            'igreja_id' => $igrejaId,
        ]);

        $this->logAcao('riscos.store', [
            'risco_id' => $risco->id,
            'imovel_id' => $risco->imovel_id,
            'nivel_risco' => $risco->nivel_risco,
            'status' => $risco->status,
        ]);

        return redirect()->route('patrimonio.riscos-juridicos.index')->with('success', 'Risco jurídico cadastrado com sucesso.');
    }

    public function show(RiscoJuridico $riscoJuridico)
    {
        $this->authorizeByIgreja($riscoJuridico);

        $riscoJuridico->load('imovel:id,nome');

        return view('patrimonio.riscos-juridicos.show', compact('riscoJuridico'));
    }

    public function edit(RiscoJuridico $riscoJuridico)
    {
        $this->authorizeByIgreja($riscoJuridico);

        $imoveis = Imovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('nome')
            ->get();

        return view('patrimonio.riscos-juridicos.edit', compact('riscoJuridico', 'imoveis'));
    }

    public function update(UpdatePatrimonioRiscoJuridicoRequest $request, RiscoJuridico $riscoJuridico)
    {
        $this->authorizeByIgreja($riscoJuridico);
        $this->assertImovelDaIgreja((int) $request->input('imovel_id'), $this->resolveIgrejaId());

        $before = $riscoJuridico->only(['imovel_id', 'nivel_risco', 'status', 'data_identificacao']);
        $riscoJuridico->update($request->validated());

        $this->logAcao('riscos.update', [
            'risco_id' => $riscoJuridico->id,
            'before' => $before,
            'after' => $riscoJuridico->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.riscos-juridicos.index')->with('success', 'Risco jurídico atualizado com sucesso.');
    }

    public function destroy(RiscoJuridico $riscoJuridico)
    {
        $this->authorizeByIgreja($riscoJuridico);

        $payload = $riscoJuridico->only(['id', 'igreja_id', 'imovel_id', 'nivel_risco', 'status']);
        $riscoJuridico->delete();

        $this->logAcao('riscos.destroy', ['risco' => $payload]);

        return redirect()->route('patrimonio.riscos-juridicos.index')->with('success', 'Risco jurídico excluído com sucesso.');
    }

    private function assertImovelDaIgreja(int $imovelId, int $igrejaId): void
    {
        $imovel = Imovel::query()->findOrFail($imovelId);

        if ((int) $imovel->igreja_id !== $igrejaId) {
            abort(403, 'Imóvel não pertence à igreja ativa.');
        }
    }

    private function authorizeByIgreja(RiscoJuridico $risco): void
    {
        if ((int) $risco->igreja_id !== $this->resolveIgrejaId()) {
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
}
