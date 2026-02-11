<?php

namespace App\Http\Controllers;

use App\Models\TipoArquivoComunicacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoArquivoComunicacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoArquivoComunicacao::query()
            ->where('instituicao_id', $this->instituicaoId());

        if ($request->filled('search')) {
            $search = strtolower(trim((string) $request->input('search')));
            $query->where('extensao', 'like', '%' . $search . '%');
        }

        $tipos = $query->orderBy('extensao')
            ->paginate(15)
            ->withQueryString();

        return view('tipo-arquivo-comunicacao.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'extensao' => strtolower(ltrim(trim((string) $request->input('extensao')), '.')),
        ]);

        $validated = $request->validate([
            'extensao' => [
                'required',
                'string',
                'max:10',
                'regex:/^[a-z0-9]+$/',
                Rule::unique('tipo_arquivo_comunicacao', 'extensao')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId())),
            ],
        ], [
            'extensao.regex' => 'Informe apenas a extensão do arquivo sem ponto (ex: pdf, docx, xlsx).',
        ]);

        $tipo = TipoArquivoComunicacao::create([
            'instituicao_id' => $this->instituicaoId(),
            'extensao' => strtolower(trim((string) $validated['extensao'])),
        ]);

        return response()->json([
            'message' => 'Tipo de arquivo criado com sucesso.',
            'data' => $this->formatTipo($tipo),
        ]);
    }

    public function show(TipoArquivoComunicacao $tipoArquivoComunicacao)
    {
        $this->ensureSameInstituicao($tipoArquivoComunicacao);

        return response()->json([
            'data' => $this->formatTipo($tipoArquivoComunicacao),
        ]);
    }

    public function update(Request $request, TipoArquivoComunicacao $tipoArquivoComunicacao)
    {
        $this->ensureSameInstituicao($tipoArquivoComunicacao);

        $request->merge([
            'extensao' => strtolower(ltrim(trim((string) $request->input('extensao')), '.')),
        ]);

        $validated = $request->validate([
            'extensao' => [
                'required',
                'string',
                'max:10',
                'regex:/^[a-z0-9]+$/',
                Rule::unique('tipo_arquivo_comunicacao', 'extensao')
                    ->where(fn ($query) => $query->where('instituicao_id', $this->instituicaoId()))
                    ->ignore($tipoArquivoComunicacao->id),
            ],
        ], [
            'extensao.regex' => 'Informe apenas a extensão do arquivo sem ponto (ex: pdf, docx, xlsx).',
        ]);

        $tipoArquivoComunicacao->update([
            'extensao' => strtolower(trim((string) $validated['extensao'])),
        ]);

        return response()->json([
            'message' => 'Tipo de arquivo atualizado com sucesso.',
            'data' => $this->formatTipo($tipoArquivoComunicacao->fresh()),
        ]);
    }

    public function destroy(TipoArquivoComunicacao $tipoArquivoComunicacao)
    {
        $this->ensureSameInstituicao($tipoArquivoComunicacao);

        $tipoArquivoComunicacao->delete();

        return response()->json([
            'message' => 'Tipo de arquivo excluido com sucesso.',
        ]);
    }

    private function instituicaoId(): int
    {
        $instituicaoId = (int) optional(session('session_perfil'))->instituicao_id;
        abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');

        return $instituicaoId;
    }

    private function ensureSameInstituicao(TipoArquivoComunicacao $tipoArquivoComunicacao): void
    {
        abort_if($tipoArquivoComunicacao->instituicao_id !== $this->instituicaoId(), 403);
    }

    private function formatTipo(TipoArquivoComunicacao $tipoArquivoComunicacao): array
    {
        return [
            'id' => $tipoArquivoComunicacao->id,
            'extensao' => $tipoArquivoComunicacao->extensao,
            'created_at' => optional($tipoArquivoComunicacao->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
