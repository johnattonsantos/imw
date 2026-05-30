<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioBenfeitoriaRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioBenfeitoriaRequest;
use App\Models\Patrimonio\Benfeitoria;
use App\Models\Patrimonio\Imovel;
use App\Services\Patrimonio\PatrimonioBenfeitoriasService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatrimonioBenfeitoriasController extends Controller
{
    private const STORAGE_DISK = 'local';

    private const STORAGE_PREFIX = 'patrimonio/benfeitorias';

    public function __construct(private readonly PatrimonioBenfeitoriasService $service)
    {
        // $this->middleware('seguranca:patrimonio.visualizar')->only(['index', 'show', 'download']);
        // $this->middleware('seguranca:patrimonio.criar')->only(['create', 'store']);
        // $this->middleware('seguranca:patrimonio.editar')->only(['edit', 'update']);
        // $this->middleware('seguranca:patrimonio.excluir')->only(['destroy']);
    }

    public function index()
    {
        $igrejaId = $this->resolveIgrejaId();

        $benfeitorias = Benfeitoria::query()
            ->daIgreja($igrejaId)
            ->with('imovel:id,nome')
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->paginate(20);

        return view('patrimonio.benfeitorias.index', compact('benfeitorias'));
    }

    public function create()
    {
        $imoveis = Imovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('id')
            ->get();

        return view('patrimonio.benfeitorias.create', compact('imoveis'));
    }

    public function store(StorePatrimonioBenfeitoriaRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();
        $imovelId = (int) $request->input('imovel_id');

        $this->assertImovelDaIgreja($imovelId, $igrejaId);

        $benfeitoria = DB::transaction(function () use ($request, $igrejaId, $imovelId) {
            $anexoPath = $request->hasFile('documento_anexo')
                ? $this->storeArquivo($request->file('documento_anexo'))
                : null;

            $benfeitoria = Benfeitoria::create([
                ...$request->validated(),
                'igreja_id' => $igrejaId,
                'imovel_id' => $imovelId,
                'documento_anexo' => $anexoPath,
            ]);

            $this->service->aplicarDeltaValorHistorico($imovelId, (float) $benfeitoria->valor_investido);

            return $benfeitoria;
        });

        $this->logAcao('benfeitorias.store', [
            'benfeitoria_id' => $benfeitoria->id,
            'imovel_id' => $imovelId,
            'igreja_id' => $igrejaId,
            'valor_investido' => $benfeitoria->valor_investido,
        ]);

        return redirect()->route('patrimonio.benfeitorias.index')->with('success', 'Benfeitoria cadastrada com sucesso.');
    }

    public function show(Benfeitoria $benfeitoria)
    {
        $this->authorizeByIgreja($benfeitoria);

        $benfeitoria->load('imovel:id,nome,valor_historico');

        return view('patrimonio.benfeitorias.show', compact('benfeitoria'));
    }

    public function edit(Benfeitoria $benfeitoria)
    {
        $this->authorizeByIgreja($benfeitoria);

        $imoveis = Imovel::query()
            ->daIgreja($this->resolveIgrejaId())
            ->orderBy('id')
            ->get();

        return view('patrimonio.benfeitorias.edit', compact('benfeitoria', 'imoveis'));
    }

    public function update(UpdatePatrimonioBenfeitoriaRequest $request, Benfeitoria $benfeitoria)
    {
        $this->authorizeByIgreja($benfeitoria);

        $igrejaId = $this->resolveIgrejaId();
        $novoImovelId = (int) $request->input('imovel_id');

        $this->assertImovelDaIgreja($novoImovelId, $igrejaId);

        $before = $benfeitoria->only(['imovel_id', 'valor_investido', 'data', 'responsavel']);

        DB::transaction(function () use ($request, $benfeitoria, $novoImovelId) {
            $imovelIdAnterior = (int) $benfeitoria->imovel_id;
            $valorAnterior = (float) $benfeitoria->valor_investido;

            $data = $request->validated();

            if ($request->hasFile('documento_anexo')) {
                $this->deleteArquivoIfExists($benfeitoria->documento_anexo);
                $data['documento_anexo'] = $this->storeArquivo($request->file('documento_anexo'));
            }

            $benfeitoria->update($data);

            $valorNovo = (float) $benfeitoria->valor_investido;

            if ($imovelIdAnterior !== $novoImovelId) {
                $this->service->aplicarDeltaValorHistorico($imovelIdAnterior, -$valorAnterior);
                $this->service->aplicarDeltaValorHistorico($novoImovelId, $valorNovo);

                return;
            }

            $this->service->aplicarDeltaValorHistorico($novoImovelId, $valorNovo - $valorAnterior);
        });

        $this->logAcao('benfeitorias.update', [
            'benfeitoria_id' => $benfeitoria->id,
            'before' => $before,
            'after' => $benfeitoria->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.benfeitorias.index')->with('success', 'Benfeitoria atualizada com sucesso.');
    }

    public function destroy(Benfeitoria $benfeitoria)
    {
        $this->authorizeByIgreja($benfeitoria);

        $payload = $benfeitoria->only(['id', 'igreja_id', 'imovel_id', 'valor_investido']);

        DB::transaction(function () use ($benfeitoria) {
            $imovelId = (int) $benfeitoria->imovel_id;
            $valor = (float) $benfeitoria->valor_investido;

            $this->deleteArquivoIfExists($benfeitoria->documento_anexo);
            $benfeitoria->delete();
            $this->service->aplicarDeltaValorHistorico($imovelId, -$valor);
        });

        $this->logAcao('benfeitorias.destroy', ['benfeitoria' => $payload]);

        return redirect()->route('patrimonio.benfeitorias.index')->with('success', 'Benfeitoria excluída com sucesso.');
    }

    public function download(Benfeitoria $benfeitoria)
    {
        $this->authorizeByIgreja($benfeitoria);

        abort_if(! $this->arquivoValido($benfeitoria->documento_anexo), 404);

        $ext = pathinfo((string) $benfeitoria->documento_anexo, PATHINFO_EXTENSION);
        $nome = $ext !== '' ? 'benfeitoria-' . $benfeitoria->id . '.' . $ext : 'benfeitoria-' . $benfeitoria->id;

        return Storage::disk(self::STORAGE_DISK)->download(
            $benfeitoria->documento_anexo,
            $nome
        );
    }

    private function assertImovelDaIgreja(int $imovelId, int $igrejaId): void
    {
        $imovel = Imovel::query()->findOrFail($imovelId);

        if ((int) $imovel->igreja_id !== $igrejaId) {
            abort(403, 'Imóvel não pertence à igreja ativa.');
        }
    }

    private function authorizeByIgreja(Benfeitoria $benfeitoria): void
    {
        if ((int) $benfeitoria->igreja_id !== $this->resolveIgrejaId()) {
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
