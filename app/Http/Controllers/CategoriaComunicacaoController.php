<?php

namespace App\Http\Controllers;

use App\Models\CategoriaComunicacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaComunicacaoController extends Controller
{
    public function index(Request $request)
    {
        $this->ensurePerfilComunicacao();

        $query = CategoriaComunicacao::query()
            ->where('instituicao_id', $this->instituicaoId());

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('nome', 'like', '%' . $search . '%');
        }

        $categorias = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('categoria-comunicacao.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $this->ensurePerfilComunicacao();

        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categoria_comunicacao', 'nome')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
        ]);

        $categoria = CategoriaComunicacao::create([
            'instituicao_id' => $this->instituicaoId(),
            'nome' => $validated['nome'],
        ]);

        return response()->json([
            'message' => 'Categoria criada com sucesso.',
            'data' => $this->formatCategoria($categoria),
        ]);
    }

    public function show(CategoriaComunicacao $categoriaComunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($categoriaComunicacao);

        return response()->json([
            'data' => $this->formatCategoria($categoriaComunicacao),
        ]);
    }

    public function update(Request $request, CategoriaComunicacao $categoriaComunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($categoriaComunicacao);

        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categoria_comunicacao', 'nome')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId()))
                    ->ignore($categoriaComunicacao->id),
            ],
        ]);

        $categoriaComunicacao->update([
            'nome' => $validated['nome'],
        ]);

        return response()->json([
            'message' => 'Categoria atualizada com sucesso.',
            'data' => $this->formatCategoria($categoriaComunicacao->fresh()),
        ]);
    }

    public function destroy(CategoriaComunicacao $categoriaComunicacao)
    {
        $this->ensurePerfilComunicacao();
        $this->ensureSameInstituicao($categoriaComunicacao);

        if ($categoriaComunicacao->comunicacoes()->exists()) {
            return response()->json([
                'message' => 'Não e possível excluir categoria que já possui comunicações vinculadas.',
            ], 422);
        }

        $categoriaComunicacao->delete();

        return response()->json([
            'message' => 'Categoria excluida com sucesso.',
        ]);
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

    private function ensureSameInstituicao(CategoriaComunicacao $categoriaComunicacao): void
    {
        abort_if($categoriaComunicacao->instituicao_id !== $this->instituicaoId(), 403);
    }

    private function formatCategoria(CategoriaComunicacao $categoriaComunicacao): array
    {
        return [
            'id' => $categoriaComunicacao->id,
            'nome' => $categoriaComunicacao->nome,
            'created_at' => optional($categoriaComunicacao->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
