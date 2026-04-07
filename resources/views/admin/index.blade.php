@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Segurança', 'url' => '/', 'active' => false],
        ['text' => 'Todos Usuários', 'url' => '/admin/', 'active' => true],
    ]">
    </x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
    </style>
@endsection

@include('extras.alerts')

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid">
        <a href="{{ route('admin.novo') }}" class="btn btn-primary position-relative mt-3 mb-3 ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-plus-circle">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <span class="ml-2">INCLUIR USUÁRIO</span>
        </a>
        @php
            $perfilNomeSessao = (string) optional(session('session_perfil'))->perfil_nome;
            $isAdminSistema = \App\Models\Perfil::correspondeCodigo($perfilNomeSessao, \App\Models\Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
        @endphp
        @if ($isAdminSistema)
            <a href="{{ route('admin.modulo-geral') }}" class="btn btn-dark position-relative mt-3 mb-3 ml-2">
                <span class="ml-1">MÓDULO GERAL</span>
            </a>
        @endif
    </div>
    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Lista de Usuários</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form>
                    <div class="row mb-4">
                        <div class="col-4">
                            <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                placeholder="Pesquisar...">
                        </div>
                        <div class="col-auto" style="margin-left: -19px;">
                            <button type="submit" class="btn btn-primary btn-rounded"><x-bx-search /> Pesquisar</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <thead>
                            <tr>
                                <th>E-MAIL</th>
                                <th>NOME</th>
                                <th>NÍVEIS DE ACESSO</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $index => $usuario)
                                <tr>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ $usuario->name }}</td>
                                    <td>
                                        <div class="row mb-4">
                                            <div class="col-xl-12">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Instituição</th>
                                                            <th>Perfil</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $perfis = $usuario->perfilUser;
                                                        @endphp
                                                        @foreach ($perfis as $perfilUser)
                                                            <tr>
                                                                <td>
                                                                    @if ($perfilUser->instituicao)
                                                                        {{ $perfilUser->instituicao->nome }}
                                                                    @else
                                                                        <!-- Se a relação não estiver carregada ou não existir -->
                                                                        Sem Instituição Associada
                                                                    @endif
                                                                </td>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-pill badge-secondary">{{ $perfilUser->perfil->nome }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
            <td class="text-center">
                <a href="{{ route('admin.editar', $usuario->id) }}" title="Editar"
                    class="btn btn-sm btn-dark mr-2 btn-rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-edit-2">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                    </svg>
                </a>
                <form action="{{ route('admin.deletar', $usuario->id) }}" method="POST" style="display: inline-block;"
                    id="form_delete_usuario_{{ $index }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" title="Apagar" class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete"
                        data-form-id="form_delete_usuario_{{ $index }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                            <line x1="10" y1="11" x2="10" y2="17">
                            </line>
                            <line x1="14" y1="11" x2="14" y2="17">
                            </line>
                        </svg>

                    </button>
                </form>
            </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>
    <script>
        $('.btn-confirm-delete').on('click', function() {
            const formId = $(this).data('form-id')
            swal({
                title: 'Deseja realmente apagar os registros deste usuario?',
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
        })
    </script>
@endsection
