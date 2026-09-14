@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicação', 'url' => route('comunicacao.index'), 'active' => false],
    ['text' => 'Chat de Pastores', 'url' => route('comunicacao.chat-pastores.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('extras-css')
<style>
    .chat-shell {
        display: grid;
        gap: 18px;
        grid-template-columns: 330px 1fr;
    }

    .chat-card {
        background: #fff;
        border: 1px solid #e0e6f2;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(31, 42, 68, .06);
    }

    .chat-card-header {
        border-bottom: 1px solid #edf1f7;
        padding: 16px 18px;
    }

    .chat-card-header h5 {
        color: #1f2a44;
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }

    .chat-context {
        color: #7b86a2;
        font-size: 12px;
        margin-top: 6px;
    }

    .chat-tabs {
        border-bottom: 0;
        margin-top: 14px;
    }

    .chat-tabs .nav-link {
        border: 1px solid #e0e6f2;
        border-radius: 8px 8px 0 0;
        color: #7b86a2;
        font-weight: 700;
        margin-right: 6px;
    }

    .chat-tabs .nav-link.active {
        background: #3154d4;
        border-color: #3154d4;
        color: #fff;
    }

    .chat-tab-content {
        flex: 1;
    }

    .conversation-heading {
        border-bottom: 1px solid #edf1f7;
        padding: 16px 18px;
    }

    .conversation-list {
        max-height: 540px;
        overflow-y: auto;
        padding: 10px;
    }

    .conversation-item {
        border: 1px solid transparent;
        border-radius: 8px;
        color: #1f2a44;
        display: block;
        margin-bottom: 8px;
        padding: 12px;
        text-decoration: none;
        transition: all .15s ease;
    }

    .conversation-item:hover,
    .conversation-item.active {
        background: #f3f6ff;
        border-color: #dce5ff;
        color: #1f2a44;
        text-decoration: none;
    }

    .conversation-title {
        font-weight: 700;
        line-height: 1.2;
    }

    .conversation-meta {
        color: #8490aa;
        font-size: 11px;
        margin-top: 5px;
    }

    .unread-dot {
        background: #ff4d4f;
        border-radius: 999px;
        display: inline-block;
        height: 8px;
        margin-right: 6px;
        width: 8px;
    }

    .chat-form-wrap {
        border-bottom: 1px solid #edf1f7;
        padding: 16px 18px;
    }

    .messages-panel {
        display: flex;
        flex-direction: column;
        min-height: 620px;
    }

    .messages-list {
        background: linear-gradient(180deg, #f9fbff 0%, #ffffff 100%);
        flex: 1;
        max-height: 520px;
        overflow-y: auto;
        padding: 18px;
    }

    .message-row {
        display: flex;
        margin-bottom: 12px;
    }

    .message-row.mine {
        justify-content: flex-end;
    }

    .message-bubble {
        background: #ffffff;
        border: 1px solid #e0e6f2;
        border-radius: 12px;
        color: #1f2a44;
        max-width: 72%;
        padding: 10px 12px;
    }

    .message-row.mine .message-bubble {
        background: #3154d4;
        border-color: #3154d4;
        color: #fff;
    }

    .message-author {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .message-time {
        font-size: 10px;
        margin-top: 6px;
        opacity: .75;
    }

    .reply-form {
        border-top: 1px solid #edf1f7;
        padding: 14px 18px;
    }

    .destination-group.d-none {
        display: none;
    }

    @media (max-width: 991px) {
        .chat-shell {
            grid-template-columns: 1fr;
        }

        .message-bubble {
            max-width: 92%;
        }
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Chat de Pastores') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="alert alert-light border mb-4">
                <strong>{{ __('Escopo atual') }}:</strong>
                {{ $context['regiao_nome'] ?: '-' }}
                @if ($context['distrito_nome'])
                    | {{ __('Distrito') }}: {{ $context['distrito_nome'] }}
                @endif
                @if ($context['igreja_nome'])
                    | {{ __('Igreja') }}: {{ $context['igreja_nome'] }}
                @endif
            </div>

            @if (empty($context['pessoa_id']))
                <div class="alert alert-warning mb-4">
                    {{ __('Seu usuário não possui pessoa vinculada. Você pode visualizar o módulo, mas precisa desse vínculo para enviar mensagens no chat pastoral.') }}
                </div>
            @endif

            <div class="chat-shell">
                <div class="chat-card">
                    <div class="chat-card-header">
                        <h5>{{ __('Conversas') }}</h5>
                        <div class="chat-context">{{ __('Histórico visível conforme participantes resolvidos no envio.') }}</div>
                    </div>

                    <div class="chat-form-wrap">
                        <form method="GET" action="{{ route('comunicacao.chat-pastores.index') }}">
                            <div class="form-group mb-2">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('Pesquisar conversa...') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('Pesquisar') }}</button>
                        </form>
                    </div>

                    <div class="conversation-list">
                        @forelse ($conversas as $conversa)
                            <a href="{{ route('comunicacao.chat-pastores.show', $conversa) }}" class="conversation-item {{ $selectedConversa && $selectedConversa->id === $conversa->id ? 'active' : '' }}">
                                <div class="conversation-title">
                                    @if ($conversa->chat_nao_lida)
                                        <span class="unread-dot"></span>
                                    @endif
                                    {{ $conversa->chat_titulo }}
                                </div>
                                <div class="conversation-meta">{{ $conversa->chat_subtitulo }}</div>
                                <div class="conversation-meta">
                                    {{ optional(optional($conversa->ultimaMensagem)->enviado_em)->format('d/m/Y H:i') ?: optional($conversa->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted p-3">{{ __('Nenhuma conversa encontrada.') }}</div>
                        @endforelse
                    </div>

                    <div class="px-3 pb-3">
                        {{ $conversas->links('vendor.pagination.index') }}
                    </div>
                </div>

                @php
                    $chatActiveTab = old('tipo') || !$selectedConversa ? 'novo' : 'responder';
                @endphp
                <div class="chat-card messages-panel">
                    <div class="chat-card-header">
                        <h5>{{ __('Chat de Pastores') }}</h5>
                        <div class="chat-context">{{ __('Use as abas para responder conversas ou iniciar um novo chat.') }}</div>
                        <ul class="nav nav-tabs chat-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $chatActiveTab === 'responder' ? 'active' : '' }} {{ !$selectedConversa ? 'disabled' : '' }}"
                                    @if ($selectedConversa)
                                        data-toggle="tab" href="#chat-tab-responder" role="tab" aria-controls="chat-tab-responder" aria-selected="{{ $chatActiveTab === 'responder' ? 'true' : 'false' }}"
                                    @else
                                        href="#" tabindex="-1" aria-disabled="true"
                                    @endif>
                                    {{ __('Responder') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $chatActiveTab === 'novo' ? 'active' : '' }}"
                                    data-toggle="tab" href="#chat-tab-novo" role="tab" aria-controls="chat-tab-novo" aria-selected="{{ $chatActiveTab === 'novo' ? 'true' : 'false' }}">
                                    {{ __('Novo chat') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content chat-tab-content">
                        <div class="tab-pane fade {{ $chatActiveTab === 'responder' ? 'show active' : '' }}" id="chat-tab-responder" role="tabpanel">
                            <div class="conversation-heading">
                                <h5 class="mb-1">{{ $selectedConversa ? $selectedConversa->chat_titulo : __('Nenhuma conversa selecionada') }}</h5>
                                <div class="chat-context">
                                    {{ $selectedConversa ? $selectedConversa->chat_subtitulo : __('Selecione uma conversa na lista para responder.') }}
                                </div>
                            </div>

                            <div class="messages-list" id="chat-messages-list">
                                @if ($selectedConversa)
                                    @forelse ($mensagens as $mensagem)
                                        @php $mine = (int) $mensagem->remetente_pessoa_id === $context['pessoa_id']; @endphp
                                        <div class="message-row {{ $mine ? 'mine' : '' }}">
                                            <div class="message-bubble">
                                                <div class="message-author">{{ $mine ? __('Você') : optional($mensagem->remetentePessoa)->nome }}</div>
                                                <div>{!! nl2br(e($mensagem->conteudo)) !!}</div>
                                                <div class="message-time">{{ optional($mensagem->enviado_em)->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted p-4">{{ __('Nenhuma mensagem nesta conversa.') }}</div>
                                    @endforelse
                                @else
                                    <div class="text-center text-muted p-4">{{ __('Selecione uma conversa para responder.') }}</div>
                                @endif
                            </div>

                            @if ($selectedConversa && $selectedConversa->status === \App\Models\ComunicacaoChatConversa::STATUS_ATIVA)
                                <form method="POST" action="{{ route('comunicacao.chat-pastores.reply', $selectedConversa) }}" class="reply-form">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <textarea name="conteudo" class="form-control" rows="2" placeholder="{{ __('Digite sua resposta...') }}" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm" {{ empty($context['pessoa_id']) ? 'disabled' : '' }}>{{ __('Responder') }}</button>
                                </form>
                            @endif
                        </div>

                        <div class="tab-pane fade {{ $chatActiveTab === 'novo' ? 'show active' : '' }}" id="chat-tab-novo" role="tabpanel">
                            <div class="chat-form-wrap border-bottom-0">
                                <form method="POST" action="{{ route('comunicacao.chat-pastores.store') }}" id="chat-new-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chat-tipo">{{ __('* Destino') }}</label>
                                                <select name="tipo" id="chat-tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                                                    <option value="PRIVADA" {{ old('tipo') === 'PRIVADA' ? 'selected' : '' }}>{{ __('Pastor específico') }}</option>
                                                    <option value="DISTRITO" {{ old('tipo') === 'DISTRITO' ? 'selected' : '' }}>{{ __('Todos os pastores do distrito') }}</option>
                                                    <option value="REGIAO" {{ old('tipo') === 'REGIAO' ? 'selected' : '' }}>{{ __('Todos os pastores da região') }}</option>
                                                </select>
                                                @error('tipo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-8 destination-group" data-destination="PRIVADA">
                                            <div class="form-group">
                                                <label for="chat-destinatario-search">{{ __('* Pastor') }}</label>
                                                <input type="text" id="chat-destinatario-search" class="form-control mb-2" placeholder="{{ __('Buscar por nome, igreja, distrito, região ou CPF') }}">
                                                <select name="destinatario_pessoa_id" id="chat-destinatario" class="form-control @error('destinatario_pessoa_id') is-invalid @enderror">
                                                    <option value="">{{ __('Selecione') }}</option>
                                                    @foreach ($pastores as $pastor)
                                                        <option value="{{ $pastor['pessoa_id'] }}" data-search="{{ strtolower($pastor['nome'] . ' ' . $pastor['igreja_nome'] . ' ' . $pastor['distrito_nome'] . ' ' . $pastor['regiao_nome'] . ' ' . $pastor['cpf_digits']) }}" {{ (int) old('destinatario_pessoa_id') === $pastor['pessoa_id'] ? 'selected' : '' }}>
                                                            {{ $pastor['nome'] }} - {{ $pastor['igreja_nome'] ?: $pastor['distrito_nome'] ?: $pastor['regiao_nome'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('destinatario_pessoa_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-8 destination-group d-none" data-destination="DISTRITO">
                                            <div class="form-group">
                                                <label for="chat-distrito">{{ __('* Distrito') }}</label>
                                                <select name="distrito_id" id="chat-distrito" class="form-control @error('distrito_id') is-invalid @enderror">
                                                    <option value="">{{ __('Selecione') }}</option>
                                                    @foreach ($distritos as $distrito)
                                                        <option value="{{ $distrito['id'] }}" {{ (int) old('distrito_id') === $distrito['id'] ? 'selected' : '' }}>{{ $distrito['nome'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('distrito_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-8 destination-group d-none" data-destination="REGIAO">
                                            <div class="form-group">
                                                <label>{{ __('Região') }}</label>
                                                <input type="text" class="form-control" value="{{ $context['regiao_nome'] }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="chat-conteudo">{{ __('* Mensagem') }}</label>
                                        <textarea name="conteudo" id="chat-conteudo" class="form-control @error('conteudo') is-invalid @enderror" rows="5" required>{{ old('conteudo') }}</textarea>
                                        @error('conteudo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary" {{ empty($context['pessoa_id']) ? 'disabled' : '' }}>{{ __('Enviar mensagem') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipo = document.getElementById('chat-tipo');
        const destinationGroups = document.querySelectorAll('.destination-group');
        const search = document.getElementById('chat-destinatario-search');
        const destinatario = document.getElementById('chat-destinatario');
        const messages = document.getElementById('chat-messages-list');

        function syncDestination() {
            const current = tipo.value;
            destinationGroups.forEach(function (group) {
                group.classList.toggle('d-none', group.dataset.destination !== current);
            });
        }

        function normalize(value) {
            return (value || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        function filterPastores() {
            const term = normalize(search.value);
            Array.prototype.forEach.call(destinatario.options, function (option, index) {
                if (index === 0) {
                    return;
                }

                option.hidden = term !== '' && normalize(option.dataset.search || option.textContent).indexOf(term) === -1;
            });
        }

        if (tipo) {
            tipo.addEventListener('change', syncDestination);
            syncDestination();
        }

        if (search && destinatario) {
            search.addEventListener('input', filterPastores);
        }

        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    });
</script>
@endsection
