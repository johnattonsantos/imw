<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoProposito;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventoController extends Controller
{
    private const STATUS = [
        'planejado' => 'Planejado',
        'confirmado' => 'Confirmado',
        'realizado' => 'Realizado',
        'cancelado' => 'Cancelado',
    ];

    public function index(Request $request)
    {
        $eventos = $this->buildQuery($request)
            ->with(['proposito', 'lider'])
            ->orderBy('data_inicio')
            ->orderBy('hora_inicio')
            ->paginate(15)
            ->withQueryString();

        $propositos = $this->propositos();
        $statusOptions = self::STATUS;

        return view('eventos.index', compact('eventos', 'propositos', 'statusOptions'));
    }

    public function create()
    {
        $evento = new Evento([
            'status' => 'planejado',
            'data_inicio' => now()->toDateString(),
        ]);
        $propositos = $this->propositos();
        $statusOptions = self::STATUS;

        return view('eventos.create', compact('evento', 'propositos', 'statusOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvento($request);

        DB::transaction(function () use ($validated) {
            $evento = Evento::create($this->eventoData($validated));
            $this->syncEquipe($evento, $validated['equipe'] ?? []);
        });

        return redirect()->route('eventos.index')->with('success', 'Evento cadastrado com sucesso.');
    }

    public function show(Evento $evento)
    {
        $this->ensureSameInstituicao($evento);

        $evento->load(['proposito', 'equipe']);
        $statusOptions = self::STATUS;

        return view('eventos.show', compact('evento', 'statusOptions'));
    }

    public function edit(Evento $evento)
    {
        $this->ensureSameInstituicao($evento);

        $evento->load('equipe');
        $propositos = $this->propositos();
        $statusOptions = self::STATUS;

        return view('eventos.edit', compact('evento', 'propositos', 'statusOptions'));
    }

    public function update(Request $request, Evento $evento)
    {
        $this->ensureSameInstituicao($evento);

        $validated = $this->validateEvento($request);

        DB::transaction(function () use ($evento, $validated) {
            $evento->update($this->eventoData($validated));
            $this->syncEquipe($evento, $validated['equipe'] ?? []);
        });

        return redirect()->route('eventos.index')->with('success', 'Evento atualizado com sucesso.');
    }

    public function destroy(Evento $evento)
    {
        $this->ensureSameInstituicao($evento);
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento excluido com sucesso.');
    }

    private function buildQuery(Request $request): Builder
    {
        $query = Evento::query()
            ->where('instituicao_id', $this->instituicaoId());

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function (Builder $q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                    ->orWhere('local', 'like', '%' . $search . '%')
                    ->orWhere('descricao', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('evento_proposito_id')) {
            $query->where('evento_proposito_id', (int) $request->input('evento_proposito_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_inicio', '>=', $request->input('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_inicio', '<=', $request->input('data_fim'));
        }

        return $query;
    }

    private function validateEvento(Request $request): array
    {
        $validated = $request->validate([
            'evento_proposito_id' => ['required', 'integer', Rule::exists('evento_propositos', 'id')->where('ativo', true)],
            'titulo' => ['required', 'string', 'max:180'],
            'descricao' => ['nullable', 'string'],
            'local' => ['nullable', 'string', 'max:180'],
            'data_inicio' => ['required', 'date_format:d/m/Y'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'data_fim' => ['nullable', 'date_format:d/m/Y'],
            'hora_fim' => ['nullable', 'date_format:H:i'],
            'status' => ['required', Rule::in(array_keys(self::STATUS))],
            'observacoes' => ['nullable', 'string'],
            'equipe' => ['nullable', 'array'],
            'equipe.*.nome' => ['nullable', 'string', 'max:150'],
            'equipe.*.funcao' => ['nullable', 'string', 'max:120'],
            'equipe.*.contato' => ['nullable', 'string', 'max:60'],
            'equipe.*.lider' => ['nullable', 'boolean'],
        ], [
            'evento_proposito_id.required' => 'Selecione o propósito do evento.',
            'titulo.required' => 'Informe o nome do evento.',
            'data_inicio.required' => 'Informe a data inicial da agenda.',
            'data_inicio.date_format' => 'Informe a data inicial no formato dd/mm/aaaa.',
            'data_fim.date_format' => 'Informe a data final no formato dd/mm/aaaa.',
            'hora_inicio.date_format' => 'Informe a hora inicial no formato HH:mm.',
            'hora_fim.date_format' => 'Informe a hora final no formato HH:mm.',
        ]);

        if (!empty($validated['data_fim'])) {
            $dataInicio = $this->parsePtBrDate($validated['data_inicio']);
            $dataFim = $this->parsePtBrDate($validated['data_fim']);

            if ($dataFim->lt($dataInicio)) {
                throw ValidationException::withMessages([
                    'data_fim' => 'A data final deve ser igual ou posterior a data inicial.',
                ]);
            }
        }

        return $validated;
    }

    private function eventoData(array $validated): array
    {
        return [
            'instituicao_id' => $this->instituicaoId(),
            'evento_proposito_id' => $validated['evento_proposito_id'],
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'] ?? null,
            'local' => $validated['local'] ?? null,
            'data_inicio' => $this->parsePtBrDate($validated['data_inicio'])->format('Y-m-d'),
            'hora_inicio' => $validated['hora_inicio'] ?? null,
            'data_fim' => !empty($validated['data_fim']) ? $this->parsePtBrDate($validated['data_fim'])->format('Y-m-d') : null,
            'hora_fim' => $validated['hora_fim'] ?? null,
            'status' => $validated['status'],
            'observacoes' => $validated['observacoes'] ?? null,
        ];
    }

    private function syncEquipe(Evento $evento, array $equipe): void
    {
        $evento->equipe()->delete();
        $liderDefinido = false;

        foreach ($equipe as $membro) {
            $nome = trim((string) data_get($membro, 'nome', ''));

            if ($nome === '') {
                continue;
            }

            $lider = !$liderDefinido && (bool) data_get($membro, 'lider', false);
            $liderDefinido = $liderDefinido || $lider;

            $evento->equipe()->create([
                'nome' => $nome,
                'funcao' => trim((string) data_get($membro, 'funcao', '')) ?: null,
                'contato' => trim((string) data_get($membro, 'contato', '')) ?: null,
                'lider' => $lider,
            ]);
        }
    }

    private function propositos()
    {
        return EventoProposito::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    private function parsePtBrDate(string $date): Carbon
    {
        return Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
    }

    private function ensureSameInstituicao(Evento $evento): void
    {
        abort_if((int) $evento->instituicao_id !== $this->instituicaoId(), 403);
    }

    private function instituicaoId(): int
    {
        $instituicaoId = (int) data_get(session('session_perfil'), 'instituicao_id', 0);
        abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');

        return $instituicaoId;
    }
}
