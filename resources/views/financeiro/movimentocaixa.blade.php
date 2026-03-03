@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Financeiro', 'url' => '/', 'active' => false],
        ['text' => 'Movimento de Caixa', 'url' => '#', 'active' => true],
    ]">
    </x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .input-group-addon {
            cursor: pointer;
            padding: 0 10px;
            border: none;
            background-color: transparent;
        }

        .datepicker {
            padding-right: 30px;
        }

        .datepicker+.input-group-addon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }

        .btn-sm-custom {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
        }

        @media (max-width: 768px) {
            .btn-responsive {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#movimentoCaixaTable').DataTable({
                "pageLength": 1000,
                "language": {
                    "decimal": "",
                    "emptyTable": "Nenhum dado disponível na tabela",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                    "infoFiltered": "(filtrado de _MAX_ entradas totais)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ entradas",
                    "loadingRecords": "Carregando...",
                    "processing": "Processando...",
                    "search": "Pesquisar:",
                    "zeroRecords": "Nenhum registro correspondente encontrado",
                    "paginate": {
                        "first": "Primeiro",
                        "last": "Último",
                        "next": "Próximo",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": ativar para classificar a coluna em ordem crescente",
                        "sortDescending": ": ativar para classificar a coluna em ordem decrescente"
                    }
                }
            });

            // Aplicar máscara de data e datepicker
            $('#d1, #d2').mask('00/00/0000');
            $('#d1, #d2').datepicker({
                dateFormat: 'dd/mm/yy'
            });

            // Limpar o campo Pagante ao carregar a página
            $('#pagante_favorecido').val('').trigger('change');
        });
    </script>
@endsection

@section('content')
    @include('extras.alerts')
    <!-- Modal Anexos -->
    <div class="modal fade" id="anexosModal" tabindex="-1" role="dialog" aria-labelledby="anexosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="anexosModalLabel">Anexos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="anexosList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Filtros para pesquisa</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <!-- Conteúdo -->
                    <form action="">
                        <div class="row">
                            <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="caixa_id">Caixa</label>
                                <select class="form-control select2" data-bs-toggle="select2" width="fit" name="caixa_id"
                                    id="caixa_id">
                                    <option value="" hidden disabled selected>Selecione</option>
                                    @foreach ($caixas as $caixa)
                                        <option value="{{ $caixa->id }}">{{ $caixa->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="plano_conta_id">Plano de Contas</label>
                                <select class="form-control select2" data-bs-toggle="select2" width="fit"
                                    name="plano_conta_id" id="plano_conta_id">
                                    <option value="" hidden disabled selected>Selecione</option>
                                    @foreach ($planoContas as $pc)
                                        <option {{ !$pc->selecionavel ? 'disabled' : '' }} value="{{ $pc->id }}">
                                            {{ $pc->numeracao }} - {{ $pc->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 col-lg-4 col-md-6 col-sm-12 pf lgpd">
                                <label for="d1">Data Inicio</label>
                                <div class="input-group">
                                    <input class="form-control datepicker" id="d1" name="d1" maxlength="20"
                                        value="" type="text" placeholder="">
                                    <span class="input-group-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 col-lg-4 col-md-6 col-sm-12 pf lgpd">
                                <label for="d2">Data Fim</label>
                                <div class="input-group">
                                    <input class="form-control datepicker" id="d2" name="d2" maxlength="20"
                                        value="" type="text" placeholder="">
                                    <span class="input-group-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                             <div class="mb-3 col-lg-2 col-md-6 col-sm-12">
                                <label for="consolidado">Consolidado</label>
                                <select class="form-control select2" data-bs-toggle="select2" width="fit" name="consolidado"
                                    id="consolidado">
                                    <option value="0" {{request()->input('consolidado') ? '' : 'selected'}}>Não</option>
                                    <option value="1" {{request()->input('consolidado') == 1 ? 'selected' : ''}}>Sim</option>
                                </select>
                            </div>
                            <div class="mb-3 col-lg-2 col-md-6 col-sm-12">
                                <div class="col-auto" style="margin-top: 32px;">
                                    <button type="submit" class="btn btn-primary btn-lg btn-rounded"><x-bx-search />
                                        Pesquisar</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-start flex-wrap">
                            <a href="{{ route('financeiro.entrada') }}" title="Novo registro de entrada"
                                class="btn btn-success btn-rounded btn-responsive mr-2 mb-2">
                                <x-bx-plus-circle /> Entrada
                            </a>

                            <a href="{{ route('financeiro.saida') }}" title="Novo registro de saída"
                                class="btn btn-danger btn-rounded btn-responsive mr-2 mb-2">
                                <x-bx-minus-circle /> Saída
                            </a>

                            <a href="{{ route('financeiro.transferencia') }}" title="Novo registro de transferência"
                                class="btn btn-warning btn-rounded btn-responsive mr-2 mb-2">
                                <x-bx-chevrons-right /> Transferência
                            </a>

                            <a href="{{ route('financeiro.saldo') }}" title="Saldo Atual"
                                class="btn btn-primary btn-rounded btn-responsive mb-2">
                                <x-bx-wallet /> Saldo
                            </a>

                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table id="movimentoCaixaTable" class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Caixa</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Plano de Conta</th>
                                    <th>Pagante/Favorecido</th>
                                    <th width="150"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lancamentos as $index => $lancamento)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($lancamento->data_movimento)->format('d/m/Y') }}</td>
                                        <td>{{ $lancamento->caixa->descricao }}</td>
                                        <td>
                                            @if ($lancamento->tipo_lancamento == 'E')
                                                <div class="badge badge-success">R$
                                                    {{ number_format($lancamento->valor, 2, ',', '.') }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lancamento->tipo_lancamento == 'S')
                                                <div class="badge badge-danger">R$
                                                    {{ number_format($lancamento->valor, 2, ',', '.') }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $lancamento->planoConta->nome }}</td>
                                        <td>{{ $lancamento->pagante_favorecido }}</td>
                                        <td class="d-flex align-items-center">
                                            @if (in_array($lancamento->planoConta->posicao, [3, 301, 302, 303, 304, 305, 306, 311, 312, 313, 314]))
                                                <form action="{{ route('financeiro.excluirMovimento', $lancamento->id) }}"
                                                    method="POST" style="display: inline-block;"
                                                    id="form_delete_excluirMovimento_{{ $index }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" title="Apagar"
                                                        class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete btn-sm-custom"
                                                        data-form-id="form_delete_excluirMovimento_{{ $index }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                            height="18" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-trash-2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                            <line x1="10" y1="11" x2="10"
                                                                y2="17"></line>
                                                            <line x1="14" y1="11" x2="14"
                                                                y2="17"></line>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                            <form action="{{ route('financeiro.excluirMovimento', $lancamento->id) }}"
                                                    method="POST" style="display: inline-block;"
                                                    id="form_delete_excluirMovimento_{{ $index }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" title="Apagar"
                                                        class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete btn-sm-custom"
                                                        data-form-id="form_delete_excluirMovimento_{{ $index }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                            height="18" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="feather feather-trash-2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                            <line x1="10" y1="11" x2="10"
                                                                y2="17"></line>
                                                            <line x1="14" y1="11" x2="14"
                                                                y2="17"></line>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <a class="btn btn-sm btn-dark mr-2 btn-rounded btn-sm-custom" title="Editar"
                                                    href="{{ route('financeiro.editarMovimento', ['id' => $lancamento->id, 'tipo_lancamento' => $lancamento->tipo_lancamento]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-edit-2">
                                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                        </path>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if ($lancamento->tipo_lancamento == 'S')
                                                <button type="button" title="Anexos"
                                                    class="btn btn-sm btn-info mr-2 btn-rounded btn-anexos btn-sm-custom"
                                                    data-lancamento-id="{{ $lancamento->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5-10-5-10 5z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button class="btn btn-success btn-rounded btn-responsive mb-2" onclick="exportReportToExcel();">
                            <i class="fa fa-file-excel" aria-hidden="true"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            {{-- Fim Conteúdo --}}
        </div>
    </div>

    <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
    <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>

    <script>
        $('.btn-confirm-delete').on('click', function() {
            const formId = $(this).data('form-id')
            swal({
                title: 'Deseja realmente apagar este registro?',
                type: 'error',
                showCancelButton: true,
                confirmButtonText: "Deletar",
                confirmButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if (result.value) document.getElementById(formId).submit()
            })
        });

        $('.btn-anexos').on('click', function() {
            console.log('Botão de anexos clicado'); // Verificar se o evento está sendo disparado
            let lancamentoId = $(this).data('lancamento-id');
            $.ajax({
                url: `/financeiro/anexos/${lancamentoId}`,
                method: 'GET',
                success: function(response) {
                    let anexosList = $('#anexosList');
                    anexosList.empty();

                    if (response.length === 0) {
                        anexosList.append('<li>Nenhum anexo encontrado</li>');
                    } else {
                        response.forEach(function(anexo) {
                            anexosList.append(
                                `<li><a href="${anexo.url}" download>${anexo.nome}</a></li>`
                            );
                        });
                    }

                    $('#anexosModal').modal('show');
                },
                error: function(error) {
                    console.log('Erro ao buscar anexos:', error); // Verificar se há erros na requisição AJAX
                }
            });
        });

        $(document).ready(function() {
            // Limpar o campo Pagante ao carregar a página
            $('#pagante_favorecido').val('').trigger('change');
        });
    </script>
@endsection
