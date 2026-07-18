@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Prebendas', 'url' => '/clerigos/perfil/prebendas', 'active' => false],
        ['text' => 'Nova Prebenda', 'url' => '', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')

    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="bg-danger rounded my-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-bell p-4"></i>
                <div class="d-flex flex-column text-start">
                    <p class="fs-1 mb-0" style="color: #fff; font-weight:900 ">{{ __('Prebenda:') }}</p>
                    <p class="fw-bold fs-1" style="color: #fff;">Caso você não receba recursos/prebendas, informe o valor
                        R$0,00 no campo valor de prebenda</p>
                </div>
            </div>
        </div>
        <div class="bg-danger rounded my-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-bell p-4"></i>
                <div class="d-flex flex-column text-start">
                    <p class="fw-bold fs-1 mb-0" style="color: #fff; font-weight:900">{{ __('Prebenda:') }}</p>
                    <p class="fw-bold fs-1" style="color: #fff">O valor atual da prebenda é de R$ <span
                            id="valor_prebenda"></span></p>
                </div>
            </div>
        </div>
        <div class="statbox widget box box-shadow">

            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Nova Prebenda') }}</h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <form class="form-vertical" action="{{ route('clerigos.perfil.prebendas.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-4 col-sm-12 col-md-3">
                            <label class="control-label">{{ __('* Ano') }}</label>
                            <select name="ano" id="ano" class="form-control @error('ano') is-invalid @enderror"
                                value="{{ old('ano') }}">
                                <option value="">{{ __('Seleciona o Ano') }}</option>
                                @foreach ($prebendas as $index =>  $prebenda)
                                    <option value="{{ $prebenda->ano }}"
                                        {{ $index == 0 && !old('ano') ? 'data-recent-year selected' : '' }}
                                        {{ old('ano') == $prebenda->ano ? 'selected' : '' }}>{{ $prebenda->ano }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4 col-12 col-sm-6 col-md-3">
                            <label class="control-label">{{ __('* Valor') }}</label>
                            <input type="text" name="valor" id="valor"
                                class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor') }}">
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p id="valor-maximo-prebenda" style="position: relative; bottom: 15px; font-style: italic"></p>
                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('clerigos.perfil.prebendas.index') }}" class="btn btn-secondary">
                                <x-bx-arrow-back /> {{ __('Voltar') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <x-bx-save /> {{ __('Salvar') }}
                            </button>
                        </div>
                    </div>
                </form>



                <div class="table-responsive mt-4">
                    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Função') }}</th>
                                <th class="text-center">{{ __('Quantidade de Prebendas') }}</th>
                                <th class="text-center">{{ __('valor calculado') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($funcoes as $funcao)
                                <tr data-qtd-prebendas="{{ $funcao->qtd_prebendas }}">
                                    <td>{{ $funcao->funcao }}</td>
                                    <td class="text-center">{{ round($funcao->qtd_prebendas, 1) }}</td>
                                    <td class="text-center valor-calculado"> {{ $funcao->valor_calculado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/numero-por-extenso@latest/dist/numero-por-extenso.min.js"></script>
    <script>
         $('#valor').mask('000.000.000.000,00', {
            reverse: true
        }).on('change', function() {
            if (!$(this).val().startsWith('R$ ')) {
                $(this).val('R$ ' + $(this).val());
            }
        });
        $('#ano').on('change', function() {
            const ano = $(this).val();

            if (ano) {
                pegarValorMaxPrebenda(ano);
                calcularPrebendasPorAno(ano);
            }
        });

        function pegarValorMaxPrebenda(ano) {
            $.ajax({
                url: "{{ route('clerigos.perfil.prebendas.maxPrebenda', ['ano' => '__ano__']) }}".replace(
                    '__ano__', ano),
                type: "GET",
                success: function(response) {
                    console.log(response);
                    if (response.valor) {
                        console.log(response.valor)
                        const valorMaxPrebenda = response.valor;
                        $('#valor').attr('max',
                            valorMaxPrebenda);
                        $('#valor-maximo-prebenda').text('O valor máximo da sua prebenda de ' + ano + ' é de R$ ' +
                            valorMaxPrebenda);
                    }
                },
                error: function() {
                    console.log('Não encontrou')
                }
            });
        }
    </script>

    <script>
        function calcularPrebendasPorAno(ano) {
            $.ajax({
                url: "{{ route('clerigos.prebendas.valor') }}", // Rota para obter o valor
                type: "GET",
                data: {
                    ano
                },
                success: function(response) {
                    if (response.valor) {
                        const valorPrebenda = response.valor; // Obtém o valor da resposta
                        $('#valor_prebenda').text(valorPrebenda); // Atualiza o campo de valor

                        // Atualiza a tabela com o valor calculado para cada função
                        $('tr').each(function() {
                            const qtdPrebendas = $(this).data(
                                'qtd-prebendas'); // Pega a quantidade de prebendas da linha
                            if (qtdPrebendas) {
                                const valorCalculado = valorPrebenda * qtdPrebendas;

                                $(this).find('.valor-calculado').text(valorCalculado.toLocaleString(
                                    'pt-br', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    })); // Atualiza o valor na célula da tabela
                            } else {
                                $(this).find('.valor-calculado').text(' - ');
                            }
                        });
                    }
                },
                error: function() {
                    $('#valor').val('Valor não encontrado');
                }
            });
        }

        // pegar o ano mais recente da option
        const anoMaisRecente = $('#ano option[data-recent-year]').val();
        if(anoMaisRecente) {
            calcularPrebendasPorAno(anoMaisRecente)
            pegarValorMaxPrebenda(anoMaisRecente);
        }
    </script>
@endsection
