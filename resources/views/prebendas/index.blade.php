@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Prebendas', 'url' => '/prebendas/prebendas', 'active' => true],
    ]"></x-breadcrumb>
@endsection
@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }

        .modal-xl {
            max-width: 90% !important;
            /* Define que o modal ocupe 90% da largura da página */
        }
    </style>
@endsection

@include('extras.alerts')

@section('content')
    <div class="container-fluid">

    </div>
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Filtros para pesquisa') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <!-- Conteúdo -->
                <div class="card mb-3">
                    <div class="bg-holder d-none d-lg-block bg-card gb-title">
                    </div>
                    <!--/.bg-holder-->
                    <div class="card-body">
                        <form method="POST" action="{{ route('clerigos.prebendas.updatePrebenda') }}">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-3">

                                    <a href="{{ route('clerigos.prebendas.createPrebenda') }}"
                                        title="{{ __('Inserir um novo registro') }}" class="btn btn-primary right btn-rounded"> <svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-plus-square">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                            </rect>
                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg> {{ __('Nova') }} </a>
                                </div>
                                <div class="col-2">
                                    <select name="ano" id="ano" class="form-control form-control-sm">
                                        <option value="" disabled selected>{{ __('Selecione um ano') }}</option>
                                        @foreach ($prebendas as $index => $prebenda)
                                            <option value="{{ $prebenda->ano }}"
                                                {{ $index == 0 && !old('ano') ? 'data-recent-year selected' : '' }}
                                                {{ old('ano') == $prebenda->ano ? 'selected' : '' }}>
                                                {{ $prebenda->ano }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('ano'))
                                        <small class="text-danger">{{ $errors->first('ano') }}</small>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <input type="text" name="valor" id="valor"
                                        class="form-control form-control-sm {{ $errors->has('valor') ? 'is-invalid' : '' }}"
                                        placeholder="{{ __('Valor...') }}" value="{{ old('valor') }}">
                                    @if ($errors->has('valor'))
                                        <small class="text-danger">{{ $errors->first('valor') }}</small>
                                    @endif
                                </div>
                                <div class="col-auto" style="margin-left: -19px;">
                                    <button type="submit" class="btn btn-primary btn-rounded">{{ __('Alterar valor') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Listagem de Registros') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                                {{-- <a href="{{ route('clerigos.prebendas.create', ['id' => auth()->user()->id]) }}"
                                    title="{{ __('Inserir um novo registro') }}" class="btn btn-primary right btn-rounded"> <svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-plus-square">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                        </rect>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg> {{ __('Novo') }} </a> --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('Função') }}</th>
                                                <th>{{ __('Ordem') }}</th>
                                                <th class="text-center">{{ __('Quantidade de Prebendas') }}</th>
                                                <th class="text-center">{{ __('Valor Calculado') }}</th>
                                                <th width="110px">{{ __('Ações') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($funcoes as $funcao)
                                                <tr data-qtd-prebendas="{{ $funcao->qtd_prebendas }}">
                                                    <td>{{ $funcao->funcao }}</td>
                                                    <td>{{ $funcao->ordem }}</td>
                                                    <td class="text-center">
                                                        {{ is_null($funcao->qtd_prebendas) ? __('Não informado') : number_format($funcao->qtd_prebendas, 1, ',') }}
                                                    </td>
                                                    <td class="text-center valor-calculado"> {{ $funcao->valor_calculado }}
                                                    </td>
                                                    <td class="table-action">
                                                        <a href="{{ route('clerigos.prebendas.edit', $funcao->id) }}"
                                                            title="{{ __('Editar') }}" class="btn btn-sm btn-dark mr-1 btn-rounded">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="feather feather-edit-2">
                                                                <path
                                                                    d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>


                                    </table>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fim Conteúdo -->
            </div>
        </div>
    </div>

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
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

            if (ano) calcularPrebendasPorAno(ano);
        });

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
                        $('#valor').val(valorPrebenda); // Atualiza o campo de valor

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
        anoMaisRecente && calcularPrebendasPorAno(anoMaisRecente);
    </script>
@endsection

@endsection
