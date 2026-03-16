@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Movimento de Caixa', 'url' => '/financeiro/movimento-caixa', 'active' => false],
        ['text' => 'Entrada', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/pt-BR.js"></script>
    <style>
        .select2-selection__rendered {
            line-height: 50px !important;
            padding-left: 25px !important;
            font-family: 'Nunito', sans-serif;
        }

        .select2-container .select2-selection--single {
            height: 50px !important;
            font-weight: 600;
            font-family: 'Nunito', sans-serif;
        }

        .select2-selection__arrow {
            height: 50px !important;
            font-family: 'Nunito', sans-serif;
        }
        /* #ui-datepicker-div{
            position: absolute;
            top: 51% !important;
            left: 886.1px;
            z-index: 1;
            display: block;
        } */
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Novo Registro de Entrada</h4>
                    </div>
                </div>
            </div>

            <form id="entradaForm" action="{{ route('financeiro.entrada.store') }}" method="POST" class="widget-content widget-content-area">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="caixa_id">* Caixa</label>
                        <select class="form-control @error('caixa_id') is-invalid @enderror" id="caixa_id" name="caixa_id" required>
                            <option value="" hidden disabled>Selecione</option>
                            @foreach ($caixas as $caixa)
                                @if ($caixa->id == old('caixa_id'))
                                    <option value="{{ $caixa->id }}" selected>{{ $caixa->descricao }}</option>
                                @else
                                    <option value="{{ $caixa->id }}">{{ $caixa->descricao }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('caixa_id')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="plano_conta_id">* Plano de contas</label>
                        <select class="form-control @error('plano_conta_id') is-invalid @enderror" id="plano_conta_id" name="plano_conta_id" required>
                            <option value="" hidden disabled>Selecione</option>
                            @foreach ($planoContas as $pc)
                                <option
                                    {{ !$pc->selecionavel ? 'disabled' : '' }}
                                    value="{{ $pc->id }}"
                                    data-numeracao="{{ $pc->numeracao }}"
                                    {{ old('plano_conta_id') == $pc->id ? 'selected' : '' }}
                                >
                                    {{ $pc->numeracao }} - {{ $pc->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('plano_conta_id')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="valor">* Valor</label>
                        <input type="text" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" required autofocus>
                        @error('valor')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="data_movimento">* Data do Movimento</label>
                        <input type="date" class="form-control @error('data_movimento') is-invalid @enderror" id="data_movimento" name="data_movimento" value="{{ old('data_movimento', date('Y-m-d')) }}" required>
                        @error('data_movimento')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_pagante_favorecido_id">* Tipo de Pagante</label>
                        <select class="form-control @error('tipo_pagante_favorecido_id') is-invalid @enderror" id="tipo_pagante_favorecido_id" name="tipo_pagante_favorecido_id" required>
                            <option value="" selected disabled hidden>Selecione</option>
                            @if($tipoInstituicao->tipoInstituicao->sigla == 'I')
                                @foreach ($tiposPagantesFavorecidos as $tipoPaganteFavorecido)
                                    @if ($tipoPaganteFavorecido->id != 2)
                                        <option value="{{ $tipoPaganteFavorecido->id }}">
                                            {{ $tipoPaganteFavorecido->nome }}
                                        </option>
                                    @endif
                                @endforeach
                            @else 
                                @foreach ($tiposPagantesFavorecidos as $tipoPaganteFavorecido)
                                        @if (($tipoPaganteFavorecido->id != 1) && ($tipoPaganteFavorecido->id != 2) && ($tipoPaganteFavorecido->id != 3))
                                            <option value="{{ $tipoPaganteFavorecido->id }}">
                                                {{ $tipoPaganteFavorecido->nome }}
                                            </option>
                                        @endif
                                @endforeach
                            @endif
                        </select>
                        @error('tipo_pagante_favorecido_id')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4 d-none" id="show_pagante_favorecido">
                    <div class="col-6">
                        <label for="pagante_favorecido">Pagante</label>
                        <input type="text" class="form-control @error('pagante_favorecido') is-invalid @enderror" id="pagante_favorecido" name="pagante_favorecido" value="{{ old('pagante_favorecido') }}">
                        @error('pagante_favorecido')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-4 ano_mes">
                        <label for="ano_mes">Mês/Ano</label>
                        <div class="input-group">
                            <select class="form-control " id="ano" name="ano" required="">
                                @php
                                    $mesAtual = date('m');
                                    $anoAtual = date('Y');
                                    $anos = range($anoAtual - 1, $anoAtual);
                                @endphp
                                @foreach($anos as $ano)
                                    <option value="{{ $ano }}" {{ $anoAtual == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <select class="form-control " id="mes" name="mes" required="">
                                @foreach($meses as $mes)
                                    <option value="{{ $mes->id }}" {{ $mesAtual == zeroEsqueda($mes->id) ? 'selected' : '' }}>{{ $mes->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" value="{{ old('descricao') }}" rows="3"></textarea>
                        @error('descricao')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4 justify-content-center">
                    <button type="submit" title="Salvar nova movimentação de entrada" class="btn btn-success btn-lg ml-4" id="submitButton">
                        <x-bx-save /> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script>
        const TIPO_PAGANTE_CLERIGO = '3';
        const PLANOS_FORCA_CLERIGO = ['1.01.07', '1.07.01'];

        function isPlanoContaComForcaClerigo() {
            const numeracaoPlanoConta = String($('#plano_conta_id option:selected').data('numeracao') || '').trim();
            return PLANOS_FORCA_CLERIGO.includes(numeracaoPlanoConta);
        }

        function aplicarRegraTipoPagantePorPlanoConta() {
            const $tipoPagante = $('#tipo_pagante_favorecido_id');
            const forcarClerigo = isPlanoContaComForcaClerigo();
            const possuiOpcaoClerigo = $tipoPagante.find(`option[value="${TIPO_PAGANTE_CLERIGO}"]`).length > 0;

            if (forcarClerigo && possuiOpcaoClerigo) {
                $tipoPagante.val(TIPO_PAGANTE_CLERIGO).trigger('change');
                $tipoPagante.find('option').each(function() {
                    const value = String($(this).val() || '');
                    if (value !== '' && value !== TIPO_PAGANTE_CLERIGO) {
                        $(this).prop('disabled', true);
                    }
                });
                return;
            }

            $tipoPagante.find('option').prop('disabled', false);
            $tipoPagante.find('option[value=""]').prop('disabled', true);
        }

        $(document).ready(function() {
            // limpar o campo Pagante
            $('#pagante_favorecido').val('').trigger('change');
            aplicarRegraTipoPagantePorPlanoConta();
            let planoContaId = $('#plano_conta_id').val();
            if(planoContaId == 4 || planoContaId == 5 || planoContaId == 110186){
                $('.ano_mes').show();
                $('#mes').prop('disabled', false);
                $('#ano').prop('disabled', false);
            }else{
                $('.ano_mes').hide();
                $('#mes').prop('disabled', true);
                $('#ano').prop('disabled', true);
            }
        });

        // máscara de valor
        $('#valor').mask('0.000.000.000,00', {
            reverse: true
        });

        // definir o idioma padrão do Select2 para português
        $.fn.select2.defaults.set("language", "pt-BR");
        $('#caixa_id').select2({
           placeholder: 'Selecione',
            allowClear: true
        }); 

    
        $('#plano_conta_id').select2({
           placeholder: 'Selecione',
            allowClear: true
        }); 

        $('#plano_conta_id').change(function() {
            aplicarRegraTipoPagantePorPlanoConta();
            let planoContaId = $('#plano_conta_id').val();
            if(planoContaId == 4 || planoContaId == 5 || planoContaId == 110186){
                $('.ano_mes').show();
                    $('#mes').prop('disabled', false);
                    $('#ano').prop('disabled', false);
            }else{
                $('.ano_mes').hide();
                    $('#mes').prop('disabled', true);
                    $('#ano').prop('disabled', true);
            }
        });

        // evento de exibição do descritivo do pagante/favorecido
        $('#tipo_pagante_favorecido_id').change(function() {
            var tipoPaganteFavorecido = this.value;
            $('.ano_mes').hide();
            if (tipoPaganteFavorecido == 1) {
                let planoContaId = $('#plano_conta_id').val();
                if(planoContaId == 4 || planoContaId == 5 || planoContaId == 110186){
                    $('.ano_mes').show();
                    $('#mes').prop('disabled', false);
                    $('#ano').prop('disabled', false);
                }else{
                    $('.ano_mes').hide();
                    $('#mes').prop('disabled', true);
                    $('#ano').prop('disabled', true);
                }
                // Exibir o campo para membros
                $('#show_pagante_favorecido').removeClass('d-none');

                if ($('#pagante_favorecido').data('select2')) {
                    $('#pagante_favorecido').select2('destroy').empty();
                }

                // Criar o select2 para membros
                $('#pagante_favorecido').select2({
                    data: {!! json_encode($membros) !!}.map(function(membro) {
                        return {
                            id: membro.id,
                            text: membro.nome
                        };
                    }),
                    placeholder: 'Selecione'
                }).on('select2:select', function(e) {
                    var data = e.params.data;
                    $('#pagante_favorecido').val(data.id); // atribuir o valor selecionado ao input
                }).prop('required', false); // Adicionar required - removi .prop('required', true);

            } else if (tipoPaganteFavorecido == 2) {
                // Exibir o campo para fornecedores
                $('#show_pagante_favorecido').removeClass('d-none');

                if ($('#pagante_favorecido').data('select2')) {
                    $('#pagante_favorecido').select2('destroy').empty();
                }

                // Criar o select2 para fornecedores
                $('#pagante_favorecido').select2({
                    data: {!! json_encode($fornecedores) !!}.map(function(fornecedor) {
                        return {
                            id: fornecedor.id,
                            text: fornecedor.nome
                        };
                    }),
                    placeholder: 'Selecione'
                }).on('select2:select', function(e) {
                    var data = e.params.data;
                    $('#pagante_favorecido').val(data.id); // atribuir o valor selecionado ao input
                }).prop('required', true); // Adicionar required

            } else if (tipoPaganteFavorecido == 3) {
                // Exibir o campo para clérigos
                $('#show_pagante_favorecido').removeClass('d-none');

                if ($('#pagante_favorecido').data('select2')) {
                    $('#pagante_favorecido').select2('destroy').empty();
                }

                // Criar o select2 para clérigos
                $('#pagante_favorecido').select2({
                    data: {!! json_encode($clerigos) !!}.map(function(clerigo) {
                        return {
                            id: clerigo.id,
                            text: clerigo.nome
                        };
                    }),
                    placeholder: 'Selecione'
                }).on('select2:select', function(e) {
                    var data = e.params.data;
                    $('#pagante_favorecido').val(data.id); // atribuir o valor selecionado ao input
                }).prop('required', true); // Adicionar required

            } else {
                $('#show_pagante_favorecido').removeClass('d-none');
                $('#pagante_favorecido').select2('destroy').empty();

                // Mostrar o campo input de texto normal
                $('#pagante_favorecido').replaceWith(
                    '<input type="text" class="form-control @error('pagante_favorecido') is-invalid @enderror" id="pagante_favorecido" name="pagante_favorecido" value="{{ old('pagante_favorecido') }}">'
                );

                // Remover required
                $('#pagante_favorecido').prop('required', false);
            }
        });

        // desabilitar botão submit ao enviar o formulário
        $('#entradaForm').submit(function() {
            var submitButton = $('#submitButton');
            submitButton.prop('disabled', true);
            var originalText = submitButton.html();
            submitButton.html('<i class="fa fa-spinner fa-spin"></i> Aguarde...');
         
        });
    </script>
@endsection
