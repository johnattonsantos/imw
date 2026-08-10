<?php

namespace App\Http\Controllers;

use App\Models\DocumentoIgreja;
use App\Models\DocumentoIgrejaArquivo;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentosIgrejasController extends Controller
{
    use Identifiable;

    private const STORAGE_DISK = 'local';
    private const STORAGE_PREFIX = 'documentos-igrejas';

    public function regionalIndex()
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $documentos = $this->queryByRegiao($regiao->id)
            ->withCount('arquivos')
            ->latest('created_at')
            ->paginate(15);

        return view('documentos-igrejas.regional.index', compact('documentos'));
    }

    public function create()
    {
        return view('documentos-igrejas.regional.create', [
            'accept' => $this->acceptAttribute(),
            'formatosPermitidos' => $this->formatosPermitidosTexto(),
        ]);
    }

    public function store(Request $request)
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'arquivos' => ['required', 'array', 'min:1'],
            'arquivos.*' => ['required', 'file', 'max:20480', 'mimes:pdf'],
        ], [
            'arquivos.required' => __('Escolha pelo menos um documento.'),
            'arquivos.*.mimes' => __('Arquivo inválido. Envie apenas: :formatos.', ['formatos' => $this->formatosPermitidosTexto()]),
            'arquivos.*.max' => __('Cada documento deve ter no máximo 20 MB.'),
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $regiao, &$storedPaths) {
                $documento = DocumentoIgreja::create([
                    'regiao_id' => $regiao->id,
                    'user_id' => optional(Auth::user())->id,
                    'titulo' => $validated['titulo'],
                ]);

                foreach ($request->file('arquivos', []) as $index => $arquivo) {
                    $extension = strtolower((string) $arquivo->getClientOriginalExtension());
                    $path = self::STORAGE_PREFIX . '/' . $regiao->id . '/' . (string) Str::uuid() . '.' . $extension;

                    Storage::disk(self::STORAGE_DISK)->put($path, file_get_contents($arquivo->getRealPath()));
                    $storedPaths[] = $path;

                    $documento->arquivos()->create([
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'caminho' => $path,
                        'mime_type' => $arquivo->getMimeType(),
                        'tamanho' => $arquivo->getSize(),
                        'ordem' => $index + 1,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk(self::STORAGE_DISK)->delete($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', __('Não foi possível cadastrar o documento. Tente novamente.'));
        }

        return redirect()
            ->route('documentos-igrejas.index')
            ->with('success', __('Documento cadastrado com sucesso.'));
    }

    public function edit(DocumentoIgreja $documento)
    {
        $this->ensureDocumentoFromSessionRegiao($documento);
        $documento->load('arquivos');

        return view('documentos-igrejas.regional.edit', [
            'documento' => $documento,
            'accept' => $this->acceptAttribute(),
            'formatosPermitidos' => $this->formatosPermitidosTexto(),
        ]);
    }

    public function update(Request $request, DocumentoIgreja $documento)
    {
        $this->ensureDocumentoFromSessionRegiao($documento);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'arquivos' => ['nullable', 'array'],
            'arquivos.*' => ['nullable', 'file', 'max:20480', 'mimes:pdf'],
        ], [
            'arquivos.*.mimes' => __('Arquivo inválido. Envie apenas: :formatos.', ['formatos' => $this->formatosPermitidosTexto()]),
            'arquivos.*.max' => __('Cada documento deve ter no máximo 20 MB.'),
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $documento, &$storedPaths) {
                $documento->update([
                    'titulo' => $validated['titulo'],
                ]);

                $nextOrder = (int) $documento->arquivos()->max('ordem');

                foreach ($request->file('arquivos', []) as $index => $arquivo) {
                    if (!$arquivo) {
                        continue;
                    }

                    $extension = strtolower((string) $arquivo->getClientOriginalExtension());
                    $path = self::STORAGE_PREFIX . '/' . $documento->regiao_id . '/' . (string) Str::uuid() . '.' . $extension;

                    Storage::disk(self::STORAGE_DISK)->put($path, file_get_contents($arquivo->getRealPath()));
                    $storedPaths[] = $path;

                    $documento->arquivos()->create([
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'caminho' => $path,
                        'mime_type' => $arquivo->getMimeType(),
                        'tamanho' => $arquivo->getSize(),
                        'ordem' => $nextOrder + $index + 1,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk(self::STORAGE_DISK)->delete($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', __('Não foi possível atualizar o documento. Tente novamente.'));
        }

        return redirect()
            ->route('documentos-igrejas.index')
            ->with('success', __('Documento atualizado com sucesso.'));
    }

    public function destroy(DocumentoIgreja $documento)
    {
        $this->ensureDocumentoFromSessionRegiao($documento);
        $documento->load('arquivos');

        DB::transaction(function () use ($documento) {
            foreach ($documento->arquivos as $arquivo) {
                Storage::disk(self::STORAGE_DISK)->delete($arquivo->caminho);
                $arquivo->delete();
            }

            $documento->delete();
        });

        return redirect()
            ->route('documentos-igrejas.index')
            ->with('success', __('Documento excluído com sucesso.'));
    }

    public function destroyArquivo(DocumentoIgrejaArquivo $arquivo)
    {
        $arquivo->loadMissing('documento');

        abort_if(!$arquivo->documento, 404);
        $this->ensureDocumentoFromSessionRegiao($arquivo->documento);

        Storage::disk(self::STORAGE_DISK)->delete($arquivo->caminho);
        $arquivo->delete();

        return back()->with('success', __('Arquivo excluído com sucesso.'));
    }

    public function localIndex()
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $documentos = $this->queryByRegiao($regiao->id)
            ->latest('created_at')
            ->paginate(15);

        return view('documentos-igrejas.local.index', compact('documentos'));
    }

    public function visualizar(DocumentoIgrejaArquivo $arquivo)
    {
        $arquivo->loadMissing('documento');
        $regiao = Identifiable::fetchtSessionRegiao();

        abort_if(!$arquivo->documento || (int) $arquivo->documento->regiao_id !== (int) $regiao->id, 403);
        abort_if(!Storage::disk(self::STORAGE_DISK)->exists($arquivo->caminho), 404);

        if (!$arquivo->isPreviewable()) {
            return view('documentos-igrejas.visualizacao-indisponivel', compact('arquivo'));
        }

        $fileName = str_replace(['"', "\r", "\n"], '', $arquivo->nome_original);

        return response()->file(Storage::disk(self::STORAGE_DISK)->path($arquivo->caminho), [
            'Content-Type' => $arquivo->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    private function queryByRegiao(int $regiaoId)
    {
        return DocumentoIgreja::query()
            ->where('regiao_id', $regiaoId)
            ->with('arquivos');
    }

    private function ensureDocumentoFromSessionRegiao(DocumentoIgreja $documento): void
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        abort_if((int) $documento->regiao_id !== (int) $regiao->id, 403);
    }

    private function acceptAttribute(): string
    {
        return '.pdf,application/pdf';
    }

    private function formatosPermitidosTexto(): string
    {
        return 'PDF';
    }
}
