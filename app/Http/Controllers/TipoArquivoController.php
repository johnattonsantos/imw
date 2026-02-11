<?php

namespace App\Http\Controllers;

use App\Models\TipoArquivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoArquivoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoArquivo::query();

        if ($request->filled('search')) {
            $search = strtolower(trim((string) $request->input('search')));
            $query->where('extensao', 'like', '%' . $search . '%');
        }

        $tipos = $query->orderBy('extensao')
            ->paginate(15)
            ->withQueryString();

        return view('tipo-arquivo.index', compact('tipos'));
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
                Rule::unique('tipo_arquivo', 'extensao'),
            ],
        ], [
            'extensao.regex' => 'Informe apenas a extensão do arquivo sem ponto (ex: pdf, docx, xlsx).',
        ]);

        $tipo = TipoArquivo::create([
            'extensao' => strtolower(trim((string) $validated['extensao'])),
        ]);

        return response()->json([
            'message' => 'Tipo de arquivo criado com sucesso.',
            'data' => $this->formatTipo($tipo),
        ]);
    }

    public function show(TipoArquivo $tipoArquivo)
    {

        return response()->json([
            'data' => $this->formatTipo($tipoArquivo),
        ]);
    }

    public function update(Request $request, TipoArquivo $tipoArquivo)
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
                Rule::unique('tipo_arquivo', 'extensao')
                    ->ignore($tipoArquivo->id),
            ],
        ], [
            'extensao.regex' => 'Informe apenas a extensão do arquivo sem ponto (ex: pdf, docx, xlsx).',
        ]);

        $tipoArquivo->update([
            'extensao' => strtolower(trim((string) $validated['extensao'])),
        ]);

        return response()->json([
            'message' => 'Tipo de arquivo atualizado com sucesso.',
            'data' => $this->formatTipo($tipoArquivo->fresh()),
        ]);
    }

    public function destroy(TipoArquivo $tipoArquivo)
    {

        $tipoArquivo->delete();

        return response()->json([
            'message' => 'Tipo de arquivo excluido com sucesso.',
        ]);
    }

    private function formatTipo(TipoArquivo $tipoArquivo): array
    {
        return [
            'id' => $tipoArquivo->id,
            'extensao' => $tipoArquivo->extensao,
            'created_at' => optional($tipoArquivo->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
