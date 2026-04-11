@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Notificações de Transferências', 'url' => '', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <h1 class="col-md-12">Notificações de Transferências</h1>

    @if ($baseParams->notificacoesTransferencia && $baseParams->notificacoesTransferencia->count())
        @foreach ($baseParams->notificacoesTransferencia as $notificacao)
            @if (!$notificacao->dt_aceite && !$notificacao->dt_rejeicao)
                <a class="media server-log"
                    href="{{ route('membro.receber_membro_externo', ['notificacao' => $notificacao->id]) }}">
                    <div class="col-md-12" style="margin: 16px;">
                        <div id='left-rollbacks' class='dragula'>
                            <div class="card post text-post" style="">
                                <div class="card-body">
                                    <div class="media user-meta d-sm-flex d-block text-sm-left text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="32"
                                            fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                                            <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1" />
                                            <path
                                                d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z" />
                                        </svg>
                                        <div class="media-body">
                                            <h5 class="">Nome: {{ $notificacao->membro->nome }}</h5>
                                            <p class="meta-time">
                                                Data de registro da transferência: 
                                                {{ $notificacao->dt_abertura 
                                                    ? \Carbon\Carbon::parse($notificacao->dt_abertura)->format('d/m/Y') 
                                                    : 'Sem informações' 
                                                }}
                                            </p>
                                            <p class="meta-time">
                                                Origem:
                                                {{ $notificacao->regiaoOrigem->nome ?? 'Sem região' }} 
                                                - {{ $notificacao->distritoOrigem->nome ?? 'Sem distrito' }} 
                                                - {{ $notificacao->igrejaOrigem->nome ?? 'Sem igreja' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-content  text-sm-left text-center" style="padding: 16px;">
                                    <h6 class="badge badge-success col-md-12" style="height: 64; font-size: 1.2rem;">Aceitar ou Rejeitar Transferência</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    @endif
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="plugins/drag-and-drop/dragula/dragula.min.js"></script>
    <script src="plugins/drag-and-drop/dragula/custom-dragula.js"></script>
@endsection
