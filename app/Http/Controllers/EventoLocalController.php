<?php

namespace App\Http\Controllers;

use App\Models\EventoLocal;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventoLocalController extends Controller
{
    public function index(Request $request)
    {
        $regiaoId = $this->regiaoId();

        $locais = EventoLocal::query()
            ->where('regiao_id', $regiaoId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', '%' . $search . '%')
                        ->orWhere('endereco', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('eventos.locais.index', compact('locais'));
    }

    public function create()
    {
        $local = new EventoLocal(['ativo' => true]);

        return view('eventos.locais.create', compact('local'));
    }

    public function store(Request $request)
    {
        $local = EventoLocal::create($this->validatedData($request));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Local do evento cadastrado com sucesso.',
                'local' => [
                    'id' => $local->id,
                    'nome' => $local->nome,
                    'label' => $local->endereco ? $local->nome . ' - ' . $local->endereco : $local->nome,
                ],
            ]);
        }

        return redirect()->route('eventos.locais.index')->with('success', 'Local do evento cadastrado com sucesso.');
    }

    public function edit(EventoLocal $local)
    {
        $this->ensureSameRegiao($local);

        return view('eventos.locais.edit', compact('local'));
    }

    public function update(Request $request, EventoLocal $local)
    {
        $this->ensureSameRegiao($local);
        $local->update($this->validatedData($request, $local));

        return redirect()->route('eventos.locais.index')->with('success', 'Local do evento atualizado com sucesso.');
    }

    public function destroy(EventoLocal $local)
    {
        $this->ensureSameRegiao($local);

        if ($local->eventos()->exists()) {
            return redirect()
                ->route('eventos.locais.index')
                ->with('error', 'Não é possível excluir este local, pois ele está vinculado a eventos.');
        }

        $local->delete();

        return redirect()->route('eventos.locais.index')->with('success', 'Local do evento excluído com sucesso.');
    }

    private function validatedData(Request $request, ?EventoLocal $local = null): array
    {
        $regiaoId = $this->regiaoId();

        return $request->validate([
            'nome' => [
                'required',
                'string',
                'max:180',
                Rule::unique('evento_locais', 'nome')
                    ->ignore($local?->id)
                    ->where(fn ($query) => $query->where('regiao_id', $regiaoId)->whereNull('deleted_at')),
            ],
            'endereco' => ['nullable', 'string', 'max:180'],
            'observacoes' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ], [
            'nome.required' => 'Informe o nome do local do evento.',
            'nome.unique' => 'Já existe um local de evento com este nome nesta região.',
        ]) + [
            'regiao_id' => $regiaoId,
            'ativo' => false,
        ];
    }

    private function ensureSameRegiao(EventoLocal $local): void
    {
        abort_if((int) $local->regiao_id !== $this->regiaoId(), 403);
    }

    private function regiaoId(): int
    {
        $instituicaoId = (int) data_get(session('session_perfil'), 'instituicao_id', 0);
        abort_if($instituicaoId <= 0, 403, 'Instituição não encontrada na sessão.');

        $regiaoId = (int) data_get(session('session_perfil'), 'instituicoes.regiao.id', 0);

        if ($regiaoId > 0) {
            return $regiaoId;
        }

        $currentId = $instituicaoId;
        $maxDepth = 10;

        while ($currentId > 0 && $maxDepth-- > 0) {
            $instituicao = InstituicoesInstituicao::query()
                ->select(['id', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id'])
                ->find($currentId);

            abort_if(!$instituicao, 403, 'Instituição não encontrada.');

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
                return (int) $instituicao->id;
            }

            if (!empty($instituicao->regiao_id)) {
                return (int) $instituicao->regiao_id;
            }

            $currentId = (int) ($instituicao->instituicao_pai_id ?? 0);
        }

        abort(403, 'Região não encontrada para o perfil logado.');
    }
}
