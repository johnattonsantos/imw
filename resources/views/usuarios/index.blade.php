@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Usuários', 'url' => '', 'active' => true],
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

@php
    if (!function_exists('formatarCpf')) {
        function formatarCpf($cpf)
        {
            // Remove todos os caracteres não numéricos do CPF
            $cpf = preg_replace('/\D/', '', $cpf);
            // Formata o CPF no padrão ###.###.###-##
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }
    }

@endphp


@section('content')
<div class="container-fluid">
    <a href="{{ route('usuarios.novo') }}" class="btn btn-primary position-relative mt-3 mb-3 ml-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-plus-circle">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        <span class="ml-2">{{ __('INCLUIR USUÁRIO') }}</span>
    </a>
</div>
<!-- TABELA -->
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Lista de Usuários - {{ session('session_perfil')->instituicao_nome }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form>
                <div class="row mb-4">
                    <div class="col-4">
                        <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                            placeholder="{{ __('Pesquisar...') }}">
                    </div>
                    <div class="col-auto" style="margin-left: -19px;">
                        <button type="submit" class="btn btn-primary btn-rounded"><x-bx-search /> {{ __('Pesquisar') }}</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>{{ __('E-MAIL') }}</th>
                            <th>{{ __('NOME') }}</th>
                            <th>{{ __('TELEFONE') }}</th>
                            <th>{{ __('CPF') }}</th>
                            <th>{{ __('NÍVEL DE ACESSO') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $index => $usuario)
                                                <tr>
                                                    <td>{{ $usuario->email }}</td>
                                                    <td>{{ $usuario->name }}</td>
                                                    <td>
                                                        @if (!empty($usuario->telefone))
                                                            <a href="https://api.whatsapp.com/send?phone={{ $usuario->telefone }}" target="_blank">
                                                                <i class="fab fa-whatsapp"></i> {{ formatarTelefone($usuario->telefone) }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ formatarCpf($usuario->cpf) }}</td>
                                                    <td>
                                                        @php
                                                            $perfis = $usuario->perfilUser;
                                                        @endphp
                                                        @foreach ($perfis as $perfilUser)
                                                            @if ($perfilUser->instituicao_id == session()->get('session_perfil')->instituicao_id)
                                                                {{ $perfilUser->perfil->nome }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('usuarios.editar', $usuario->id) }}" title="{{ __('Editar') }}"
                                                            class="btn btn-sm btn-dark mr-2 btn-rounded">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" class="feather feather-edit-2">
                                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('usuarios.deletar', $usuario->id) }}" method="POST"
                                                            style="display: inline-block;" id="form_delete_usuario_{{ $index }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                title="Remover o vínculo deste usuário na {{ session('session_perfil')->instituicao_nome }}"
                                                                class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete"
                                                                data-form-id="form_delete_usuario_{{ $index }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-unlink">
                                                                    <line x1="10" y1="15" x2="21" y2="4">
                                                                    </line>
                                                                    <line x1="15" y1="4" x2="21" y2="4">
                                                                    </line>
                                                                    <line x1="4" y1="15" x2="4" y2="21">
                                                                    </line>
                                                                    <line x1="4" y1="19" x2="9" y2="19">
                                                                    </line>
                                                                </svg>

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
    $('.btn-confirm-delete').on('click', function () {
        const formId = $(this).data('form-id')
        swal({
            title: 'Deseja realmente remover o vínculo deste usuário nesta instituição?',
            type: 'error',
            showCancelButton: true,
            confirmButtonText: "Remover Vínculo",
            confirmButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            cancelButtonColor: "#3085d6",
            padding: '2em'
        }).then(function (result) {
            if (result.value) document.getElementById(formId).submit()
        })
    })
</script>
@endsection