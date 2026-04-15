@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
    ['text' => 'Estatísticas GCEU', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Relatório Estatísticas GCEU</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="form-vertical" id="filtro-estatisticas-gceu">
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">Distrito:</label>
                    </div>
                    <div class="col-lg-3">
                        <select name="distrito_id" id="distrito_id" class="form-control">
                            <option value="">Todos</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ (string) $distrito_id === (string) $distrito->id ? 'selected' : '' }}>
                                    {{ $distrito->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 text-right">
                        <label class="control-label">Igreja:</label>
                    </div>
                    <div class="col-lg-3">
                        <select name="igreja_id" id="igreja_id" class="form-control">
                            <option value="">Todas</option>
                            @foreach($igrejas as $igreja)
                                <option value="{{ $igreja->id }}" {{ (string) $igreja_id === (string) $igreja->id ? 'selected' : '' }}>
                                    {{ $igreja->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary"><x-bx-search /> Buscar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped display nowrap" id="estatisticas-gceu-regiao" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Distrito</th>
                            <th>Igreja</th>
                            <th>Quant. GCEUS</th>
                            <th>% GCEUS</th>
                            <th>Quant. Membros</th>
                            <th>% Membros</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalGceus = (int) ($totais['qtd_gceus'] ?? 0);
                            $totalMembros = (int) ($totais['qtd_membros_gceu'] ?? 0);
                        @endphp
                        @foreach($dados as $item)
                            <tr>
                                <td>{{ $item->distrito }}</td>
                                <td>{{ $item->igreja }}</td>
                                <td>{{ (int) $item->qtd_gceus }}</td>
                                <td>
                                    {{ $totalGceus > 0 ? number_format((((int) $item->qtd_gceus) / $totalGceus) * 100, 2, ',', '.') : '0,00' }}%
                                </td>
                                <td>{{ (int) $item->qtd_membros_gceu }}</td>
                                <td>
                                    {{ $totalMembros > 0 ? number_format((((int) $item->qtd_membros_gceu) / $totalMembros) * 100, 2, ',', '.') : '0,00' }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right;">TOTAL GERAL:</th>
                            <th>{{ $totais['qtd_gceus'] ?? 0 }}</th>
                            <th>100,00%</th>
                            <th>{{ $totais['qtd_membros_gceu'] ?? 0 }}</th>
                            <th>100,00%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script>
        const distritoSelect = document.getElementById('distrito_id');
        const igrejaSelect = document.getElementById('igreja_id');
        const todasIgrejasHtml = igrejaSelect ? igrejaSelect.innerHTML : '';

        distritoSelect?.addEventListener('change', function () {
            const distritoId = this.value;
            const igrejaSelecionadaAtual = igrejaSelect.value;

            if (!distritoId) {
                igrejaSelect.innerHTML = todasIgrejasHtml;
                igrejaSelect.value = '';
                return;
            }

            igrejaSelect.innerHTML = '<option value="">Carregando...</option>';

            fetch(`/instituicoes/igrejasByDistrito/${distritoId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Falha ao carregar igrejas');
                    }
                    return response.json();
                })
                .then(igrejas => {
                    let options = '<option value="">Todas</option>';
                    igrejas.forEach(igreja => {
                        const selected = String(igreja.id) === String(igrejaSelecionadaAtual) ? 'selected' : '';
                        options += `<option value="${igreja.id}" ${selected}>${igreja.nome}</option>`;
                    });
                    igrejaSelect.innerHTML = options;
                })
                .catch(() => {
                    igrejaSelect.innerHTML = '<option value="">Todas</option>';
                });
        });

        igrejaSelect?.addEventListener('change', function () {
            document.getElementById('filtro-estatisticas-gceu').submit();
        });

        new DataTable('#estatisticas-gceu-regiao', {
            order: [[2, 'desc']],
            pageLength: 25,
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'IMW - RELATÓRIO ESTATÍSTICAS GCEU (REGIONAL)'
                        },
                        {
                            extend: 'pdf',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'IMW - RELATÓRIO ESTATÍSTICAS GCEU (REGIONAL)',
                            customize: function (doc) {
                                if (doc.content && doc.content[1] && doc.content[1].table) {
                                    const columnCount = doc.content[1].table.body[0].length;
                                    doc.content[1].table.widths = Array(columnCount).fill('*');
                                    const centeredColumns = [2, 3, 4, 5];
                                    doc.content[1].table.body.forEach(function (row) {
                                        centeredColumns.forEach(function (index) {
                                            if (row[index] !== undefined) {
                                                if (typeof row[index] === 'string' || typeof row[index] === 'number') {
                                                    row[index] = { text: String(row[index]), alignment: 'center' };
                                                } else {
                                                    row[index].alignment = 'center';
                                                }
                                            }
                                        });
                                    });
                                }
                            }
                        }
                    ]
                },
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            language: {
                decimal: ',',
                thousands: '.',
                processing: 'Processando...',
                loadingRecords: 'Carregando...',
                lengthMenu: 'Exibir _MENU_ resultados por página',
                zeroRecords: 'Nenhum registro encontrado',
                emptyTable: 'Nenhum registro encontrado',
                info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 até 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros no total)',
                search: 'Pesquisar',
                paginate: {
                    first: 'Primeira',
                    previous: 'Anterior',
                    next: 'Próxima',
                    last: 'Última'
                },
                aria: {
                    sortAscending: ': ativar para ordenar a coluna de forma crescente',
                    sortDescending: ': ativar para ordenar a coluna de forma decrescente'
                }
            }
        });
    </script>
@endsection
