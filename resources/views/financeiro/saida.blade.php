@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Movimento de Caixa', 'url' => '/financeiro/movimento-caixa', 'active' => false],
        ['text' => 'Saída', 'url' => '#', 'active' => true],
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
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Novo Registro de Saída</h4>
                    </div>
                </div>
            </div>

            <form action="{{ route('financeiro.saida.store') }}" method="POST" class="widget-content widget-content-area"
                enctype="multipart/form-data" id="saidaForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="caixa_id">* Caixa</label>
                        <select class="form-control @error('caixa_id') is-invalid @enderror" id="caixa_id" name="caixa_id"
                            required>
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
                        <select class="form-control @error('plano_conta_id') is-invalid @enderror" id="plano_conta_id"
                            name="plano_conta_id" required>
                            <option value="" hidden disabled>Selecione</option>
                            @foreach ($planoContas as $pc)
                                <option
                                    {{ !$pc->selecionavel ? 'disabled' : '' }}
                                    value="{{ $pc->id }}"
                                    data-numeracao="{{ $pc->numeracao }}"
                                    {{ old('plano_conta_id') == $pc->id ? 'selected' : '' }}>
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
                        <input type="text" class="form-control @error('valor') is-invalid @enderror" id="valor"
                            name="valor" required autofocus>
                        @error('valor')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="data_movimento">* Data do Movimento</label>
                        <input type="date" class="form-control @error('data_movimento') is-invalid @enderror"
                            id="data_movimento" name="data_movimento" value="{{ old('data_movimento', date('Y-m-d')) }}"
                            required>
                        @error('data_movimento')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_pagante_favorecido_id">* Tipo de Beneficiário</label>
                        <select class="form-control @error('tipo_pagante_favorecido_id') is-invalid @enderror"
                            id="tipo_pagante_favorecido_id" name="tipo_pagante_favorecido_id" required>
                            <option value="" hidden>Selecione</option>
                            @foreach ($tiposPagantesFavorecidos as $tipoPaganteFavorecido)
                                @if ($tipoPaganteFavorecido->id == 2 || $tipoPaganteFavorecido->id == 3 || $tipoPaganteFavorecido->id == 99)
                                    <option value="{{ $tipoPaganteFavorecido->id }}">
                                        {{ $tipoPaganteFavorecido->nome }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('tipo_pagante_favorecido_id')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4 d-none" id="show_pagante_favorecido">
                    <div class="col-6">
                        <label for="pagante_favorecido">Beneficiário</label>
                        <input type="text" class="form-control @error('pagante_favorecido') is-invalid @enderror"
                            id="pagante_favorecido" name="pagante_favorecido" value="{{ old('pagante_favorecido') }}">

                        @error('pagante_favorecido')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                            value="{{ old('descricao') }}" rows="3"></textarea>
                        @error('descricao')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <hr>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4><b>Anexos</b></h4>
                        <p>Utilize este espaço para anexar boletos, comprovantes de pagamento, contratos ou qualquer
                            documento que julgue necessário armazenar no sistema.</p>
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="anexo1">Anexo 1</label>
                        <input type="file" class="mb-3 form-control-file @error('anexo1') is-invalid @enderror"
                            id="anexo1" name="anexo1">
                        <label for="descricao_anexo1">Descrição do Anexo</label>
                        <textarea class="form-control @error('descricao_anexo1') is-invalid @enderror" id="descricao_anexo1"
                            name="descricao_anexo1" rows="1"></textarea>
                        @error('anexo1')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                        @error('descricao_anexo1')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="anexo2">Anexo 2</label>
                        <input type="file" class="mb-3 form-control-file @error('anexo2') is-invalid @enderror"
                            id="anexo2" name="anexo2">
                        <label for="descricao_anexo2">Descrição do Anexo</label>
                        <textarea class="form-control @error('descricao_anexo2') is-invalid @enderror" id="descricao_anexo2"
                            name="descricao_anexo2" rows="1"></textarea>
                        @error('anexo2')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                        @error('descricao_anexo2')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="anexo3">Anexo 3</label>
                        <input type="file" class="mb-3 form-control-file @error('anexo3') is-invalid @enderror"
                            id="anexo3" name="anexo3">
                        <label for="descricao_anexo3">Descrição do Anexo 3</label>
                        <textarea class="form-control @error('descricao_anexo3') is-invalid @enderror" id="descricao_anexo3"
                            name="descricao_anexo3" rows="1"></textarea>
                        @error('anexo3')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                        @error('descricao_anexo3')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4 justify-content-center">
                    <button type="submit" title="Salvar nova movimentação de entrada" class="btn btn-success btn-lg ml-4"
                        id="submitButton">
                        <x-bx-save /> Salvar
                    </button>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
@section('extras-scripts')
    <script>
        const TIPO_BENEFICIARIO_CLERIGO = '3';
        const PLANOS_FORCA_CLERIGO = ['2.18.23', '2.18.11', '1.01.07', '1.07.01'];

        function isPlanoContaComForcaClerigo() {
            const numeracaoPlanoConta = String($('#plano_conta_id option:selected').data('numeracao') || '').trim();
            return PLANOS_FORCA_CLERIGO.includes(numeracaoPlanoConta);
        }

        function aplicarRegraTipoBeneficiarioPorPlanoConta() {
            const $tipoBeneficiario = $('#tipo_pagante_favorecido_id');
            const forcarClerigo = isPlanoContaComForcaClerigo();
            const possuiOpcaoClerigo = $tipoBeneficiario.find(`option[value="${TIPO_BENEFICIARIO_CLERIGO}"]`).length > 0;

            if (forcarClerigo && possuiOpcaoClerigo) {
                $tipoBeneficiario.val(TIPO_BENEFICIARIO_CLERIGO).trigger('change');
                $tipoBeneficiario.find('option').each(function() {
                    const value = String($(this).val() || '');
                    if (value !== '' && value !== TIPO_BENEFICIARIO_CLERIGO) {
                        $(this).prop('disabled', true);
                    }
                });
                return;
            }

            $tipoBeneficiario.find('option').prop('disabled', false);
            $tipoBeneficiario.find('option[value=""]').prop('disabled', true);
        }

        $(document).ready(function() {
            // limpar o campo Pagante
            $('#pagante_favorecido').val('').trigger('change');
            aplicarRegraTipoBeneficiarioPorPlanoConta();
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
            aplicarRegraTipoBeneficiarioPorPlanoConta();
        });

        
        // evento de exibição do descritivo do pagante/favorecido
        $('#tipo_pagante_favorecido_id').change(function() {
            var tipoPaganteFavorecido = this.value;

            if (tipoPaganteFavorecido == 1) {
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
                }).prop('required', true); // Adicionar required

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

        // Desabilitar o botão de submit ao salvar
        $('#saidaForm').submit(function() {
            $('#submitButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Aguarde...');
        });
    </script>
@endsection
