<?php

namespace App\Http\Controllers;

use App\Exports\ComunicacaoExport;
use App\Models\CategoriaComunicacao;
use App\Models\Comunicacao;
use App\Models\TipoArquivo;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ComunicacaoController extends Controller
{
    public function index(Request $request)
    {

        $comunicacoes = $this->buildQuery($request)
            ->latest('created_at')
            ->paginate(15);
            //->withQueryString();
           // dd($comunicacoes);

        $categorias = $this->categorias();

        $arquivoAccept = $this->arquivoAcceptAttribute();
        $arquivoFormatosTexto = $this->arquivoFormatosTexto();

        return view('comunicacao.index', compact('comunicacoes', 'categorias', 'arquivoAccept', 'arquivoFormatosTexto'));
    }

    public function create()
    {

        $categorias = $this->categorias();

        $arquivoAccept = $this->arquivoAcceptAttribute();
        $arquivoFormatosTexto = $this->arquivoFormatosTexto();

        return view('comunicacao.create', compact('categorias', 'arquivoAccept', 'arquivoFormatosTexto'));
    }

    public function store(Request $request)
    {

        $allowedExtensions = $this->allowedFileExtensions();
        $this->ensureFileTypeConfiguredForUpload($request, $allowedExtensions);

        $arquivoRules = ['nullable', 'file', 'max:10240'];
        if (!empty($allowedExtensions)) {
            $arquivoRules[] = 'mimes:' . implode(',', $allowedExtensions);
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'categoria_comunicacao_id' => [
                'required',
                'integer',
                Rule::exists('categoria_comunicacao', 'id')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
            'arquivo' => $arquivoRules,
        ], [
            'arquivo.mimes' => 'Arquivo invalido. Envie apenas: ' . $this->arquivoFormatosTexto($allowedExtensions) . '.',
            'categoria_comunicacao_id.required' => 'Selecione uma categoria.',
            'categoria_comunicacao_id.exists' => 'Categoria invalida.',
        ]);

        $path = null;
        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('comunicacao', $this->storageDiskName());
        }

        $comunicacao = Comunicacao::create([
            'instituicao_id' => $this->instituicaoId(),
            'categoria_comunicacao_id' => $validated['categoria_comunicacao_id'],
            'titulo' => $validated['titulo'],
            'comentario' => $validated['comentario'],
            'arquivo' => $path,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Comunicacao cadastrada com sucesso.',
                'data' => $this->formatComunicacao($comunicacao->fresh(['instituicao', 'categoria'])),
            ]);
        }

        return redirect()->route('comunicacao.create')->with('success', 'Comunicacao cadastrada com sucesso.');
    }

    public function show(Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'data' => $this->formatComunicacao($comunicacao->loadMissing(['instituicao', 'categoria'])),
            ]);
        }

        return view('comunicacao.show', compact('comunicacao'));
    }

    public function edit(Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        $categorias = $this->categorias();

        $arquivoAccept = $this->arquivoAcceptAttribute();
        $arquivoFormatosTexto = $this->arquivoFormatosTexto();

        return view('comunicacao.edit', compact('comunicacao', 'categorias', 'arquivoAccept', 'arquivoFormatosTexto'));
    }

    public function update(Request $request, Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        $allowedExtensions = $this->allowedFileExtensions();
        $this->ensureFileTypeConfiguredForUpload($request, $allowedExtensions);

        $arquivoRules = ['nullable', 'file', 'max:10240'];
        if (!empty($allowedExtensions)) {
            $arquivoRules[] = 'mimes:' . implode(',', $allowedExtensions);
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'categoria_comunicacao_id' => [
                'required',
                'integer',
                Rule::exists('categoria_comunicacao', 'id')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
            'arquivo' => $arquivoRules,
        ], [
            'arquivo.mimes' => 'Arquivo invalido. Envie apenas: ' . $this->arquivoFormatosTexto($allowedExtensions) . '.',
            'categoria_comunicacao_id.required' => 'Selecione uma categoria.',
            'categoria_comunicacao_id.exists' => 'Categoria invalida.',
        ]);

        $path = $comunicacao->arquivo;
        if ($request->hasFile('arquivo')) {
            if ($path) {
                $this->storageDisk()->delete($path);
            }
            $path = $request->file('arquivo')->store('comunicacao', $this->storageDiskName());
        }

        $comunicacao->update([
            'categoria_comunicacao_id' => $validated['categoria_comunicacao_id'],
            'titulo' => $validated['titulo'],
            'comentario' => $validated['comentario'],
            'arquivo' => $path,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Comunicacao atualizada com sucesso.',
                'data' => $this->formatComunicacao($comunicacao->fresh(['instituicao', 'categoria'])),
            ]);
        }

        return redirect()->route('comunicacao.index')->with('success', 'Comunicacao atualizada com sucesso.');
    }

    public function destroy(Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        if ($comunicacao->arquivo) {
            $this->storageDisk()->delete($comunicacao->arquivo);
        }

        $comunicacao->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'message' => 'Comunicacao excluida com sucesso.',
            ]);
        }

        return redirect()->route('comunicacao.index')->with('success', 'Comunicacao excluida com sucesso.');
    }

    public function download(Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        abort_if(!$comunicacao->arquivo, 404);

        return $this->storageDisk()->download($comunicacao->arquivo);
    }

    public function visualizar(Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        abort_if(!$comunicacao->arquivo, 404);
        $disk = $this->storageDisk();
        abort_if(!$disk->exists($comunicacao->arquivo), 404);

        if ($this->storageDiskName() === 's3') {
            return redirect()->away($disk->temporaryUrl($comunicacao->arquivo, now()->addMinutes(15)));
        }

        $path = $disk->path($comunicacao->arquivo);
        $mimeType = $disk->mimeType($comunicacao->arquivo) ?: 'application/octet-stream';
        $fileName = basename($comunicacao->arquivo);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function exportXlsx(Request $request)
    {

        $comunicacoes = $this->buildQuery($request)
            ->with('instituicao')
            ->latest('created_at')
            ->get();

        return Excel::download(
            new ComunicacaoExport($comunicacoes),
            'comunicacao_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {

        $comunicacoes = $this->buildQuery($request)
            ->with('instituicao')
            ->latest('created_at')
            ->get();

        $pdf = FacadePdf::loadView('comunicacao.pdf', [
            'comunicacoes' => $comunicacoes,
            'search' => $request->input('search'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('comunicacao_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildQuery(Request $request): Builder
    {
        $query = Comunicacao::query()
            ->with(['instituicao', 'categoria'])
            ->where('instituicao_id', $this->instituicaoId());

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function (Builder $q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                    ->orWhere('comentario', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function instituicaoId(): int
    {
        // $perfil = session('session_perfil');
        // $instituicaoId = (int) (optional($perfil)->instituicao_id ?? data_get($perfil, 'instituicoes.regiao.id', 0));
        // abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');
        $instituicaoId = session('session_perfil')->instituicoes->regiao->id;
        return $instituicaoId;
    }

    private function ensureSameInstituicao(Comunicacao $comunicacao): void
    {
        abort_if($comunicacao->instituicao_id !== $this->instituicaoId(), 403);
    }

    private function categorias()
    {
        return CategoriaComunicacao::query()
            ->where('instituicao_id', $this->instituicaoId())
            ->orderBy('nome')
            ->get();
    }

    private function formatComunicacao(Comunicacao $comunicacao): array
    {
        $arquivoExt = $comunicacao->arquivo
            ? strtolower((string) pathinfo($comunicacao->arquivo, PATHINFO_EXTENSION))
            : null;

        return [
            'id' => $comunicacao->id,
            'titulo' => $comunicacao->titulo,
            'comentario' => $comunicacao->comentario,
            'comentario_texto' => strip_tags($comunicacao->comentario),
            'comentario_html' => $comunicacao->comentario,
            'categoria_comunicacao_id' => $comunicacao->categoria_comunicacao_id,
            'categoria_nome' => optional($comunicacao->categoria)->nome,
            'arquivo' => $comunicacao->arquivo,
            'arquivo_ext' => $arquivoExt,
            'arquivo_nome' => $comunicacao->arquivo ? basename($comunicacao->arquivo) : null,
            'arquivo_visualizar_url' => $comunicacao->arquivo ? route('comunicacao.visualizar', $comunicacao) : null,
            'created_at' => optional($comunicacao->created_at)->format('d/m/Y H:i:s'),
            'instituicao' => optional($comunicacao->instituicao)->nome,
        ];
    }

    private function storageDiskName(): string
    {
        return (string) config('filesystems.comunicacao_disk', 'public');
    }

    private function storageDisk(): FilesystemAdapter
    {
        return Storage::disk($this->storageDiskName());
    }

    private function allowedFileExtensions(): array
    {
        $extensions = TipoArquivo::query()
            ->orderBy('extensao')
            ->pluck('extensao')
            ->map(fn ($value) => strtolower(trim((string) $value)))
            ->map(fn ($value) => ltrim($value, '.'))
            ->filter(fn ($value) => preg_match('/^[a-z0-9]+$/', $value) === 1)
            ->unique()
            ->values()
            ->all();

        return $extensions;
    }

    private function arquivoAcceptAttribute(?array $extensions = null): string
    {
        $extensions = $extensions ?? $this->allowedFileExtensions();

        return collect($extensions)
            ->map(fn ($ext) => '.' . ltrim((string) $ext, '.'))
            ->implode(',');
    }

    private function arquivoFormatosTexto(?array $extensions = null): string
    {
        $extensions = $extensions ?? $this->allowedFileExtensions();

        if (empty($extensions)) {
            return 'Nenhum tipo configurado';
        }

        return collect($extensions)
            ->map(fn ($ext) => Str::upper((string) $ext))
            ->implode(', ');
    }

    private function ensureFileTypeConfiguredForUpload(Request $request, array $allowedExtensions): void
    {
        if (!$request->hasFile('arquivo')) {
            return;
        }

        if (!empty($allowedExtensions)) {
            return;
        }

        throw ValidationException::withMessages([
            'arquivo' => 'Nenhum tipo de arquivo foi configurado. Cadastre ao menos um tipo em "Tipo Arquivo Comunicacao".',
        ]);
    }
}
