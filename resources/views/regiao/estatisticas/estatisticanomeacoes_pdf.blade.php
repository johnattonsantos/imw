<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Histórico de Nomeações - IMW PGA') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .header .info {
            text-align: left;
            margin-top: 10px;
        }

        .header .info .title {
            font-size: 12px;
            font-weight: bold;
        }

        .header .info .period {
            font-size: 10px;
        }

        .header .date {
            font-size: 8px;
            color: #555;
            text-align: right;
        }

        h4,
        h5 {
            color: #333;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }

        th,
        td {
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

@php
    use Carbon\Carbon;
@endphp

<body>
    <div class="header">
        <img src="{{ public_path('auth/images/login.png') }}" alt="{{ __('Logotipo') }}">
        <div class="info">
            <div class="title">Histórico de Nomeações - {{ $lancamentos[0]->instituicao }}</div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    !
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h6 class="mt-3">QUANTIDADE DE MEMBROS -
                        {{ $lancamentos[0]->escolaridade }}</h6>
                    <div class="table-responsive">
                        @if (request('visao') == 1)
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped" style="font-size: 90%;">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>{{ __('Nome') }}</th>
                                                <th>{{ __('Distrito') }}</th>
                                                <th>{{ __('Igreja') }}</th>
                                                <th>{{ __('Data Início') }}</th>
                                                <th>{{ __('Data Termino') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lancamentos as $lancamento)
                                                <tr>
                                                    <td colspan="5">
                                                        <i class="fas fa-plus-square toggle-icon"
                                                            data-target="pai-{{ $lancamento[0]->id }}"></i>
                                                        {{ $lancamento[0]->nome }}
                                                    </td>
                                                </tr>
                                                @foreach ($lancamento as $nomeacao)
                                                    <tr class="child-row" data-parent="pai-{{ $nomeacao->id }}">
                                                        <td>{{ $nomeacao->nome }}</td>
                                                        <td>{{ $nomeacao->distrito }}</td>
                                                        <td>{{ $nomeacao->igreja }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($nomeacao->inicio_nomeacao)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $nomeacao->fim_nomeacao ? \Carbon\Carbon::parse($nomeacao->fim_nomeacao)->format('d/m/Y') : 'Atual' }}
                                                    </tr>
                                                @endforeach
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>

                                <p class="text-center text-muted">{{ __('Nenhum resultado encontrado para o período selecionado.') }}</p>

                            </div>
                        @elseif (request('visao') == 2)
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped" style="font-size: 90%;">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>{{ __('Nome') }}</th>
                                                <th>{{ __('Clérigo') }}</th>
                                                <th>{{ __('Distrito') }}</th>
                                                <th>{{ __('Data Início') }}</th>
                                                <th>{{ __('Data Termino') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lancamentos as $lancamento)
                                                <tr>
                                                    <td colspan="5">
                                                        <i class="fas fa-plus-square toggle-icon"
                                                            data-target="pai-{{ $lancamento[0]->id }}"></i>
                                                        {{ $lancamento[0]->nome }}
                                                    </td>
                                                </tr>
                                                @foreach ($lancamento as $nomeacao)
                                                    <tr class="child-row" data-parent="pai-{{ $nomeacao->id }}">
                                                        <td>{{ $nomeacao->nome }}</td>
                                                        <td>{{ $nomeacao->clerigo }}</td>
                                                        <td>{{ $nomeacao->distrito }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($nomeacao->inicio_nomeacao)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $nomeacao->fim_nomeacao ? \Carbon\Carbon::parse($nomeacao->fim_nomeacao)->format('d/m/Y') : 'Atual' }}
                                                    </tr>
                                                @endforeach
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>

                                <p class="text-center text-muted">{{ __('Nenhum resultado encontrado para o período selecionado.') }}</p>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

</html>
