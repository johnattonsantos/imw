@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
        ['text' => 'Diário', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
        .cursor-pointer{
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>GCEU Membros da igreja: <u id="instituicao">{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>
                        
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="widget-content-area row">
                        <div class="mb-3 col-lg-5 col-md-6 col-sm-12">
                            <label class="control-label">Membros:</label>
                            <select id="membro_id" name="membro_id" class="form-control @error('membro_id') is-invalid @enderror" required>
                                <option value="" {{ request()->input('membro_id') == '' ? 'selected' : '' }}>Selecione</option>
                                @foreach($membros as $membro)
                                    <option value="{{ $membro->id }}" {{ request()->input('membro_id') == $membro->id ? 'selected' : '' }}>{{ $membro->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-3 col-md-6 col-sm-12" style="margin-top: 30px;">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="Buscar GCEU" class="btn btn-primary btn">
                                <x-bx-plus /> Adicionar Membro ao GCEU
                            </button>
                        </div>
                    </div>
                    
                    <div class="row" style="display: flex;flex-direction: row;align-items: baseline;align-content: space-around;flex-wrap: wrap;justify-content: center;">
                    @foreach ($totalFuncao as $total)
                        @if($total->total > 1)
                            <div class="card bg-warning col-lg-3" style="float: left; margin: 3px;">
                                <div class="card-body">
                                    
                                    <p>
                                        <b>ATENÇÃO:</b>
                                        </p>
                                    A função <b><u>{{$total->funcao}}</u></b> aparece em mais de um GCEU.
                                </div>
                            </div>
                        @endif
                    @endforeach
                    </div>
                    
                </form>
                @if(request()->input('membro_id'))
                 <form method="POST" action="{{ route('gceu.membro.update', ['id' => request()->input('membro_id')]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="widget-content-area">
                        <blockquote class="blockquote">
                            <h4>Adicione o GCEU e a Função para o membro escolhido </h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover mb-4">
                                    <thead>
                                        <tr>
                                            <th>GCEU</th>
                                            <th>FUNÇÃO</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="gceuIndex-tbody">
                                        @forelse ($gceuMembros as $gceuMembro)
                                        <tr>
                                            <td>
                                                <select class="form-control gceu" name="gceu[]">
                                                    <option value="">Selecione</option>
                                                    @foreach ($gceus as $gceu)
                                                    <option value="{{ $gceu->id }}" {{ $gceuMembro->gceu_cadastro_id == $gceu->id ? 'selected' : '' }}>
                                                        {{ $gceu->nome }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control gceu-funcao" name="gceu-funcao[]">
                                                    <option value="">Selecione</option>
                                                    @foreach ($gceuFuncoes as $funcao)
                                                        @if($funcao->id != 6)
                                                            <option value="{{ $funcao->id }}" {{ $gceuMembro->gceu_funcao_id == $funcao->id ? 'selected' : '' }}>
                                                                {{ $funcao->funcao }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="width: 200px;">
                                                <div class="centralizado">
                                                    <!-- Botão Adicionar -->
                                                    <button type="button" title="Adicionar Linha" class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-gceuIndex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                                        </svg>
                                                    </button>
                                                    <!-- Botão Apagar -->
                                                    <button type="button" title="Apagar Linha" class="btn btn-sm btn-danger btn-rounded apagar-linha-gceuIndex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td>
                                                <select class="form-control gceu" name="gceu[]">
                                                    <option value="">Selecione</option>
                                                    @foreach ($gceus as $gceu)
                                                    <option value="{{ $gceu->id }}">{{ $gceu->nome }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control gceu-funcao" name="gceu-funcao[]">
                                                    <option value="">Selecione</option>
                                                    @foreach ($gceuFuncoes as $funcao)
                                                    <option value="{{ $funcao->id }}">{{ $funcao->funcao }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="width: 200px;">
                                                <div class="centralizado">
                                                    <!-- Botão Adicionar -->
                                                    <button type="button" title="Adicionar Linha" class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-gceuIndex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                                        </svg>
                                                    </button>
                                                    <!-- Botão Apagar -->
                                                    <button type="button" title="Apagar Linha" class="btn btn-sm btn-danger btn-rounded apagar-linha-gceuIndex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </blockquote>
                    </div>
                    <div class="form-group mt-4" style="margin-top: -25px !important;">
                        <button type="submit" title="Salvar" class="btn btn-primary btn-lg ml-4">Atualizar</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
<script src="{{ asset('membros/js/editar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/pt-BR.js"></script>
<script>
    $.fn.select2.defaults.set("language", "pt-BR");
    $('#membro_id').select2({
        width: '100%',
        placeholder: 'Selecione',
        allowClear: true
    });

    $('form').on('submit', function (event) {
        if (!validateFormacaoEclesiastica() || !validateMinisterialDates()) {
            event.preventDefault();
            toastr.warning('Por favor, corrija os erros de data antes de enviar.');
        }
    });
    $(document).on('click', '.cgeu-presenca-falta', function(){
        let valor = $(this).val();
        let gceu_id = $(this).data('gceuid');
        let membro_id = $(this).data('membroid');
        let dt_gceu = $('#dt-gceu').val();
        let nome = $(this).data('nome');
        if(!dt_gceu){
            toastr.warning('Escolha uma data para registrar a  presença/ausência.');
            return false
        }
        $.ajax({
            url: "/gceu/diario-presenca-falta/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            dataType: "json",
            data: {
                valor, gceu_id, membro_id, dt_gceu, nome
            },            
            success: function (response) {
                if(valor == 1){
                    var msg = `Presença confirmada com sucesso para ${nome}`
                }else{
                    var msg = `Falta confirmada com sucesso para ${nome}`
                }
                toastr.success(msg);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function (response) {
                if(valor == 1){
                    var msg = `Presença confirmada com sucesso para ${nome}`
                }else{
                    var msg = `Falta confirmada com sucesso para ${nome}`
                }
                toastr.success(msg);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
        });
    })

</script>

@endsection
