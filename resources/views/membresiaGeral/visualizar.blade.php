<style>
    .blockui-growl-message {
        display: none;
        text-align: left;
        padding: 15px;
        background-color: #455a64;
        color: #fff;
        border-radius: 3px;
    }

    .blockui-animation-container {
        display: none;
    }

    .multiMessageBlockUi {
        display: none;
        background-color: #455a64;
        color: #fff;
        border-radius: 3px;
        padding: 15px 15px 10px 15px;
    }

    .multiMessageBlockUi i {
        display: block
    }
</style>

<div class="modal-header">
    <h5 class="modal-title">{{ $membro->nome }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-3">
            <!-- conteudo -->
            <ul class="nav flex-column nav-pills mb-sm-0 mb-3 mx-auto" id="borderTop" role="tablist">
                <li class="nav-item my-1">
                    <a class="nav-link active" id="border-top-dados-pessoais"
                        data-toggle="tab"href="#border-top-dados-pessoal" role="tab"
                        aria-controls="border-top-dados-pessoais" aria-selected="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ __('Dados Pessoais') }}
                    </a>
                </li>

                @if ($membro->contato)
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-contatos" data-toggle="tab" href="#border-top-contato"
                            role="tab" aria-controls="border-top-contato" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-phone">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                            {{ __('Contatos') }}
                        </a>
                    </li>
                @endif

                @if ($membro->familiar)
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-familiar" data-toggle="tab" href="#border-top-familia"
                            role="tab" aria-controls="border-top-familia" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ __('Familiar') }}
                        </a>
                    </li>
                @endif

                @if ($membro->funcoesMinisteriais->count())
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-ministerial" data-toggle="tab" href="#border-top-ministerio"
                            role="tab" aria-controls="border-top-ministerio" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-bookmark">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('Ministerial') }}
                        </a>
                    </li>
                @endif

                @if ($membro->formacoesEclesiasticas->count())
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-formacaoEclesiatica" data-toggle="tab"
                            href="#border-top-formacao" role="tab" aria-controls="border-top-formacao"
                            aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-bookmark">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('Formação Eclesiática') }}
                        </a>
                    </li>
                @endif

                @if ($membro->rolPermanente->count())
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-historicoeclesiastico" data-toggle="tab"
                            href="#border-top-historico" role="tab" aria-controls="border-top-historico"
                            aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('Histórico Eclesiástico') }}
                        </a>
                    </li>
                @endif

                @if ($membro->disciplinas->count())
                    <li class="nav-item my-1">
                        <a class="nav-link" id="border-top-historicoeclesiastico" data-toggle="tab"
                            href="#border-top-disciplina" role="tab" aria-controls="border-top-disciplina"
                            aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('Histórico Disciplina') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="col-9">
            <div class="tab-content">
                @include('membresiaGeral.tab-dados-pessoais', ['membro' => $membro])


                @if ($membro->contato)
                    @include('membresiaGeral.tab-contato', ['membro' => $membro])
                @endif

                @if ($membro->familiar)
                    @include('membresiaGeral.tab-familia', ['membro' => $membro])
                @endif

                @if ($membro->funcoesMinisteriais->count())
                    @include('membresiaGeral.tab-ministerio', ['membro' => $membro])
                @endif

                @if ($membro->formacoesEclesiasticas->count())
                    @include('membresiaGeral.tab-formacao', ['membro' => $membro])
                @endif

                @if ($membro->rolPermanente->count())
                    @include('membresiaGeral.tab-historico')
                @endif

                @if ($membro->disciplinas->count())
                    @include('membresiaGeral.tab-disciplina')
                @endif
            </div>
        </div>
    </div>
</div>
