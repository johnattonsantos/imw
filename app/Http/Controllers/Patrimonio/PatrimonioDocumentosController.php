<?php

namespace App\Http\Controllers\Patrimonio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StorePatrimonioDocumentoRequest;
use App\Http\Requests\Patrimonio\UpdatePatrimonioDocumentoRequest;
use App\Models\Patrimonio\BemMovel;
use App\Models\Patrimonio\DocumentoPatrimonial;
use App\Models\Patrimonio\Imovel;
use App\Models\Patrimonio\PatrimonioConfiguracao;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatrimonioDocumentosController extends Controller
{
    private const STORAGE_DISK = 'local';

    private const STORAGE_PREFIX = 'patrimonio/documentos';

    public function __construct()
    {
        $this->middleware(['seguranca:patrimonio.documentos', 'seguranca:patrimonio.visualizar'])->only(['index', 'show', 'download']);
        $this->middleware(['seguranca:patrimonio.documentos', 'seguranca:patrimonio.criar'])->only(['create', 'store']);
        $this->middleware(['seguranca:patrimonio.documentos', 'seguranca:patrimonio.editar'])->only(['edit', 'update']);
        $this->middleware(['seguranca:patrimonio.documentos', 'seguranca:patrimonio.excluir'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $igrejaId = $this->resolveIgrejaId();

        $documentos = DocumentoPatrimonial::query()
            ->daIgreja($igrejaId)
            ->with('documentavel')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', (string) $request->string('status'));
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $alertaVencimentoCount = DocumentoPatrimonial::query()
            ->daIgreja($igrejaId)
            ->vencendoEmAte30Dias()
            ->count();

        return view('patrimonio.documentos.index', compact('documentos', 'alertaVencimentoCount'));
    }

    public function create()
    {
        $igrejaId = $this->resolveIgrejaId();

        $imoveis = Imovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();
        $bensMoveis = BemMovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();
        $tiposDocumento = $this->configuracoesAtivasPorTipo('tipo_documento');

        return view('patrimonio.documentos.create', compact('imoveis', 'bensMoveis', 'tiposDocumento'));
    }

    public function store(StorePatrimonioDocumentoRequest $request)
    {
        $igrejaId = $this->resolveIgrejaId();

        [$documentavelType, $documentavelId] = $this->mapDocumentavel(
            (string) $request->input('documentavel_type'),
            (int) $request->input('documentavel_id')
        );

        $this->assertDocumentavelDaIgreja($documentavelType, $documentavelId, $igrejaId);

        $documento = DocumentoPatrimonial::create([
            'igreja_id' => $igrejaId,
            'nome' => $request->input('nome'),
            'tipo' => $request->input('tipo'),
            'arquivo' => $this->storeArquivo($request->file('arquivo')),
            'data_emissao' => $request->input('data_emissao'),
            'data_validade' => $request->input('data_validade'),
            'status' => $request->input('status', 'vigente'),
            'observacoes' => $request->input('observacoes'),
            'documentavel_type' => $documentavelType,
            'documentavel_id' => $documentavelId,
        ]);

        $this->logAcao('documentos.store', [
            'documento_id' => $documento->id,
            'igreja_id' => $igrejaId,
            'vinculo_type' => $documentavelType,
            'vinculo_id' => $documentavelId,
        ]);

        return redirect()->route('patrimonio.documentos.index')->with('success', 'Documento patrimonial cadastrado com sucesso.');
    }

    public function show(DocumentoPatrimonial $documento)
    {
        $this->authorizeByIgreja($documento);

        $documento->load('documentavel');

        return view('patrimonio.documentos.show', compact('documento'));
    }

    public function edit(DocumentoPatrimonial $documento)
    {
        $this->authorizeByIgreja($documento);

        $igrejaId = $this->resolveIgrejaId();
        $imoveis = Imovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();
        $bensMoveis = BemMovel::query()->daIgreja($igrejaId)->orderBy('nome')->get();
        $tiposDocumento = $this->configuracoesAtivasPorTipo('tipo_documento');

        return view('patrimonio.documentos.edit', compact('documento', 'imoveis', 'bensMoveis', 'tiposDocumento'));
    }

    public function update(UpdatePatrimonioDocumentoRequest $request, DocumentoPatrimonial $documento)
    {
        $this->authorizeByIgreja($documento);

        [$documentavelType, $documentavelId] = $this->mapDocumentavel(
            (string) $request->input('documentavel_type'),
            (int) $request->input('documentavel_id')
        );

        $this->assertDocumentavelDaIgreja($documentavelType, $documentavelId, $this->resolveIgrejaId());

        $before = $documento->only(['nome', 'tipo', 'status', 'data_validade', 'documentavel_type', 'documentavel_id']);

        $data = [
            'nome' => $request->input('nome'),
            'tipo' => $request->input('tipo'),
            'data_emissao' => $request->input('data_emissao'),
            'data_validade' => $request->input('data_validade'),
            'status' => $request->input('status', 'vigente'),
            'observacoes' => $request->input('observacoes'),
            'documentavel_type' => $documentavelType,
            'documentavel_id' => $documentavelId,
        ];

        if ($request->hasFile('arquivo')) {
            $this->deleteArquivoIfExists($documento->arquivo);
            $data['arquivo'] = $this->storeArquivo($request->file('arquivo'));
        }

        $documento->update($data);

        $this->logAcao('documentos.update', [
            'documento_id' => $documento->id,
            'before' => $before,
            'after' => $documento->only(array_keys($before)),
        ]);

        return redirect()->route('patrimonio.documentos.index')->with('success', 'Documento patrimonial atualizado com sucesso.');
    }

    public function destroy(DocumentoPatrimonial $documento)
    {
        $this->authorizeByIgreja($documento);

        $payload = $documento->only(['id', 'igreja_id', 'nome', 'tipo', 'status']);
        $this->deleteArquivoIfExists($documento->arquivo);
        $documento->delete();

        $this->logAcao('documentos.destroy', ['documento' => $payload]);

        return redirect()->route('patrimonio.documentos.index')->with('success', 'Documento patrimonial excluído com sucesso.');
    }

    public function download(DocumentoPatrimonial $documento)
    {
        $this->authorizeByIgreja($documento);

        abort_if(! $this->arquivoValido($documento->arquivo), 404);

        return Storage::disk(self::STORAGE_DISK)->download(
            $documento->arquivo,
            $this->arquivoNomeDownload($documento)
        );
    }

    private function mapDocumentavel(string $documentavelType, int $documentavelId): array
    {
        return match ($documentavelType) {
            'imovel' => [Imovel::class, $documentavelId],
            'bem_movel' => [BemMovel::class, $documentavelId],
            default => abort(422, 'Tipo de vínculo inválido.'),
        };
    }

    private function assertDocumentavelDaIgreja(string $documentavelType, int $documentavelId, int $igrejaId): void
    {
        $model = $documentavelType::query()->findOrFail($documentavelId);

        if ((int) $model->igreja_id !== $igrejaId) {
            abort(403, 'Vínculo não pertence à igreja ativa.');
        }
    }

    private function authorizeByIgreja(DocumentoPatrimonial $documento): void
    {
        if ((int) $documento->igreja_id !== $this->resolveIgrejaId()) {
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

    private function arquivoNomeDownload(DocumentoPatrimonial $documento): string
    {
        $ext = pathinfo((string) $documento->arquivo, PATHINFO_EXTENSION);
        $base = Str::slug((string) $documento->nome);

        if ($base === '') {
            $base = 'documento-patrimonial-' . $documento->id;
        }

        return $ext !== '' ? ($base . '.' . $ext) : $base;
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
