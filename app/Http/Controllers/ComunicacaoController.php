<?php

namespace App\Http\Controllers;

use App\Exports\ComunicacaoExport;
use App\Models\CategoriaComunicacao;
use App\Models\Comunicacao;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ComunicacaoController extends Controller
{
    private const ALLOWED_FILE_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

    public function index(Request $request)
    {

        $comunicacoes = $this->buildQuery($request)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();
           // dd($comunicacoes);

        $categorias = $this->categorias();

        return view('comunicacao.index', compact('comunicacoes', 'categorias'));
    }

    public function create()
    {

        $categorias = $this->categorias();

        return view('comunicacao.create', compact('categorias'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'categoria_comunicacao_id' => [
                'required',
                'integer',
                Rule::exists('categoria_comunicacao', 'id')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
            'arquivo' => ['nullable', 'file', 'mimes:' . implode(',', self::ALLOWED_FILE_EXTENSIONS), 'max:10240'],
        ], [
            'arquivo.mimes' => 'Arquivo invalido. Envie apenas PDF, imagem, Word, Excel, ZIP ou RAR.',
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

        return view('comunicacao.edit', compact('comunicacao', 'categorias'));
    }

    public function update(Request $request, Comunicacao $comunicacao)
    {
        $this->ensureSameInstituicao($comunicacao);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'categoria_comunicacao_id' => [
                'required',
                'integer',
                Rule::exists('categoria_comunicacao', 'id')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
            'arquivo' => ['nullable', 'file', 'mimes:' . implode(',', self::ALLOWED_FILE_EXTENSIONS), 'max:10240'],
        ], [
            'arquivo.mimes' => 'Arquivo invalido. Envie apenas PDF, imagem, Word, Excel, ZIP ou RAR.',
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
        $instituicaoId = (int) session('session_perfil')->instituicoes->regiao->id;
        abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');

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
}
