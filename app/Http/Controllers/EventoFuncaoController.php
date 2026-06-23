<?php

namespace App\Http\Controllers;

use App\Models\EventoFuncao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventoFuncaoController extends Controller
{
    public function index(Request $request)
    {
        $funcoes = EventoFuncao::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where('nome', 'like', '%' . $search . '%');
            })
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('eventos.funcoes.index', compact('funcoes'));
    }

    public function create()
    {
        $funcao = new EventoFuncao(['ativo' => true]);

        return view('eventos.funcoes.create', compact('funcao'));
    }

    public function store(Request $request)
    {
        EventoFuncao::create($this->validatedData($request));

        return redirect()->route('eventos.funcoes.index')->with('success', 'Função de evento cadastrada com sucesso.');
    }

    public function edit(EventoFuncao $funcao)
    {
        return view('eventos.funcoes.edit', compact('funcao'));
    }

    public function update(Request $request, EventoFuncao $funcao)
    {
        $funcao->update($this->validatedData($request, $funcao));

        return redirect()->route('eventos.funcoes.index')->with('success', 'Função de evento atualizada com sucesso.');
    }

    public function destroy(EventoFuncao $funcao)
    {
        if ($funcao->equipes()->exists()) {
            return redirect()
                ->route('eventos.funcoes.index')
                ->with('error', 'Não é possível excluir esta função, pois ela está vinculada a eventos.');
        }

        $funcao->delete();

        return redirect()->route('eventos.funcoes.index')->with('success', 'Função de evento excluída com sucesso.');
    }

    private function validatedData(Request $request, ?EventoFuncao $funcao = null): array
    {
        return $request->validate([
            'nome' => [
                'required',
                'string',
                'max:120',
                Rule::unique('evento_funcoes', 'nome')->ignore($funcao?->id)->whereNull('deleted_at'),
            ],
            'ativo' => ['nullable', 'boolean'],
        ], [
            'nome.required' => 'Informe o nome da função.',
            'nome.unique' => 'Já existe uma função de evento com este nome.',
        ]) + ['ativo' => false];
    }
}
