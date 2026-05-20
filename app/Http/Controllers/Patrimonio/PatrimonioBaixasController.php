<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioBaixaRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioBaixaRequest;
use App\Models\Patrimonio\BaixaPatrimonial;
use App\Models\Patrimonio\BemMovel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatrimonioBaixasController extends Controller
{
    private const STORAGE_DISK = 'local';

    private const STORAGE_PREFIX = 'patrimonio/baixas';

    public function __construct()
    {
        $this->middleware(['seguranca:patrimonio.baixa', 'seguranca:patrimonio.visualizar'])->only(['index', 'show', 'download']);
        $this->middleware(['seguranca:patrimonio.baixa', 'seguranca:patrimonio.criar'])->only(['create', 'store']);
        $this->middleware(['seguranca:patrimonio.baixa', 'seguranca:patrimonio.editar'])->only(['edit', 'update']);
        $this->middleware(['seguranca:patrimonio.baixa', 'seguranca:patrimonio.excluir'])->only(['destroy']);
    }

    public function index()
    {
        $baixas = BaixaPatrimonial::query()
            ->daIgreja($this->resolveIgrejaId())
            ->with('bemMovel:id,nome,codigo_patrimonial')
            ->orderByDesc('data_baixa')
            ->orderByDesc('id')
            ->paginate(20);

        return view('patrimonio.baixas.index', compact('baixas'));
    }

    public function create()
    {
        $bensMoveis = BemMovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('nome')
            ->get();

        return view('patrimonio.baixas.create', compact('bensMoveis'));
    }

    public function store(StorePatrimonioBaixaRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();
        $bemMovel = $this->resolveBemMovelDaIgreja((int) $request->input('bem_movel_id'), $igrejaId);

        $baixa = DB::transaction(function () use ($request, $igrejaId, $bemMovel) {
            $arquivo = $request->hasFile('documento_comprobatorio')
                ? $this->storeArquivo($request->file('documento_comprobatorio'))
                : null;

            $baixa = BaixaPatrimonial::create([
                ...$request->validated(),
                'igreja_id' => $igrejaId,
                'imovel_id' => $bemMovel->imovel_id,
                'documento_comprobatorio' => $arquivo,
            ]);

            $bemMovel->update(['status' => 'baixado']);

            return $baixa;
        });

        $this->logAcao('baixas.store', [
            'baixa_id' => $baixa->id,
            'igreja_id' => $igrejaId,
            'bem_movel_id' => $baixa->bem_movel_id,
            'data_baixa' => $baixa->data_baixa,
        ]);

        return redirect()->route('patrimonio.baixas.index')->with('success', 'Baixa patrimonial registrada com sucesso.');
    }

    public function show(BaixaPatrimonial $baixa)
    {
        $this->authorizeByIgreja($baixa);

        $baixa->load('bemMovel:id,nome,codigo_patrimonial,status');

        return view('patrimonio.baixas.show', compact('baixa'));
    }

    public function edit(BaixaPatrimonial $baixa)
    {
        $this->authorizeByIgreja($baixa);

        $bensMoveis = BemMovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('nome')
            ->get();

        return view('patrimonio.baixas.edit', compact('baixa', 'bensMoveis'));
    }

    public function update(UpdatePatrimonioBaixaRequest $request, BaixaPatrimonial $baixa)
    {
        $this->authorizeByIgreja($baixa);

        $igrejaId = $this->resolveIgrejaId();
        $bemMovel = $this->resolveBemMovelDaIgreja((int) $request->input('bem_movel_id'), $igrejaId);

        $before = $baixa->only(['bem_movel_id', 'motivo', 'data_baixa', 'responsavel']);

        DB::transaction(function () use ($request, $baixa, $bemMovel) {
            $data = [
                ...$request->validated(),
                'imovel_id' => $bemMovel->imovel_id,
            ];

            if ($request->hasFile('documento_comprobatorio')) {
                $this->deleteArquivoIfExists($baixa->documento_comprobatorio);
                $data['documento_comprobatorio'] = $this->storeArquivo($request->file('documento_comprobatorio'));
            }

            $baixa->update($data);
            $bemMovel->update(['status' => 'baixado']);
        });

        $this->logAcao('baixas.update', [
            'baixa_id' => $baixa->id,
            'before' => $before,
            'after' => $baixa->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.baixas.index')->with('success', 'Baixa patrimonial atualizada com sucesso.');
    }

    public function destroy(BaixaPatrimonial $baixa)
    {
        $this->authorizeByIgreja($baixa);

        $payload = $baixa->only(['id', 'igreja_id', 'bem_movel_id', 'data_baixa', 'motivo']);
        $this->deleteArquivoIfExists($baixa->documento_comprobatorio);
        $baixa->delete();

        $this->logAcao('baixas.destroy', ['baixa' => $payload]);

        return redirect()->route('patrimonio.baixas.index')->with('success', 'Baixa patrimonial excluída com sucesso.');
    }

    public function download(BaixaPatrimonial $baixa)
    {
        $this->authorizeByIgreja($baixa);

        abort_if(! $this->arquivoValido($baixa->documento_comprobatorio), 404);

        $ext = pathinfo((string) $baixa->documento_comprobatorio, PATHINFO_EXTENSION);
        $nome = $ext !== '' ? 'baixa-patrimonial-' . $baixa->id . '.' . $ext : 'baixa-patrimonial-' . $baixa->id;

        return Storage::disk(self::STORAGE_DISK)->download(
            $baixa->documento_comprobatorio,
            $nome
        );
    }

    private function authorizeByIgreja(BaixaPatrimonial $baixa): void
    {
        if ((int) $baixa->igreja_id !== $this->resolveIgrejaId()) {
            abort(403);
        }
    }

    private function resolveBemMovelDaIgreja(int $bemMovelId, int $igrejaId): BemMovel
    {
        $bemMovel = BemMovel::query()->findOrFail($bemMovelId);

        if ((int) $bemMovel->igreja_id !== $igrejaId) {
            abort(403, 'Bem móvel não pertence à igreja ativa.');
        }

        return $bemMovel;
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

    private function storeArquivo(UploadedFile $arquivo): string
    {
        return $arquivo->store(self::STORAGE_PREFIX, self::STORAGE_DISK);
    }

    private function deleteArquivoIfExists(?string $arquivo): void
    {
        if (! $this->arquivoValido($arquivo)) {
            return;
        }

        Storage::disk(self::STORAGE_DISK)->delete($arquivo);
    }

    private function arquivoValido(?string $arquivo): bool
    {
        if (empty($arquivo) || ! Str::startsWith($arquivo, self::STORAGE_PREFIX . '/')) {
            return false;
        }

        return Storage::disk(self::STORAGE_DISK)->exists($arquivo);
    }

    private function logAcao(string $acao, array $contexto = []): void
    {
        Log::info('patrimonio.' . $acao, array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ], $contexto));
    }
}
