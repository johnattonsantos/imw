<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioConfiguracaoRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioConfiguracaoRequest;
use App\Models\Patrimonio\PatrimonioConfiguracao;
use Illuminate\Support\Facades\Log;

class PatrimonioConfiguracoesController extends Controller
{
    private const TIPOS = [
        'natureza' => 'Natureza',
        'status' => 'Status',
        'iptu' => 'IPTU',
        'categoria' => 'Categoria',
        'comprobatorio' => 'Comprobatório',
        'tipo_documento' => 'Tipo de documento',
    ];

    public function __construct()
    {
        $this->middleware('seguranca:patrimonio.visualizar')->only(['hub', 'index']);
        $this->middleware('seguranca:patrimonio.criar')->only(['create', 'store']);
        $this->middleware('seguranca:patrimonio.editar')->only(['edit', 'update']);
        $this->middleware('seguranca:patrimonio.excluir')->only(['destroy']);
    }

    public static function tiposPermitidos(): array
    {
        return array_keys(self::TIPOS);
    }

    public function hub()
    {
        $counts = PatrimonioConfiguracao::query()
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        return view('patrimonio.configuracoes.hub', [
            'tipos' => self::TIPOS,
            'counts' => $counts,
        ]);
    }

    public function index(string $tipo)
    {
        $tipo = $this->resolveTipo($tipo);

        $configuracoes = PatrimonioConfiguracao::query()
            ->doTipo($tipo)
            ->orderBy('ordem')
            ->orderBy('nome')
            ->paginate(20);

        return view('patrimonio.configuracoes.index', [
            'tipo' => $tipo,
            'labelTipo' => self::TIPOS[$tipo],
            'configuracoes' => $configuracoes,
        ]);
    }

    public function create(string $tipo)
    {
        $tipo = $this->resolveTipo($tipo);

        return view('patrimonio.configuracoes.create', [
            'tipo' => $tipo,
            'labelTipo' => self::TIPOS[$tipo],
        ]);
    }

    public function store(StorePatrimonioConfiguracaoRequest $request, string $tipo)
    {
        $tipo = $this->resolveTipo($tipo);

        $configuracao = PatrimonioConfiguracao::create([
            'tipo' => $tipo,
            'nome' => (string) $request->input('nome'),
            'descricao' => $request->input('descricao'),
            'ativo' => $request->boolean('ativo', true),
            'ordem' => (int) ($request->input('ordem', 0)),
        ]);

        $this->logAcao('configuracoes.store', [
            'tipo' => $tipo,
            'configuracao_id' => $configuracao->id,
        ]);

        return redirect()
            ->route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo])
            ->with('success', self::TIPOS[$tipo] . ' cadastrada com sucesso.');
    }

    public function edit(string $tipo, PatrimonioConfiguracao $configuracao)
    {
        $tipo = $this->resolveTipo($tipo);
        $this->authorizeConfiguracao($configuracao, $tipo);

        return view('patrimonio.configuracoes.edit', [
            'tipo' => $tipo,
            'labelTipo' => self::TIPOS[$tipo],
            'configuracao' => $configuracao,
        ]);
    }

    public function update(UpdatePatrimonioConfiguracaoRequest $request, string $tipo, PatrimonioConfiguracao $configuracao)
    {
        $tipo = $this->resolveTipo($tipo);
        $this->authorizeConfiguracao($configuracao, $tipo);

        $configuracao->update([
            'nome' => (string) $request->input('nome'),
            'descricao' => $request->input('descricao'),
            'ativo' => $request->boolean('ativo', true),
            'ordem' => (int) ($request->input('ordem', 0)),
        ]);

        $this->logAcao('configuracoes.update', [
            'tipo' => $tipo,
            'configuracao_id' => $configuracao->id,
        ]);

        return redirect()
            ->route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo])
            ->with('success', self::TIPOS[$tipo] . ' atualizada com sucesso.');
    }

    public function destroy(string $tipo, PatrimonioConfiguracao $configuracao)
    {
        $tipo = $this->resolveTipo($tipo);
        $this->authorizeConfiguracao($configuracao, $tipo);

        $configuracao->delete();

        $this->logAcao('configuracoes.destroy', [
            'tipo' => $tipo,
            'configuracao_id' => $configuracao->id,
        ]);

        return redirect()
            ->route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo])
            ->with('success', self::TIPOS[$tipo] . ' excluída com sucesso.');
    }

    private function resolveTipo(string $tipo): string
    {
        if (! array_key_exists($tipo, self::TIPOS)) {
            abort(404);
        }

        return $tipo;
    }

    private function authorizeConfiguracao(PatrimonioConfiguracao $configuracao, string $tipo): void
    {
        if ($configuracao->tipo !== $tipo) {
            abort(404);
        }
    }

    private function logAcao(string $acao, array $contexto = []): void
    {
        Log::info('patrimonio.' . $acao, array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ], $contexto));
    }
}
