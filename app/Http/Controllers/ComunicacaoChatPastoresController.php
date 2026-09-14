<?php

namespace App\Http\Controllers;

use App\Models\ComunicacaoChatConversa;
use App\Models\ComunicacaoChatMensagem;
use App\Models\ComunicacaoChatMensagemLeitura;
use App\Models\ComunicacaoChatParticipante;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\PessoaNomeacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ComunicacaoChatPastoresController extends Controller
{
    private array $institutionCache = [];
    private array $institutionScopeCache = [];

    public function index(Request $request)
    {
        return $this->renderChat($request);
    }

    public function show(Request $request, ComunicacaoChatConversa $conversa)
    {
        return $this->renderChat($request, $conversa);
    }

    public function store(Request $request)
    {
        $context = $this->currentPastorContext();
        $this->ensurePessoaVinculada($context);

        $validated = $request->validate([
            'tipo' => ['required', Rule::in([
                ComunicacaoChatConversa::TIPO_PRIVADA,
                ComunicacaoChatConversa::TIPO_DISTRITO,
                ComunicacaoChatConversa::TIPO_REGIAO,
            ])],
            'destinatario_pessoa_id' => ['nullable', 'integer'],
            'distrito_id' => ['nullable', 'integer'],
            'conteudo' => ['required', 'string', 'max:5000'],
        ], [
            'tipo.required' => 'Escolha o destino da conversa.',
            'tipo.in' => 'Destino da conversa invalido.',
            'conteudo.required' => 'Digite a mensagem antes de enviar.',
        ]);

        $eligiblePastors = $this->eligiblePastors($context);
        $participants = collect([$context]);
        $conversationData = [
            'tipo' => $validated['tipo'],
            'status' => ComunicacaoChatConversa::STATUS_ATIVA,
            'remetente_user_id' => auth()->id(),
            'remetente_pessoa_id' => $context['pessoa_id'],
            'regiao_id' => $context['regiao_id'],
        ];

        if ($validated['tipo'] === ComunicacaoChatConversa::TIPO_PRIVADA) {
            $destinatarioId = (int) ($validated['destinatario_pessoa_id'] ?? 0);
            $destinatario = $eligiblePastors->firstWhere('pessoa_id', $destinatarioId);

            if (!$destinatario || $destinatarioId === $context['pessoa_id']) {
                throw ValidationException::withMessages([
                    'destinatario_pessoa_id' => 'Selecione um pastor ativo e dentro do escopo permitido.',
                ]);
            }

            $participants->push($destinatario);
            $conversationData['destinatario_pessoa_id'] = $destinatarioId;
            $conversationData['igreja_id'] = $destinatario['igreja_id'] ?: null;
            $conversationData['distrito_id'] = $destinatario['distrito_id'] ?: null;
            $conversationData['titulo'] = 'Conversa com ' . $destinatario['nome'];
        }

        if ($validated['tipo'] === ComunicacaoChatConversa::TIPO_DISTRITO) {
            $distritoId = (int) ($validated['distrito_id'] ?? 0);
            $participants = $participants->merge($eligiblePastors->where('distrito_id', $distritoId));
            $distrito = $this->institutionById($distritoId);

            if (!$distrito || (int) ($this->resolveInstitutionScope($distrito)['regiao_id'] ?? 0) !== $context['regiao_id']) {
                throw ValidationException::withMessages([
                    'distrito_id' => 'Selecione um distrito ativo da sua região.',
                ]);
            }

            $conversationData['distrito_id'] = $distritoId;
            $conversationData['titulo'] = 'Distrito: ' . $distrito->nome;
        }

        if ($validated['tipo'] === ComunicacaoChatConversa::TIPO_REGIAO) {
            $participants = $participants->merge($eligiblePastors->where('regiao_id', $context['regiao_id']));
            $conversationData['titulo'] = 'Região: ' . $context['regiao_nome'];
        }

        $participants = $participants
            ->filter(fn ($participant) => !empty($participant['pessoa_id']))
            ->unique('pessoa_id')
            ->values();

        if ($participants->count() < 2) {
            throw ValidationException::withMessages([
                'tipo' => 'Nenhum outro pastor ativo foi encontrado para este destino.',
            ]);
        }

        $conversa = DB::transaction(function () use ($conversationData, $participants, $validated, $context) {
            $conversa = ComunicacaoChatConversa::create($conversationData);

            foreach ($participants as $participant) {
                ComunicacaoChatParticipante::create([
                    'conversa_id' => $conversa->id,
                    'user_id' => $participant['user_id'] ?: null,
                    'pessoa_id' => $participant['pessoa_id'],
                    'igreja_id' => $participant['igreja_id'] ?: null,
                    'distrito_id' => $participant['distrito_id'] ?: null,
                    'regiao_id' => $participant['regiao_id'],
                    'lido_em' => $participant['pessoa_id'] === $context['pessoa_id'] ? now() : null,
                ]);
            }

            ComunicacaoChatMensagem::create([
                'conversa_id' => $conversa->id,
                'remetente_user_id' => auth()->id(),
                'remetente_pessoa_id' => $context['pessoa_id'],
                'conteudo' => $validated['conteudo'],
                'status_entrega' => 'enviada',
                'enviado_em' => now(),
            ]);

            return $conversa;
        });

        return redirect()
            ->route('comunicacao.chat-pastores.show', $conversa)
            ->with('success', 'Mensagem enviada com sucesso.');
    }

    public function reply(Request $request, ComunicacaoChatConversa $conversa)
    {
        $context = $this->currentPastorContext();
        $this->ensurePessoaVinculada($context);
        $this->ensureParticipant($conversa, $context);

        abort_if($conversa->status !== ComunicacaoChatConversa::STATUS_ATIVA, 403, 'Esta conversa nao esta ativa.');

        $validated = $request->validate([
            'conteudo' => ['required', 'string', 'max:5000'],
        ], [
            'conteudo.required' => 'Digite a mensagem antes de enviar.',
        ]);

        ComunicacaoChatMensagem::create([
            'conversa_id' => $conversa->id,
            'remetente_user_id' => auth()->id(),
            'remetente_pessoa_id' => $context['pessoa_id'],
            'conteudo' => $validated['conteudo'],
            'status_entrega' => 'enviada',
            'enviado_em' => now(),
        ]);

        $conversa->touch();

        return redirect()
            ->route('comunicacao.chat-pastores.show', $conversa)
            ->with('success', 'Mensagem enviada com sucesso.');
    }

    public function destinatarios(Request $request)
    {
        $context = $this->currentPastorContext();
        $pastores = $this->eligiblePastors($context, [
            'search' => (string) $request->input('search', ''),
            'distrito_id' => (int) $request->input('distrito_id', 0),
            'igreja_id' => (int) $request->input('igreja_id', 0),
        ])
            ->reject(fn ($pastor) => $pastor['pessoa_id'] === $context['pessoa_id'])
            ->values()
            ->map(fn ($pastor) => [
                'id' => $pastor['pessoa_id'],
                'nome' => $pastor['nome'],
                'label' => $pastor['nome'] . ' - ' . ($pastor['igreja_nome'] ?: $pastor['distrito_nome'] ?: $pastor['regiao_nome']),
                'igreja' => $pastor['igreja_nome'],
                'distrito' => $pastor['distrito_nome'],
                'regiao' => $pastor['regiao_nome'],
            ]);

        return response()->json(['data' => $pastores]);
    }

    private function renderChat(Request $request, ?ComunicacaoChatConversa $selectedConversa = null)
    {
        $context = $this->currentPastorContext();
        $pastores = $this->eligiblePastors($context)
            ->reject(fn ($pastor) => $pastor['pessoa_id'] === $context['pessoa_id'])
            ->values();
        $distritos = $pastores
            ->filter(fn ($pastor) => !empty($pastor['distrito_id']))
            ->map(fn ($pastor) => ['id' => $pastor['distrito_id'], 'nome' => $pastor['distrito_nome']])
            ->unique('id')
            ->sortBy('nome')
            ->values();

        $conversas = ComunicacaoChatConversa::query()
            ->with(['ultimaMensagem', 'participantes.pessoa', 'remetentePessoa', 'destinatarioPessoa', 'regiao', 'distrito', 'igreja'])
            ->whereHas('participantes', fn ($query) => $query->where('pessoa_id', $context['pessoa_id']))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', '%' . $search . '%')
                        ->orWhereHas('mensagens', fn ($mq) => $mq->where('conteudo', 'like', '%' . $search . '%'));
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        $this->appendConversationMeta($conversas->getCollection(), $context);

        if (!$selectedConversa && $request->filled('conversa')) {
            $selectedConversa = ComunicacaoChatConversa::find((int) $request->input('conversa'));
        }

        if (!$selectedConversa) {
            $selectedConversa = $conversas->getCollection()->first();
        }

        $mensagens = collect();
        if ($selectedConversa) {
            $this->ensureParticipant($selectedConversa, $context);
            $selectedConversa->loadMissing(['participantes.pessoa', 'remetentePessoa', 'destinatarioPessoa', 'regiao', 'distrito', 'igreja']);
            $this->appendConversationMeta(collect([$selectedConversa]), $context);
            $mensagens = $selectedConversa->mensagens()
                ->with('remetentePessoa')
                ->orderBy('enviado_em')
                ->orderBy('id')
                ->get();
            $this->markAsRead($selectedConversa, $context, $mensagens->pluck('id')->all());
        }

        return view('comunicacao.chat-pastores.index', compact(
            'context',
            'pastores',
            'distritos',
            'conversas',
            'selectedConversa',
            'mensagens'
        ));
    }

    private function currentPastorContext(): array
    {
        $user = auth()->user();
        abort_if(!$user, 403, 'Acesso nao autorizado.');

        $nomeacao = null;
        if (!empty($user->pessoa_id)) {
            $nomeacao = PessoaNomeacao::query()
                ->whereHas('funcaoMinisterial', fn ($query) => $query->where('funcao', 'like', '%pastor%'))
                ->where('pessoa_id', $user->pessoa_id)
                ->whereNull('data_termino')
                ->whereNull('deleted_at')
                ->orderByDesc('data_nomeacao')
                ->first();
        }

        $instituicao = $nomeacao
            ? $this->institutionById((int) $nomeacao->instituicao_id)
            : $this->sessionInstitution();

        $scope = $this->resolveInstitutionScope($instituicao);

        abort_if(empty($scope['regiao_id']), 403, 'Nao foi possivel identificar a regiao para acessar o chat pastoral.');

        return [
            'user_id' => (int) $user->id,
            'pessoa_id' => (int) ($user->pessoa_id ?? 0),
            'nome' => (string) data_get($user, 'pessoa.nome', $user->name),
            'is_pastor' => (bool) $nomeacao,
            'igreja_id' => $scope['igreja_id'],
            'igreja_nome' => $scope['igreja_nome'],
            'distrito_id' => $scope['distrito_id'],
            'distrito_nome' => $scope['distrito_nome'],
            'regiao_id' => $scope['regiao_id'],
            'regiao_nome' => $scope['regiao_nome'],
        ];
    }

    private function ensurePessoaVinculada(array $context): void
    {
        if (!empty($context['pessoa_id'])) {
            return;
        }

        throw ValidationException::withMessages([
            'conteudo' => 'Seu usuario nao possui pessoa vinculada para enviar mensagens no chat pastoral.',
        ]);
    }

    private function eligiblePastors(array $context, array $filters = [])
    {
        $rows = DB::table('pessoas_pessoas as pp')
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pn.pessoa_id', '=', 'pp.id')
                    ->whereNull('pn.deleted_at')
                    ->whereNull('pn.data_termino');
            })
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->leftJoin('users as u', function ($join) {
                $join->on('u.pessoa_id', '=', 'pp.id')
                    ->whereNull('u.deleted_at');
            })
            ->whereNull('pp.deleted_at')
            ->where('pf.funcao', 'like', '%pastor%')
            ->select([
                'pp.id as pessoa_id',
                'pp.nome',
                'pp.cpf',
                'pn.instituicao_id',
                'pf.funcao as funcao_ministerial',
                DB::raw('MIN(u.id) as user_id'),
                DB::raw('MAX(pn.data_nomeacao) as ultima_nomeacao'),
            ])
            ->groupBy('pp.id', 'pp.nome', 'pp.cpf', 'pn.instituicao_id', 'pf.funcao')
            ->orderBy('pp.nome')
            ->get();

        $pastores = $rows->map(function ($row) {
            $scope = $this->resolveInstitutionScope($this->institutionById((int) $row->instituicao_id));

            if (empty($scope['regiao_id'])) {
                return null;
            }

            return [
                'pessoa_id' => (int) $row->pessoa_id,
                'user_id' => (int) ($row->user_id ?? 0),
                'nome' => (string) $row->nome,
                'cpf_digits' => preg_replace('/\D/', '', (string) $row->cpf),
                'funcao_ministerial' => (string) ($row->funcao_ministerial ?? ''),
                'igreja_id' => $scope['igreja_id'],
                'igreja_nome' => $scope['igreja_nome'],
                'distrito_id' => $scope['distrito_id'],
                'distrito_nome' => $scope['distrito_nome'],
                'regiao_id' => $scope['regiao_id'],
                'regiao_nome' => $scope['regiao_nome'],
            ];
        })
            ->filter()
            ->filter(fn ($pastor) => $pastor['regiao_id'] === $context['regiao_id'])
            ->when(!empty($filters['distrito_id']), fn ($items) => $items->where('distrito_id', (int) $filters['distrito_id']))
            ->when(!empty($filters['igreja_id']), fn ($items) => $items->where('igreja_id', (int) $filters['igreja_id']))
            ->unique('pessoa_id')
            ->values();

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $normalizedSearch = $this->normalizeText($search);
            $digits = preg_replace('/\D/', '', $search);

            $pastores = $pastores->filter(function ($pastor) use ($normalizedSearch, $digits) {
                $haystack = $this->normalizeText(implode(' ', [
                    $pastor['nome'],
                    $pastor['funcao_ministerial'],
                    $pastor['igreja_nome'],
                    $pastor['distrito_nome'],
                    $pastor['regiao_nome'],
                ]));

                return str_contains($haystack, $normalizedSearch)
                    || ($digits !== '' && str_contains($pastor['cpf_digits'], $digits));
            })->values();
        }

        return $pastores->sortBy('nome')->values();
    }

    private function appendConversationMeta($conversas, array $context): void
    {
        $conversas->each(function (ComunicacaoChatConversa $conversa) use ($context) {
            $participant = $conversa->participantes->firstWhere('pessoa_id', $context['pessoa_id']);
            $lastMessage = $conversa->ultimaMensagem;
            $conversa->chat_nao_lida = $participant && $lastMessage && (!$participant->lido_em || $participant->lido_em->lt($lastMessage->enviado_em));
            $conversa->chat_titulo = $this->conversationTitle($conversa, $context);
            $conversa->chat_subtitulo = $this->conversationSubtitle($conversa);
        });
    }

    private function conversationTitle(ComunicacaoChatConversa $conversa, array $context): string
    {
        if ($conversa->tipo === ComunicacaoChatConversa::TIPO_PRIVADA) {
            $other = $conversa->participantes
                ->first(fn ($participant) => (int) $participant->pessoa_id !== $context['pessoa_id']);

            return optional(optional($other)->pessoa)->nome ?: ($conversa->titulo ?: 'Conversa privada');
        }

        return $conversa->titulo ?: match ($conversa->tipo) {
            ComunicacaoChatConversa::TIPO_DISTRITO => 'Conversa do distrito',
            ComunicacaoChatConversa::TIPO_REGIAO => 'Conversa da região',
            default => 'Conversa',
        };
    }

    private function conversationSubtitle(ComunicacaoChatConversa $conversa): string
    {
        return match ($conversa->tipo) {
            ComunicacaoChatConversa::TIPO_PRIVADA => 'Privada',
            ComunicacaoChatConversa::TIPO_DISTRITO => 'Distrito' . ($conversa->distrito ? ': ' . $conversa->distrito->nome : ''),
            ComunicacaoChatConversa::TIPO_REGIAO => 'Região' . ($conversa->regiao ? ': ' . $conversa->regiao->nome : ''),
            default => $conversa->tipo,
        };
    }

    private function ensureParticipant(ComunicacaoChatConversa $conversa, array $context): void
    {
        $isParticipant = ComunicacaoChatParticipante::query()
            ->where('conversa_id', $conversa->id)
            ->where('pessoa_id', $context['pessoa_id'])
            ->exists();

        abort_if(!$isParticipant, 403, 'Voce nao possui acesso a esta conversa.');
    }

    private function markAsRead(ComunicacaoChatConversa $conversa, array $context, array $messageIds): void
    {
        $now = now();

        ComunicacaoChatParticipante::query()
            ->where('conversa_id', $conversa->id)
            ->where('pessoa_id', $context['pessoa_id'])
            ->update(['lido_em' => $now, 'updated_at' => $now]);

        foreach ($messageIds as $messageId) {
            ComunicacaoChatMensagemLeitura::query()->updateOrCreate(
                ['mensagem_id' => $messageId, 'pessoa_id' => $context['pessoa_id']],
                ['user_id' => auth()->id(), 'lido_em' => $now]
            );
        }
    }

    private function resolveInstitutionScope(?InstituicoesInstituicao $instituicao): array
    {
        if (!$instituicao || !(bool) $instituicao->ativo) {
            return [];
        }

        $cacheKey = (int) $instituicao->id;
        if (isset($this->institutionScopeCache[$cacheKey])) {
            return $this->institutionScopeCache[$cacheKey];
        }

        $igreja = null;
        $distrito = null;
        $regiao = null;

        if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
            $regiao = $instituicao;
        } elseif ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::DISTRITO) {
            $distrito = $instituicao;
            $regiao = $this->institutionById((int) ($instituicao->regiao_id ?: $instituicao->instituicao_pai_id));
        } else {
            $igreja = $instituicao;

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::CONGREGACAO) {
                $igreja = $this->institutionById((int) $instituicao->instituicao_pai_id) ?: $instituicao;
            }

            $distrito = $this->institutionById((int) ($igreja->instituicao_pai_id ?? 0));

            if ($distrito && (int) $distrito->tipo_instituicao_id !== InstituicoesTipoInstituicao::DISTRITO) {
                $distrito = null;
            }

            $regiaoId = (int) (($igreja->regiao_id ?? 0) ?: ($distrito->regiao_id ?? 0) ?: ($distrito->instituicao_pai_id ?? 0));
            $regiao = $this->institutionById($regiaoId);
        }

        $scope = [
            'igreja_id' => $igreja ? (int) $igreja->id : 0,
            'igreja_nome' => $igreja ? (string) $igreja->nome : '',
            'distrito_id' => $distrito ? (int) $distrito->id : 0,
            'distrito_nome' => $distrito ? (string) $distrito->nome : '',
            'regiao_id' => $regiao ? (int) $regiao->id : 0,
            'regiao_nome' => $regiao ? (string) $regiao->nome : '',
        ];

        return $this->institutionScopeCache[$cacheKey] = $scope;
    }

    private function sessionInstitution(): ?InstituicoesInstituicao
    {
        $sessionPerfil = session('session_perfil');
        $instituicaoId = (int) (
            data_get($sessionPerfil, 'instituicoes.igrejaLocal.id')
            ?: data_get($sessionPerfil, 'instituicoes.distrito.id')
            ?: data_get($sessionPerfil, 'instituicoes.regiao.id')
            ?: data_get($sessionPerfil, 'instituicao_id', 0)
        );

        if ($instituicaoId <= 0) {
            return null;
        }

        return $this->institutionById($instituicaoId);
    }

    private function institutionById(int $id): ?InstituicoesInstituicao
    {
        if ($id <= 0) {
            return null;
        }

        if (!array_key_exists($id, $this->institutionCache)) {
            $this->institutionCache[$id] = InstituicoesInstituicao::query()
                ->select(['id', 'nome', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id', 'ativo'])
                ->find($id);
        }

        return $this->institutionCache[$id] instanceof InstituicoesInstituicao
            ? $this->institutionCache[$id]
            : null;
    }

    private function normalizeText(string $value): string
    {
        return Str::lower(Str::ascii($value));
    }
}
