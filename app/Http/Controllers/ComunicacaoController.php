<?php

namespace App\Http\Controllers;

use App\Exports\ComunicacaoExport;
use App\Models\Comunicacao;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ComunicacaoController extends Controller
{
    public function index(Request $request)
    {
        $this->ensurePerfilComunicacao();

        $comunicacoes = $this->buildQuery($request)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('comunicacao.index', compact('comunicacoes'));
    }

    public function create()
    {
        $this->ensurePerfilComunicacao();

        return view('comunicacao.create');
    }

    public function store(Request $request)
    {
        $this->ensurePerfilComunicacao();

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'arquivo' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = null;
        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('comunicacao', 'public');
        }

        Comunicacao::create([
            'instituicao_id' => $this->instituicaoId(),
            'titulo' => $validated['titulo'],
            'comentario' => $validated['comentario'],
            'arquivo' => $path,
        ]);

        return redirect()->route('comunicacao.create')->with('success', 'Comunicacao cadastrada com sucesso.');
    }

    public function show(Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        return view('comunicacao.show', compact('comunicacao'));
    }

    public function edit(Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        return view('comunicacao.edit', compact('comunicacao'));
    }

    public function update(Request $request, Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'comentario' => ['required', 'string'],
            'arquivo' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = $comunicacao->arquivo;
        if ($request->hasFile('arquivo')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('arquivo')->store('comunicacao', 'public');
        }

        $comunicacao->update([
            'titulo' => $validated['titulo'],
            'comentario' => $validated['comentario'],
            'arquivo' => $path,
        ]);

        return redirect()->route('comunicacao.index')->with('success', 'Comunicacao atualizada com sucesso.');
    }

    public function destroy(Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        if ($comunicacao->arquivo) {
            Storage::disk('public')->delete($comunicacao->arquivo);
        }

        $comunicacao->delete();

        return redirect()->route('comunicacao.index')->with('success', 'Comunicacao excluida com sucesso.');
    }

    public function download(Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        abort_if(!$comunicacao->arquivo, 404);

        return Storage::disk('public')->download($comunicacao->arquivo);
    }

    public function visualizar(Comunicacao $comunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($comunicacao);

        abort_if(!$comunicacao->arquivo, 404);
        abort_if(!Storage::disk('public')->exists($comunicacao->arquivo), 404);

        $path = Storage::disk('public')->path($comunicacao->arquivo);
        $mimeType = Storage::disk('public')->mimeType($comunicacao->arquivo) ?: 'application/octet-stream';
        $fileName = basename($comunicacao->arquivo);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function exportXlsx(Request $request)
    {
        $this->ensurePerfilComunicacao();

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
        $this->ensurePerfilComunicacao();

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
            ->with('instituicao')
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

    private function ensurePerfilComunicacao(): void
    {
        $perfilId = (int) optional(session('session_perfil'))->perfil_id;
        abort_if($perfilId !== 3, 403, 'Perfil sem permissao para o modulo Comunicacao.');
    }

    private function instituicaoId(): int
    {
        $instituicaoId = (int) optional(session('session_perfil'))->instituicao_id;
        abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');

        return $instituicaoId;
    }

    private function ensureSameInstituicao(Comunicacao $comunicacao): void
    {
        abort_if($comunicacao->instituicao_id !== $this->instituicaoId(), 403);
    }
}
