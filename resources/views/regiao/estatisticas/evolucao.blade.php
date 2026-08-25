@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
    ['text' => 'Evolução de Membros', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
<style>
    .swal2-popup .swal2-styled.swal2-cancel {
        color: white !important;
    }

    .toggle-icon {
        cursor: pointer;
        margin-right: 5px;
    }

    .child-row {
        display: none;
        /* Filhos ficam escondidos inicialmente */
    }
</style>
@endsection

@section('extras-scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const distritoSelect = document.getElementById('distrito_id');
        const igrejaSelect = document.getElementById('igreja_id');

        const carregarIgrejasPorDistrito = function(distritoId) {
            if (!igrejaSelect) {
                return;
            }

            if (!distritoId || distritoId === 'all') {
                igrejaSelect.innerHTML = '<option value="all">Selecione um distrito</option>';
                igrejaSelect.value = 'all';
                igrejaSelect.disabled = true;
                return;
            }

            igrejaSelect.disabled = false;
            igrejaSelect.innerHTML = '<option value="all">Carregando...</option>';

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
                let options = '<option value="all">Todas</option>';
                igrejas.forEach(igreja => {
                    options += `<option value="${igreja.id}">${igreja.nome}</option>`;
                });
                igrejaSelect.innerHTML = options;
                igrejaSelect.value = 'all';
            })
            .catch(() => {
                igrejaSelect.innerHTML = '<option value="all">Todas</option>';
                igrejaSelect.value = 'all';
            });
        };

        if (distritoSelect) {
            distritoSelect.addEventListener('change', function() {
                carregarIgrejasPorDistrito(this.value);
            });
        }

        document.querySelectorAll('.toggle-icon').forEach(function(icon) {
            icon.addEventListener('click', function() {
                let target = this.dataset.target;
                let rows = document.querySelectorAll(`.child-row[data-parent="${target}"]`);
                if (!rows.length) {
                    return;
                }

                let isHidden = rows[0].style.display === 'none' || rows[0].style.display === '';

                rows.forEach(row => {
                    row.style.display = isHidden ? 'table-row' : 'none';
                });

                if (isHidden) {
                    this.classList.remove('fa-plus-square');
                    this.classList.add('fa-minus-square');
                } else {
                    this.classList.remove('fa-minus-square');
                    this.classList.add('fa-plus-square');
                }
            });
        });

        if (document.querySelector('#evolucao-membros-table')) {
            new DataTable('#evolucao-membros-table', {
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'excel',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-excel"></i> {{ __('Excel') }}',
                                titleAttr: '{{ __('Excel') }}',
                                title: '{{ __('Evolução de Membros') }} - {{ $anoinicio ?? '' }} {{ __('a') }} {{ $anofinal ?? '' }}'
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-pdf"></i> {{ __('PDF') }}',
                                titleAttr: '{{ __('PDF') }}',
                                title: '{{ __('Evolução de Membros') }} - {{ $anoinicio ?? '' }} {{ __('a') }} {{ $anofinal ?? '' }}',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                customize: function(doc) {
                                    doc.defaultStyle.fontSize = 8;
                                    const tableNode = doc.content.find(function(item) {
                                        return item.table;
                                    });

                                    if (tableNode) {
                                        tableNode.table.widths = Array(tableNode.table.body[0].length).fill('*');
                                    }
                                }
                            }
                        ]
                    }
                },
                language: {
                    emptyTable: '{{ __('Nenhum registro encontrado') }}',
                    zeroRecords: '{{ __('Nenhum registro encontrado') }}'
                }
            });
        }
        // Validação do formulário
        document.getElementById('filter_form').addEventListener('submit', function(event) {
            let anoinicio = parseInt(document.getElementById('anoinicio').value);
            let anofinal = parseInt(document.getElementById('anofinal').value);

            if (anofinal < anoinicio) {
                event.preventDefault(); // Impede o envio do formulário
                Swal.fire({
                    icon: 'error',
                    title: 'Erro na seleção de datas',
                    text: 'O ano final não pode ser menor que o ano inicial!',
                    confirmButtonText: 'Entendi',
                });
            }
        });
    });
</script>

@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Evolução de Membros') }}</h4>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <!-- 🔹 Formulário de Pesquisa -->
            <form class="form-vertical" id="filter_form" method="GET">
                @php
                $anoAtual = intval(date('Y')); // Garante que seja um número inteiro
                $anoInicio = $anoAtual - 10;
                @endphp

                <div class="form-group row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="control-label">{{ __('* Ano Inicial:') }}</label>
                        <select class="form-control" id="anoinicio" name="anoinicio" required>
                            @for ($ano = $anoInicio; $ano <= $anoAtual; $ano++)
                                <option value="{{ $ano }}" {{ request()->input('anoinicio') == $ano ? 'selected' : '' }}>
                                    {{ $ano }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="control-label">{{ __('* Ano Final:') }}</label>
                        <select class="form-control" id="anofinal" name="anofinal" required>
                            @for ($ano = date('Y') - 10; $ano <= date('Y'); $ano++)
                                <option value="{{ $ano }}" {{ request()->input('anofinal') == $ano ? 'selected' : '' }}>
                                    {{ $ano }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="control-label">{{ __('Distrito:') }}</label>
                        <select class="form-control" id="distrito_id" name="distrito_id">
                            <option value="all" {{ (string) $distritoId === 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ (string) $distritoId === (string) $distrito->id ? 'selected' : '' }}>
                                    {{ $distrito->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="control-label">{{ __('Igreja:') }}</label>
                        <select class="form-control" id="igreja_id" name="igreja_id" {{ (string) $distritoId === 'all' ? 'disabled' : '' }}>
                            @if((string) $distritoId === 'all')
                                <option value="all">{{ __('Selecione um distrito') }}</option>
                            @else
                                <option value="all">{{ __('Todas') }}</option>
                            @endif
                            @foreach($igrejas as $igreja)
                                <option value="{{ $igreja->id }}" {{ (string) $igrejaId === (string) $igreja->id ? 'selected' : '' }}>
                                    {{ $igreja->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <div class="col-lg-12">
                        <button id="btn_buscar" type="submit" class="btn btn-primary">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                    </div>
                </div>
            </form>

            @if(request()->has('anoinicio') && request()->has('anofinal'))
            @if(isset($instituicoes_pais) && count($instituicoes_pais) > 0)
            <h4>{{ __('Resultados de') }} {{ $anoinicio }} {{ __('a') }} {{ $anofinal }}</h4>
            <div class="table-responsive">
                <table id="evolucao-membros-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Nome') }}</th>
                            @foreach (range($anoinicio, $anofinal) as $ano)
                            <th>{{ $ano }}</th>
                            @endforeach
                            <th>{{ __('Evolução') }}</th>
                            <th>{{ __('Percentual') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totaisPais = array_fill_keys(range($anoinicio, $anofinal), 0);
                        $totalEvolucaoPais = 0;
                        $totalAnoInicialPais = 0;
                        $totalAnoFinalPais = 0;
                        @endphp

                        @foreach ($instituicoes_pais as $pai)
                        <tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td>
                                <i class="fas fa-plus-square toggle-icon" data-target="pai-{{ $pai->id }}"></i>
                                {{ $pai->instituicao }}
                            </td>
                            @php
                            $valorAnoInicial = $pai->$anoinicio ?? 0;
                            $valorAnoFinal = $pai->$anofinal ?? 0;
                            $evolucao = $valorAnoFinal - $valorAnoInicial;

                            $percentual = $valorAnoInicial > 0 ? round(($evolucao / $valorAnoInicial) * 100, 2) : ($valorAnoFinal > 0 ? 100 * $valorAnoFinal : 0);

                            $totalAnoInicialPais += $valorAnoInicial;
                            $totalAnoFinalPais += $valorAnoFinal;
                            $totalEvolucaoPais += $evolucao;
                            @endphp
                            @foreach (range($anoinicio, $anofinal) as $ano)
                            <td>{{ $pai->$ano ?? 0 }}</td>
                            @php $totaisPais[$ano] += $pai->$ano ?? 0; @endphp
                            @endforeach
                            <td>{{ $evolucao }}</td>
                            <td>{{ $percentual }}%</td>
                        </tr>

                        @foreach ($instituicoes_filhos as $filho)
                        @if ($filho->instituicao_pai_id == $pai->id)
                        <tr class="child-row" data-parent="pai-{{ $pai->id }}">
                            <td>➜ {{ $filho->instituicao }}</td>
                            @php
                            $valorAnoInicialFilho = $filho->$anoinicio ?? 0;
                            $valorAnoFinalFilho = $filho->$anofinal ?? 0;
                            $evolucaoFilho = $valorAnoFinalFilho - $valorAnoInicialFilho;

                            $percentualFilho = $valorAnoInicialFilho > 0 ? round(($evolucaoFilho / $valorAnoInicialFilho) * 100, 2) : ($valorAnoFinalFilho > 0 ? 100 * $valorAnoFinalFilho : 0);
                            @endphp
                            @foreach (range($anoinicio, $anofinal) as $ano)
                            <td>{{ $filho->$ano ?? 0 }}</td>
                            @endforeach
                            <td>{{ $evolucaoFilho }}</td>
                            <td>{{ $percentualFilho }}%</td>
                        </tr>
                        @endif
                        @endforeach
                        @endforeach
                        @php
                        $percentualTotalColunas = $totalAnoInicialPais > 0
                            ? round(($totalEvolucaoPais / $totalAnoInicialPais) * 100, 2)
                            : ($totalAnoFinalPais > 0 ? 100 * $totalAnoFinalPais : 0);
                        @endphp
                        <tr style="font-weight: bold; background-color: #e9ecef;">
                            <td>{{ __('Total de Membros da Região') }}</td>
                            @foreach (range($anoinicio, $anofinal) as $ano)
                            <td>{{ $totaisPais[$ano] ?? 0 }}</td>
                            @endforeach
                            <td>{{ $totalEvolucaoPais }}</td>
                            <td>{{ number_format($percentualTotalColunas, 2, ',', '.') }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted">{{ __('Nenhum resultado encontrado para o período selecionado.') }}</p>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
