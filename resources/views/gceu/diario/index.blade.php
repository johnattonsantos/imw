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
                        <h4>{{ __('Diário dos GCEUs da Igreja:') }} <u id="instituicao">{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="row">
                        <div class="mb-3 col-lg-4 col-md-6 col-sm-12" id="filtros_data">
                            <label class="control-label">{{ __('*Data:') }}</label>
                            <input type="date" class="form-control @error('dt-gceu') is-invalid @enderror" id="dt-gceu" name="dt_gceu" value="{{ request()->input('dt_gceu') }}" required placeholder="{{ __('ex: 31/12/2000') }}">
                        </div>
                        <div class="mb-3 col-lg-5 col-md-6 col-sm-12">
                            <label class="control-label">{{ __('GCEU:') }}</label>
                            <select id="gceu_id" name="gceu_id" class="form-control @error('gceu_id') is-invalid @enderror" required>
                                <option value="" {{ request()->input('gceu_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                                @foreach($Gceus as $gceu)
                                    <option value="{{ $gceu->id }}" {{ request()->input('gceu_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-2 col-md-6 col-sm-12" style="margin-top: 30px;">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar GCEU') }}" class="btn btn-primary btn">
                                <x-bx-plus /> {{ __('Registrar presença') }}
                            </button>
                        </div>
                    </div>
                </form>
                @if(request()->input('gceu_id'))
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('Diário para registro de presença/ausência do GCEU:') }} </h4>
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">&nbsp;</div>
                            <div class="row">
                                @foreach($membros as $membro)
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-body">

                                                <p>
                                                    {{ $membro->nome }}
                                                    @if($membro->presenca === 0)
                                                        <i class="fas fa-times-circle" style="float: right; color: red;"></i>
                                                    @elseif($membro->presenca === 1)
                                                        <i class="fas fa-check-circle"  style="float: right; color: green;"></i>
                                                    @endif
                                                </p>
                                                {{ __('Presença:') }}
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input cursor-pointer cgeu-presenca-falta" onclick="presencaDiario()" type="radio" data-nome="{{ $membro->nome }}" data-membroid="{{ $membro->membro_id }}" data-gceuid="{{ $membro->gceu_id }}" data-valor="1" name="{{ $membro->membro_id }}" id="{{ $membro->membro_id }}" {{ $membro->presenca === 1 ? 'checked' : '' }} value="1">
                                                    <label class="form-check-label cursor-pointer" for="{{ $membro->membro_id }}">{{ __('Sim') }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                    <input class="form-check-input cursor-pointer cgeu-presenca-falta" onclick="presencaDiario()" type="radio" data-nome="{{ $membro->nome }}" data-membroid="{{ $membro->membro_id }}" data-gceuid="{{ $membro->gceu_id }}"  data-valor="0" name="{{ $membro->membro_id }}" id="{{ $membro->membro_id }}{{ $membro->membro_id }}" {{ $membro->presenca === 0 ? 'checked' : '' }} value="0">
                                                    <label class="form-check-label cursor-pointer" for="{{ $membro->membro_id }}{{ $membro->membro_id }}">{{ __('Não') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@section('extras-scripts')
<script>
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
