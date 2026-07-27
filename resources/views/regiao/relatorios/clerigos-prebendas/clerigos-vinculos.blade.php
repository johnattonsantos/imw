@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Clérigos', 'url' => '#', 'active' => false],
        ['text' => 'Clérigos por Vínculo', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <style>
        .resumo-vinculos .card {
            border: 1px solid #e5e9f2;
            box-shadow: 0 8px 18px rgba(31, 45, 61, .06);
        }

        .resumo-vinculos .valor {
            color: #4361ee;
            font-size: 1.45rem;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Relatório Clérigos por Vínculo') }} - {{ $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-row align-items-end">
                        <div class="form-group col-lg-3 col-md-6">
                            <label class="control-label">{{ __('Distrito') }}</label>
                            <select id="distrito" name="distrito" class="form-control">
                                <option value="all" {{ request('distrito', 'all') === 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}" {{ (string) request('distrito') === (string) $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6">
                            <label class="control-label">{{ __('Igreja') }}</label>
                            <select id="igreja" name="igreja" class="form-control">
                                <option value="all" {{ request('igreja', 'all') === 'all' ? 'selected' : '' }}>{{ __('Todas') }}</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" data-distrito="{{ $igreja->distrito_nome }}" {{ (string) request('igreja') === (string) $igreja->id ? 'selected' : '' }}>
                                        {{ $igreja->distrito_nome }} - {{ $igreja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-2 col-md-6">
                            <label class="control-label">{{ __('Tipo de Vínculo') }}</label>
                            <select id="tipo_vinculo" name="tipo_vinculo" class="form-control">
                                <option value="all" {{ request('tipo_vinculo', 'all') === 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                                <option value="integral" {{ request('tipo_vinculo') === 'integral' ? 'selected' : '' }}>{{ __('Integral') }}</option>
                                <option value="parcial" {{ request('tipo_vinculo') === 'parcial' ? 'selected' : '' }}>{{ __('Parcial') }}</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-2 col-md-6">
                            <label class="control-label">{{ __('Ônus') }}</label>
                            <select id="onus" name="onus" class="form-control">
                                <option value="all" {{ request('onus', 'all') === 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                                <option value="1" {{ request('onus') === '1' ? 'selected' : '' }}>{{ __('Com ônus') }}</option>
                                <option value="0" {{ request('onus') === '0' ? 'selected' : '' }}>{{ __('Sem ônus') }}</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-2 col-md-12">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn-block">
                                <x-bx-search /> {{ __('Buscar') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (request()->has('action'))
        <div class="col-lg-12 col-12 layout-spacing resumo-vinculos">
            <div class="row">
                <div class="col-md col-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="text-muted">{{ __('Total') }}</div>
                            <div class="valor">{{ $resumo['total'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="text-muted">{{ __('Integrais') }}</div>
                            <div class="valor">{{ $resumo['integrais'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="text-muted">{{ __('Parciais') }}</div>
                            <div class="valor">{{ $resumo['parciais'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="text-muted">{{ __('Com ônus') }}</div>
                            <div class="valor">{{ $resumo['com_onus'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="text-muted">{{ __('Sem ônus') }}</div>
                            <div class="valor">{{ $resumo['sem_onus'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <div class="table-responsive mt-0">
                        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="clerigos-vinculos">
                            <thead>
                                <tr>
                                    <th>{{ __('REGIÃO') }}</th>
                                    <th>{{ __('DISTRITO') }}</th>
                                    <th>{{ __('IGREJA') }}</th>
                                    <th>{{ __('CLÉRIGO') }}</th>
                                    <th>{{ __('FUNÇÃO MINISTERIAL') }}</th>
                                    <th>{{ __('TIPO DE VÍNCULO') }}</th>
                                    <th>{{ __('ÔNUS') }}</th>
                                    <th>{{ __('QTD. PREBENDAS') }}</th>
                                    <th>{{ __('INTEGRALIZAÇÃO') }}</th>
                                    <th>{{ __('CONTATO') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clerigos_vinculos as $clerigo)
                                    <tr>
                                        <td>{{ $clerigo->regiao_nome ?: $regiao->nome }}</td>
                                        <td>{{ $clerigo->distrito_nome }}</td>
                                        <td>{{ $clerigo->igreja_nome }}</td>
                                        <td>{{ $clerigo->clerigo_nome }}</td>
                                        <td>{{ $clerigo->funcao_ministerial }}</td>
                                        <td>{{ __($clerigo->tipo_vinculo) }}</td>
                                        <td>{{ __($clerigo->onus_descricao) }}</td>
                                        <td>{{ is_null($clerigo->qtd_prebendas) ? __('Não informado') : number_format($clerigo->qtd_prebendas, 2, ',', '.') }}</td>
                                        <td>{{ $clerigo->data_integralizacao ?: '-' }}</td>
                                        <td>{{ $clerigo->contato ? formatStr($clerigo->contato, '## (##) #####-####') : '-' }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('extras-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>

    @if (request()->has('action'))
        <script>
            new DataTable('#clerigos-vinculos', {
                scrollX: true,
                scrollY: 420,
                scrollCollapse: true,
                layout: {
                    topStart: {
                        buttons: [
                            'pageLength',
                            {
                                extend: 'excel',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                titleAttr: 'Excel',
                                title: 'IMW - RELATÓRIO CLÉRIGOS POR VÍNCULO'
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-pdf"></i> PDF',
                                titleAttr: 'PDF',
                                title: 'IMW - RELATÓRIO CLÉRIGOS POR VÍNCULO',
                                orientation: 'landscape',
                                pageSize: 'A4'
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-print"></i> {{ __('Imprimir') }}',
                                titleAttr: '{{ __('Imprimir') }}',
                                title: 'IMW - RELATÓRIO CLÉRIGOS POR VÍNCULO'
                            }
                        ]
                    },
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    emptyTable: '{{ __('Nenhum registro encontrado') }}',
                    info: '{{ __('Mostrando de _START_ até _END_ de _TOTAL_ registros') }}',
                    infoEmpty: '{{ __('Mostrando 0 até 0 de 0 registros') }}',
                    lengthMenu: '{{ __('Mostrar _MENU_ registros') }}',
                    loadingRecords: '{{ __('Carregando...') }}',
                    processing: '{{ __('Processando...') }}',
                    search: '{{ __('Pesquisar') }}',
                    zeroRecords: '{{ __('Nenhum registro encontrado') }}',
                    paginate: {
                        first: '{{ __('Primeiro') }}',
                        last: '{{ __('Último') }}',
                        next: '{{ __('Próxima') }}',
                        previous: '{{ __('Anterior') }}'
                    }
                }
            });
        </script>
    @endif
@endsection
