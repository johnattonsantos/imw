@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Instituições', 'url' => '/instituicoes-regiao?tipo_instituicao_id=1', 'active' => false],
        ['text' => 'Nomeações', 'url' => '#', 'active' => true],
    ]">
    </x-breadcrumb>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
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
                        <h4>Nomeações da instituição: {{ $instituicao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <!-- Conteúdo -->
                <div class="card mb-3">
                    <div class="bg-holder d-none d-lg-block bg-card gb-title">
                    </div>
                    <!--/.bg-holder-->
                    <!-- <div class="card-body">
                        <form>
                            <div class="row mb-4">
                                <div class="col-2">
                                    <select name="status" id="" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="ativo"
                                            {{ !empty($status) && $status === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                        <option value="inativo"
                                            {{ !empty($status) && $status === 'inativo' ? 'selected' : '' }}>Inativo
                                        </option>
                                    </select>
                                </div>
                                <div class="col-auto" style="margin-left: -19px;">
                                    <button type="submit" class="btn btn-primary btn-rounded">Pesquisar</button>
                                </div>
                            </div>
                        </form>
                    </div> -->
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Listagem de Nomeações</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('instituicoes-regiao.nomeacoes.novo', ['instituicao' => $instituicao->id]) }}"
                                    title="Inserir um novo registro" class="btn btn-primary right btn-rounded"> <svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
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
                                                <th>Nome</th>
                                                <th>Função ministerial</th>
                                                <th>Nomeação</th>
                                                <th>Término</th>
                                                <th width="120px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($nomeacoes as $index => $nomeacao)
                                                @if(isset($nomeacao->pessoa->nome))
                                                    <tr>

                                                        <td>
                                                            {{ $nomeacao->pessoa->nome }}
                                                        </td>
                                                        <td>
                                                            {{ $nomeacao->funcaoMinisterial?->funcao }}
                                                        </td>

                                                        <td>
                                                            {{ $nomeacao->data_nomeacao }}
                                                        </td>

                                                        <td>
                                                            {{ $nomeacao->data_termino }}
                                                        </td>

                                                        <td class="table-action">
                                                            @if (!$nomeacao->data_termino)
                                                                <button type="button" class="btn btn-sm btn-danger btn-rounded"
                                                                    data-bs-toggle="modal" data-bs-target="#modalFinalizar"
                                                                    data-nomeacao-id="{{ $nomeacao->id }}"
                                                                    onclick="abrirModal({{ $instituicao->id }},{{ $nomeacao->id }}, {{ $nomeacao->funcao }})">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="feather feather-slash">
                                                                        <circle cx="12" cy="12" r="10"></circle>
                                                                        <line x1="4.93" y1="4.93" x2="19.07"
                                                                            y2="19.07"></line>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    @endif
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
     <!-- Modal -->
    <div class="modal fade " id="modalFinalizar" tabindex="-1" aria-labelledby="modalFinalizarLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content"  style="max-width: 500px; margin: 0 auto">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFinalizarLabel">Finalizar Nomeação</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="funcao" class="form-control-plaintext">{{ old('nome') }}</p>
                    <form id="finalizarForm" method="POST">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <div class="mb-3">
                            <label for="data_termino" class="form-label">Data de Término</label>
                            <input type="date" class="form-control" id="data_termino" name="data_termino" required
                                value="{{ old('data_termino') }}" @error('data_termino') is-invalid @enderror>
                            @error('data_termino')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                         <input type="hidden" id="nomeacao_id" name="nomeacao_id" value="{{ old('nomeacao_id') }}">
                        <div class="modal-footer">
                            <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function abrirModal(instituicao_id,id, funcao) {
            const nomeacaoId = id;
            const form = $('#finalizarForm');
            form.attr('action', '/instituicoes-regiao/nomeacoes/' + instituicao_id + '/finalizar/' + id);

            if (document.getElementById('funcao')) {
                document.getElementById('funcao').textContent = funcao;
            }
            if (document.getElementById('nomeacao_id')) {
                document.getElementById('nomeacao_id').value = id;
            }


            const modalElement = document.getElementById('modalFinalizar');
            let modal = bootstrap.Modal.getInstance(modalElement);

            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }

            modal.show();
        }
        $('#modalFinalizar').on('hidden.bs.modal', function() {

        });

        @if ($errors->any())
                $(document).ready(function() {
                    var nome = "{{ old('nome') }}";
                    var id = "{{ old('nomeacao_id') }}";
                    abrirModal(clerigoId,id, nome);
                });
        @endif
    </script>
@endsection
