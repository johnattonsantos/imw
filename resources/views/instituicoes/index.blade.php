@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[['text' => 'Instituições', 'url' => '/', 'active' => true]]"></x-breadcrumb>
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

@section('extras-scripts')
<script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
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
                    <h4>Filtro por tipo de instituição</h4>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET">
                        <!-- Linha para Tipo de Instituição -->
                        <div class="row mb-3">
                            <div class="col-4">
                                <select name="tipo_instituicao_id" id="tipo_instituicao_id" class="form-control">
                                    <option value="" disabled {{ !request('tipo_instituicao_id') ? 'selected' : '' }}>* Selecione o tipo de instituição</option>
                                    <option value="1" {{ request('tipo_instituicao_id') == 1 ? 'selected' : '' }}>Igrejas</option>
                                    <option value="5" {{ request('tipo_instituicao_id') == 5 ? 'selected' : '' }}>Secretarias</option>
                                    <option value="2" {{ request('tipo_instituicao_id') == 2 ? 'selected' : '' }}>Distritos</option>
                                </select>
                            </div>


                            <div class="col-4">
                                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Pesquisar...">
                            </div>
                            <div class="col-2">
                                <select name="ativo" id="ativo" class="form-control">
                                    <option value="" {{ request('ativo') === null || request('ativo') === '' ? 'selected' : '' }}>Todos</option>
                                    <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-rounded">
                                    Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(request()->has('search') || request()->has('tipo_instituicao_id'))
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Listagem de Registros</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('instituicoes-regiao.novo') }}" title="Inserir um novo registro"
                                class="btn btn-primary right btn-rounded"> <svg xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-plus-square">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg> Novo </a>
                            <div class="table-responsive">
                                <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>NOME</th>
                                            <th>E-MAIL</th>
                                            <th>TELEFONE</th>
                                            <th>CIDADE</th>
                                            <th>STATUS</th>
                                            <th width="310px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instituicoes as $index => $instituicao)
                                        <tr>
                                            <td>
                                                @if ($instituicao->deleted_at)
                                                <del>{{ $instituicao->nome }}</del>
                                                @else
                                                {{ $instituicao->nome }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($instituicao->deleted_at)
                                                <del>
                                                    @if ($instituicao->email)
                                                    <a href="mailto:{{ $instituicao->email }}">
                                                        <i class="fas fa-envelope"></i> {{ $instituicao->email }}
                                                    </a>
                                                    @else
                                                    {{ $instituicao->email }}
                                                    @endif
                                                </del>
                                                @else
                                                @if ($instituicao->email)
                                                <a href="mailto:{{ $instituicao->email }}">
                                                    <i class="fas fa-envelope"></i> {{ $instituicao->email }}
                                                </a>
                                                @else
                                                {{ $instituicao->email }}
                                                @endif
                                                @endif
                                            </td>

                                            <td>
                                                @if ($instituicao->deleted_at)
                                                <del>
                                                    @if ($instituicao->ddd && $instituicao->telefone)
                                                    @php
                                                    // Remove caracteres não numéricos do telefone
                                                    $telefoneCompleto = preg_replace('/\D/', '', $instituicao->ddd . $instituicao->telefone);
                                                    $isCelular = substr($telefoneCompleto, 2, 1) == '9'; // Verifica se o número começa com '9' (após o DDD)
                                                    @endphp

                                                    @if ($isCelular)
                                                    <a href="https://wa.me/{{ $telefoneCompleto }}" target="_blank">
                                                        <i class="fab fa-whatsapp"></i> ({{ $instituicao->ddd }}) {{ $instituicao->telefone }}
                                                    </a>
                                                    @else
                                                    ({{ $instituicao->ddd }}) {{ $instituicao->telefone }}
                                                    @endif
                                                    @else
                                                    {{ $instituicao->telefone ?: ($instituicao->ddd ?: '') }}
                                                    @endif
                                                </del>
                                                @else
                                                @if ($instituicao->ddd && $instituicao->telefone)
                                                @php
                                                // Remove caracteres não numéricos do telefone
                                                $telefoneCompleto = preg_replace('/\D/', '', $instituicao->ddd . $instituicao->telefone);
                                                $isCelular = substr($telefoneCompleto, 2, 1) == '9'; // Verifica se o número começa com '9' (após o DDD)
                                                @endphp

                                                @if ($isCelular)
                                                <a href="https://wa.me/{{ $telefoneCompleto }}" target="_blank">
                                                    <i class="fab fa-whatsapp"></i> ({{ $instituicao->ddd }}) {{ $instituicao->telefone }}
                                                </a>
                                                @else
                                                ({{ $instituicao->ddd }}) {{ $instituicao->telefone }}
                                                @endif
                                                @else
                                                {{ $instituicao->telefone ?: ($instituicao->ddd ?: '') }}
                                                @endif
                                                @endif
                                            </td>

                                            <td>
                                                @if ($instituicao->deleted_at)
                                                <del>{{ mb_strtoupper($instituicao->cidade ?? '') }}</del>
                                                @else
                                                {{ mb_strtoupper($instituicao->cidade ?? '') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ((int) $instituicao->ativo === 1)
                                                    <span class="badge badge-success">Ativo</span>
                                                @else
                                                    <span class="badge badge-danger">Inativo</span>
                                                @endif
                                            </td>


                                            <td class="table-action">
                                                <a href="{{ route('instituicoes-regiao.nomeacoes', $instituicao->id) }}"
                                                    title="Nomeações" class="btn btn-primary right btn-rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-plus-square">
                                                        <rect x="3" y="3" width="18" height="18"
                                                            rx="2" ry="2">
                                                        </rect>
                                                        <line x1="12" y1="8" x2="12"
                                                            y2="16"></line>
                                                        <line x1="8" y1="12" x2="16"
                                                            y2="12"></line>
                                                    </svg>
                                                </a>
                                            
                                                @if (!$instituicao->deleted_at)
                                                <a href="javascript:void(0);" title="Visualizar"
                                                    class="btn btn-sm btn-info mr-1 btn-rounded btn-view-details"
                                                    data-instituicao-id="{{ $instituicao->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-eye">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z">
                                                        </path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>

                                                </a>
                                                <a href="{{ route('instituicoes-regiao.editar', $instituicao->id) }}"
                                                    title="Editar"
                                                    class="btn btn-sm btn-dark mr-1 btn-rounded">
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
                                                @endif
                                                
                                                @if ((int) $instituicao->ativo === 0 || $instituicao->deleted_at)
                                                    <form
                                                        action="{{ route('instituicoes-regiao.ativar', [$instituicao->id, 'search' => request('search')]) }}"
                                                        method="POST" style="display: inline-block;"
                                                        id="form_ativar_instituicao_{{ $index }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" title="Ativar"
                                                            class="btn btn-sm btn-success mr-1 btn-rounded btn-confirm-ativar"
                                                            data-form-ativar-id="form_ativar_instituicao_{{ $index }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="feather feather-refresh-cw">
                                                                <polyline points="23 4 23 10 17 10"></polyline>
                                                                <polyline points="1 20 1 14 7 14"></polyline>
                                                                <path d="M3.51 9a9 9 0 0 1 13.36-5.36L23 10m-2 6a9 9 0 0 1-13.36 5.36L1 14"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif


                                            </td>
                                        </tr>
                                        <!-- Modal de Visualização -->
                                        <div class="modal fade" id="viewDetailsModal_{{ $instituicao->id }}"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="viewDetailsModalLabel_{{ $instituicao->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                                role="document"> <!-- Adiciona o scroll ao modal -->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Os detalhes do instituicao serão preenchidos aqui pelo JavaScript -->
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            {{ $instituicoes->appends(request()->query())->links('vendor.pagination.index') }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- Fim Conteúdo -->
        </div>
    </div>
</div>

<script>
    // Confirmação para ativar a instituição
    $('.btn-confirm-ativar').on('click', function() {
        const formId = $(this).data('form-ativar-id');
        const params = new URLSearchParams(window.location.search); // Captura os parâmetros da URL
        const filtro = params.toString(); // Converte os parâmetros em uma string
        const form = document.getElementById(formId);
        form.action = form.action + (filtro ? '?' + filtro : ''); // Adiciona os parâmetros à ação do formulário
        swal({
            title: 'Deseja realmente ativar esta instituição?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: "Sim",
            confirmButtonColor: "#28a745", // Verde para ativar
            cancelButtonText: "Não",
            cancelButtonColor: "#3085d6",
            padding: '2em'
        }).then(function(result) {
            if (result.value) {
                form.submit();
            }
        });
    });


    $('.btn-view-details').on('click', function() {
        var instituicaoId = $(this).data('instituicao-id');
        var modalId = '#viewDetailsModal_' + instituicaoId;
        var button = $(this);

        // Adicionar o ícone de loading no botão usando Font Awesome
        var originalButtonText = button.html(); // Salvar o texto original do botão
        button.html('<i class="fas fa-spinner fa-spin"></i>'); // Usar Font Awesome spinner

        $.ajax({
            url: '/instituicoes-regiao/' + instituicaoId + '/detalhes',
            method: 'GET',
            success: function(data) {
                // Preenche o modal com as informações do instituicao e as nomeações, ambos em formato de card
                $(modalId).find('.modal-body').html(`
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        Informações do Instituições
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> ${data.nome}</p>
                        <p><strong>CNPJ:</strong> ${data.cnpj || '-'}</p>
                        <p><strong>CEP:</strong> ${data.cep || '-'}</p>
                        <p><strong>Endereço:</strong> ${data.endereco || '-'}, ${data.numero || '-'}</p>
                        <p><strong>Complemento:</strong> ${data.complemento || '-'}</p>
                        <p><strong>Bairro:</strong> ${data.bairro || '-'}</p>
                        <p><strong>Cidade:</strong> ${data.cidade || '-'}</p>
                        <p><strong>UF:</strong> ${data.uf || '-'}</p>
                        <p><strong>País:</strong> ${data.pais || '-'}</p>
                        <p><strong>DDD:</strong> ${data.ddd || '-'}</p>
                        <p><strong>Telefone:</strong> ${data.telefone || '-'}</p>
                        <p><strong>Site:</strong> ${data.site || '-'}</p>
                        <p><strong>Email:</strong> ${data.email || '-'}</p>
                        <p><strong>Responsável:</strong> ${data.pastor || '-'}</p>
                    </div>
                </div>
            `);

                // Exibe o modal
                $(modalId).modal('show');
            },
            error: function(err) {
                console.error("Erro ao buscar os detalhes do instituicao: ", err);
            },
            complete: function() {
                // Restaurar o texto original do botão após o carregamento
                button.html(originalButtonText);
            }
        });
    });
    const params = new URLSearchParams(window.location.search);
    const id = params.get('tipo_instituicao_id');

    console.log(id);
</script>
@endsection
