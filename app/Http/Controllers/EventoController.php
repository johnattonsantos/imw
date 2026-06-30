<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoEquipe;
use App\Models\EventoFuncao;
use App\Models\EventoProposito;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

    public function index()
    {
        $eventos = Evento::query()
            ->whereIn('instituicao_id', $this->allowedEventInstitutionIds())
            ->with(['proposito', 'lider', 'instituicao.instituicaoPai.instituicaoPai'])
            ->orderBy('data_inicio')
            ->orderBy('hora_inicio')
            ->paginate(15);

        $this->appendInstitutionMeta($eventos->getCollection());

        $escopoEvento = $this->eventScopeType();
        $statusOptions = self::STATUS;

        return view('eventos.index', compact('eventos', 'escopoEvento', 'statusOptions'));
    }

    public function agenda()
    {
        $eventos = Evento::query()
            ->whereIn('instituicao_id', $this->allowedEventInstitutionIds())
            ->with(['proposito', 'lider', 'instituicao.instituicaoPai.instituicaoPai'])
            ->orderBy('data_inicio')
            ->orderBy('hora_inicio')
            ->get();

        $this->appendInstitutionMeta($eventos);

        $statusOptions = self::STATUS;
        $agendaEventos = $eventos->map(function (Evento $evento) use ($statusOptions) {
            $hasTime = !empty($evento->hora_inicio) || !empty($evento->hora_fim);
            $startDate = $evento->data_inicio->toDateString();
            $start = $hasTime
                ? $startDate . 'T' . substr((string) ($evento->hora_inicio ?: '00:00:00'), 0, 8)
                : $startDate;
            $end = null;

            if (!$hasTime && $evento->data_fim) {
                // O FullCalendar usa o fim exclusivo para eventos de dia inteiro.
                $end = $evento->data_fim->copy()->addDay()->toDateString();
            } elseif ($hasTime && ($evento->data_fim || $evento->hora_fim)) {
                $endDate = optional($evento->data_fim)->toDateString() ?: $startDate;
                $endTime = substr((string) ($evento->hora_fim ?: '23:59:59'), 0, 8);
                $end = $endDate . 'T' . $endTime;
            }

            $colors = $this->eventStatusColors((string) $evento->status);
            $startTime = $evento->hora_inicio
                ? substr((string) $evento->hora_inicio, 0, 5)
                : null;

            return [
                'id' => (int) $evento->id,
                'title' => trim(($startTime ? $startTime . ' - ' : '') . $evento->titulo),
                'eventName' => $evento->titulo,
                'start' => $start,
                'end' => $end,
                'allDay' => !$hasTime,
                'backgroundColor' => $colors['background'],
                'borderColor' => $colors['border'],
                'textColor' => '#ffffff',
                'detailsUrl' => route('eventos.show', $evento),
                'statusLabel' => $statusOptions[$evento->status] ?? $evento->status,
                'purpose' => optional($evento->proposito)->nome ?: '-',
                'institution' => $evento->evento_instituicao_nome,
                'location' => $evento->local ?: '-',
            ];
        })->values();

        return view('eventos.agenda', compact('agendaEventos', 'statusOptions'));
    }

    public function create()
    {
        $evento = new Evento([
            'status' => 'planejado',
            'data_inicio' => now()->toDateString(),
        ]);
        $propositos = $this->propositos();
        $funcoesEventos = $this->funcoesEventos();
        $instituicoesEvento = $this->instituicoesEventoOptions();
        $statusOptions = self::STATUS;

        return view('eventos.create', compact('evento', 'propositos', 'funcoesEventos', 'instituicoesEvento', 'statusOptions'));
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

        $evento->load(['proposito', 'equipe.eventoFuncao', 'instituicao.instituicaoPai.instituicaoPai']);
        $this->appendInstitutionMeta(collect([$evento]));
        $statusOptions = self::STATUS;

        if (request()->ajax()) {
            return view('eventos._show_modal', compact('evento', 'statusOptions'));
        }

        return view('eventos.show', compact('evento', 'statusOptions'));
    }

    public function edit(Evento $evento)
    {
        $this->ensureSameInstituicao($evento);

        $evento->load('equipe');
        $propositos = $this->propositos();
        $funcoesEventos = $this->funcoesEventos();
        $instituicoesEvento = $this->instituicoesEventoOptions();
        $statusOptions = self::STATUS;

        return view('eventos.edit', compact('evento', 'propositos', 'funcoesEventos', 'instituicoesEvento', 'statusOptions'));
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

    public function relatorio(Request $request)
    {
        $eventos = $this->buildQuery($request)
            ->with(['proposito', 'lider', 'instituicao.instituicaoPai.instituicaoPai'])
            ->orderBy('data_inicio')
            ->orderBy('hora_inicio')
            ->get();

        $this->appendInstitutionMeta($eventos);

        $propositos = $this->propositos();
        $instituicoesEvento = $this->instituicoesEventoOptions();
        $escopoEvento = $this->eventScopeType();
        $statusOptions = self::STATUS;

        return view('eventos.relatorio', compact('eventos', 'propositos', 'instituicoesEvento', 'escopoEvento', 'statusOptions'));
    }

    public function relatorioPessoas(Request $request)
    {
        $allowedInstitutionIds = $this->allowedEventInstitutionIds();

        $pessoas = EventoEquipe::query()
            ->join('eventos', 'eventos.id', '=', 'evento_equipes.evento_id')
            ->whereNull('eventos.deleted_at')
            ->whereIn('eventos.instituicao_id', $allowedInstitutionIds)
            ->when($request->filled('evento_id'), fn ($query) =>
                $query->where('eventos.id', (int) $request->input('evento_id')))
            ->when($request->filled('instituicao_id'), fn ($query) =>
                $query->where('eventos.instituicao_id', (int) $request->input('instituicao_id')))
            ->when($request->filled('evento_funcao_id'), fn ($query) =>
                $query->where('evento_equipes.evento_funcao_id', (int) $request->input('evento_funcao_id')))
            ->when($request->filled('lider'), fn ($query) =>
                $query->where('evento_equipes.lider', (int) $request->input('lider')))
            ->when($request->filled('status'), fn ($query) =>
                $query->where('eventos.status', (string) $request->input('status')))
            ->when($request->filled('data_inicio'), fn ($query) =>
                $query->whereDate('eventos.data_inicio', '>=', $request->input('data_inicio')))
            ->when($request->filled('data_fim'), fn ($query) =>
                $query->whereDate('eventos.data_inicio', '<=', $request->input('data_fim')))
            ->with([
                'evento.proposito',
                'evento.instituicao.instituicaoPai.instituicaoPai',
                'eventoFuncao',
            ])
            ->select('evento_equipes.*')
            ->orderBy('eventos.data_inicio')
            ->orderBy('eventos.hora_inicio')
            ->orderBy('eventos.titulo')
            ->orderBy('evento_equipes.nome')
            ->get();

        $eventos = $pessoas->pluck('evento')->filter()->unique('id')->values();
        $this->appendInstitutionMeta($eventos);

        $eventOptions = Evento::query()
            ->whereIn('instituicao_id', $allowedInstitutionIds)
            ->orderByDesc('data_inicio')
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'data_inicio']);
        $instituicoesEvento = $this->instituicoesEventoOptions();
        $funcoesEventos = $this->funcoesEventos();
        $escopoEvento = $this->eventScopeType();
        $statusOptions = self::STATUS;

        return view('eventos.relatorio-pessoas', compact(
            'pessoas',
            'eventOptions',
            'instituicoesEvento',
            'funcoesEventos',
            'escopoEvento',
            'statusOptions'
        ));
    }

    public function relatorioEventoPdf(Evento $evento)
    {
        $this->ensureSameInstituicao($evento);

        $evento->load(['proposito', 'equipe.eventoFuncao', 'instituicao.instituicaoPai.instituicaoPai']);
        $this->appendInstitutionMeta(collect([$evento]));
        $statusOptions = self::STATUS;
        $filename = 'evento-' . Str::slug($evento->titulo ?: 'relatorio') . '.pdf';

        $pdf = FacadePdf::loadView('eventos.pdf.evento', compact('evento', 'statusOptions'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:10240'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $extension;
        $path = 'eventos/editor/' . date('Y/m') . '/' . $filename;

        $this->editorDisk()->put($path, file_get_contents($file));
        $token = rtrim(strtr(base64_encode($path), '+/', '-_'), '=');

        return response()->json([
            'location' => URL::signedRoute('eventos.editor-image', ['token' => $token]),
        ]);
    }

    public function editorImage(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $base64 = strtr($token, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $path = base64_decode($base64, true);
        $disk = $this->editorDisk();

        if (!is_string($path) || $path === '' || !$disk->exists($path)) {
            abort(404);
        }

        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
        $content = $disk->get($path);

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        $query = Evento::query()
            ->whereIn('instituicao_id', $this->allowedEventInstitutionIds());

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

        if ($request->filled('instituicao_id')) {
            $instituicaoId = (int) $request->input('instituicao_id');
            if (in_array($instituicaoId, $this->allowedEventInstitutionIds(), true)) {
                $query->where('instituicao_id', $instituicaoId);
            }
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
            'instituicao_id' => ['required', 'integer', Rule::in($this->allowedEventInstitutionIds())],
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
            'equipe.*.evento_funcao_id' => ['nullable', 'integer', Rule::exists('evento_funcoes', 'id')->where('ativo', true)->whereNull('deleted_at')],
            'equipe.*.contato' => ['nullable', 'string', 'max:60'],
            'equipe.*.lider' => ['nullable', 'boolean'],
        ], [
            'instituicao_id.required' => 'Selecione a igreja ou congregação do evento.',
            'instituicao_id.in' => 'A instituição selecionada não está disponível para o perfil logado.',
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
            'instituicao_id' => $validated['instituicao_id'],
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
        $funcoesEventos = $this->funcoesEventos()->keyBy('id');

        foreach ($equipe as $membro) {
            $nome = trim((string) data_get($membro, 'nome', ''));

            if ($nome === '') {
                continue;
            }

            $eventoFuncaoId = (int) data_get($membro, 'evento_funcao_id', 0);
            $funcaoEvento = $eventoFuncaoId > 0 ? $funcoesEventos->get($eventoFuncaoId) : null;
            $lider = !$liderDefinido && (bool) data_get($membro, 'lider', false);
            $liderDefinido = $liderDefinido || $lider;

            $evento->equipe()->create([
                'evento_funcao_id' => $funcaoEvento?->id,
                'nome' => $nome,
                'funcao' => $funcaoEvento?->nome,
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

    private function funcoesEventos()
    {
        return EventoFuncao::query()
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
        abort_if(!in_array((int) $evento->instituicao_id, $this->allowedEventInstitutionIds(), true), 403);
    }

    private function instituicaoId(): int
    {
        $instituicaoId = (int) data_get(session('session_perfil'), 'instituicao_id', 0);
        abort_if($instituicaoId <= 0, 403, 'Instituicao nao encontrada na sessao.');

        return $instituicaoId;
    }

    private function currentInstitution(): ?InstituicoesInstituicao
    {
        return InstituicoesInstituicao::query()
            ->select(['id', 'nome', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id', 'ativo'])
            ->find($this->instituicaoId());
    }

    private function eventScopeType(): string
    {
        $instituicao = $this->currentInstitution();
        $tipo = (int) optional($instituicao)->tipo_instituicao_id;

        if ($tipo === InstituicoesTipoInstituicao::REGIAO || $this->isRegionalInstitutionType($tipo)) {
            return 'regiao';
        }

        if ($tipo === InstituicoesTipoInstituicao::DISTRITO) {
            return 'distrito';
        }

        return 'local';
    }

    private function allowedEventInstitutionIds(): array
    {
        return $this->instituicoesEventoOptions()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function instituicoesEventoOptions()
    {
        $instituicao = $this->currentInstitution();
        if (!$instituicao) {
            return collect();
        }

        $tipo = (int) $instituicao->tipo_instituicao_id;

        if ($tipo === InstituicoesTipoInstituicao::REGIAO || $this->isRegionalInstitutionType($tipo)) {
            $regiaoId = $this->resolveRegiaoId((int) $instituicao->id);

            return $regiaoId > 0 ? $this->instituicoesEventoRegiao($regiaoId) : collect();
        }

        if ($tipo === InstituicoesTipoInstituicao::DISTRITO) {
            return $this->instituicoesEventoDistrito((int) $instituicao->id);
        }

        $igrejaId = (int) data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id', 0);
        if ($igrejaId <= 0 && $tipo === InstituicoesTipoInstituicao::IGREJA_LOCAL) {
            $igrejaId = (int) $instituicao->id;
        }
        if ($igrejaId <= 0 && $tipo === InstituicoesTipoInstituicao::CONGREGACAO) {
            $igrejaId = (int) $instituicao->instituicao_pai_id;
        }

        return $igrejaId > 0 ? $this->instituicoesEventoIgreja($igrejaId) : collect();
    }

    private function instituicoesEventoRegiao(int $regiaoId)
    {
        $distritos = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where(function ($query) use ($regiaoId) {
                $query->where('instituicao_pai_id', $regiaoId)
                    ->orWhere('regiao_id', $regiaoId);
            })
            ->where('ativo', 1)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return $distritos->flatMap(function ($distrito) use ($regiaoId) {
            return $this->instituicoesEventoDistrito((int) $distrito->id, $distrito->nome, $regiaoId);
        })->values();
    }

    private function instituicoesEventoDistrito(int $distritoId, ?string $distritoNome = null, ?int $regiaoId = null)
    {
        $igrejas = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('instituicao_pai_id', $distritoId)
            ->when($regiaoId, fn ($query) => $query->where('regiao_id', $regiaoId))
            ->where('ativo', 1)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return $igrejas->flatMap(function ($igreja) use ($distritoId, $distritoNome) {
            return $this->instituicoesEventoIgreja((int) $igreja->id, $igreja->nome, $distritoId, $distritoNome);
        })->values();
    }

    private function instituicoesEventoIgreja(int $igrejaId, ?string $igrejaNome = null, ?int $distritoId = null, ?string $distritoNome = null)
    {
        $igreja = InstituicoesInstituicao::query()
            ->with('instituicaoPai')
            ->find($igrejaId);

        if (!$igreja) {
            return collect();
        }

        $igrejaNome = $igrejaNome ?: $igreja->nome;
        $distritoId = $distritoId ?: (int) $igreja->instituicao_pai_id;
        $distritoNome = $distritoNome ?: optional($igreja->instituicaoPai)->nome;

        $options = collect([
            (object) [
                'id' => (int) $igreja->id,
                'nome' => 'Sede',
                'label' => 'Sede - ' . $igrejaNome,
                'grupo' => $distritoNome ? $distritoNome . ' / ' . $igrejaNome : $igrejaNome,
                'igreja_id' => (int) $igreja->id,
                'igreja_nome' => $igrejaNome,
                'distrito_id' => $distritoId,
                'distrito_nome' => $distritoNome,
                'tipo_instituicao_id' => (int) $igreja->tipo_instituicao_id,
            ],
        ]);

        $congregacoes = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::CONGREGACAO)
            ->where('instituicao_pai_id', $igreja->id)
            ->where('ativo', 1)
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo_instituicao_id']);

        return $options->merge($congregacoes->map(fn ($congregacao) => (object) [
            'id' => (int) $congregacao->id,
            'nome' => $congregacao->nome,
            'label' => 'Congregação - ' . $congregacao->nome,
            'grupo' => $distritoNome ? $distritoNome . ' / ' . $igrejaNome : $igrejaNome,
            'igreja_id' => (int) $igreja->id,
            'igreja_nome' => $igrejaNome,
            'distrito_id' => $distritoId,
            'distrito_nome' => $distritoNome,
            'tipo_instituicao_id' => (int) $congregacao->tipo_instituicao_id,
        ]));
    }

    private function isRegionalInstitutionType(int $tipo): bool
    {
        return in_array($tipo, [
            InstituicoesTipoInstituicao::SECRETARIA_REGIONAL,
            InstituicoesTipoInstituicao::CONTABILIDADE,
            InstituicoesTipoInstituicao::ESTATISTICA,
        ], true);
    }

    private function resolveRegiaoId(int $fallbackInstituicaoId = 0): int
    {
        $sessionPerfil = session('session_perfil');
        $regiaoId = (int) data_get($sessionPerfil, 'instituicoes.regiao.id', 0);

        if ($regiaoId > 0) {
            return $regiaoId;
        }

        $instituicaoId = $fallbackInstituicaoId ?: (int) data_get($sessionPerfil, 'instituicao_id', 0);
        if ($instituicaoId <= 0) {
            return 0;
        }

        return $this->resolveRegiaoByInstituicaoId($instituicaoId);
    }

    private function resolveRegiaoByInstituicaoId(int $instituicaoId): int
    {
        $currentId = $instituicaoId;
        $maxDepth = 10;

        while ($currentId > 0 && $maxDepth-- > 0) {
            $instituicao = InstituicoesInstituicao::query()
                ->select(['id', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id'])
                ->find($currentId);

            if (!$instituicao) {
                return 0;
            }

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
                return (int) $instituicao->id;
            }

            if (!empty($instituicao->regiao_id)) {
                return (int) $instituicao->regiao_id;
            }

            $currentId = (int) ($instituicao->instituicao_pai_id ?? 0);
        }

        return 0;
    }

    private function appendInstitutionMeta($eventos): void
    {
        foreach ($eventos as $evento) {
            $instituicao = $evento->instituicao;
            $igreja = null;
            $distrito = null;
            $local = optional($instituicao)->nome ?: '-';

            if ($instituicao && (int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::CONGREGACAO) {
                $igreja = $instituicao->instituicaoPai;
                $distrito = optional($igreja)->instituicaoPai;
                $local = $instituicao->nome;
            } elseif ($instituicao && (int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::IGREJA_LOCAL) {
                $igreja = $instituicao;
                $distrito = $instituicao->instituicaoPai;
                $local = 'Sede';
            } elseif ($instituicao && (int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::DISTRITO) {
                $distrito = $instituicao;
            }

            $evento->evento_distrito_nome = optional($distrito)->nome ?: '-';
            $evento->evento_igreja_nome = optional($igreja)->nome ?: '-';
            $evento->evento_local_nome = $local ?: '-';
            $evento->evento_instituicao_nome = $instituicao
                ? trim(($evento->evento_igreja_nome !== '-' ? $evento->evento_igreja_nome . ' / ' : '') . $evento->evento_local_nome)
                : '-';
        }
    }

    private function eventStatusColors(string $status): array
    {
        return match ($status) {
            'confirmado' => ['background' => '#1976d2', 'border' => '#125ca4'],
            'realizado' => ['background' => '#27865d', 'border' => '#1d6748'],
            'cancelado' => ['background' => '#c44343', 'border' => '#963131'],
            default => ['background' => '#d48624', 'border' => '#a5661a'],
        };
    }

    private function editorDisk(): FilesystemAdapter
    {
        return Storage::disk((string) Config::get('filesystems.editor_disk', 's3'));
    }
}
