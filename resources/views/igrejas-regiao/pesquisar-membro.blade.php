@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Igrejas', 'url' => route('igrejas.regiao.index'), 'active' => false],
    ['text' => 'Pesquisar Membro', 'url' => route('igrejas.regiao.pesquisar-membro'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
@php
    $formatCpf = function ($cpf) {
        $digits = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digits) !== 11) {
            return $cpf ?: '-';
        }

        return substr($digits, 0, 3) . '.' . substr($digits, 3, 3) . '.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
    };

    $formatStatus = fn ($status) => $status === 'A' ? __('Ativo') : ($status === 'I' ? __('Inativo') : ($status ?: '-'));
@endphp

@include('extras.alerts')

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Pesquisar Membro') }}</h4>
                    <p class="pl-3 mb-0">{{ __('Busque membros da região logada por nome ou CPF.') }}</p>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('igrejas.regiao.pesquisar-membro') }}" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-lg-5 col-md-5 mb-2">
                        <label for="nome">{{ __('Nome') }}</label>
                        <input type="text" name="nome" id="nome" value="{{ $nome }}" class="form-control" placeholder="{{ __('Nome ou parte do nome') }}">
                    </div>
                    <div class="col-lg-3 col-md-3 mb-2">
                        <label for="cpf">{{ __('CPF') }}</label>
                        <input type="text" name="cpf" id="cpf" value="{{ $cpf }}" class="form-control" placeholder="{{ __('Digite o CPF') }}" maxlength="14" autocomplete="off">
                    </div>
                    <div class="col-lg-4 col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary btn-rounded">
                            <x-bx-search /> {{ __('Pesquisar') }}
                        </button>
                        <a href="{{ route('igrejas.regiao.pesquisar-membro') }}" class="btn btn-light btn-rounded">
                            {{ __('Limpar') }}
                        </a>
                    </div>
                </div>
            </form>

            @if ($searched)
                <p>{{ __('Registros encontrados') }}: {{ $membros->count() }}</p>
            @else
                <div class="alert alert-info">
                    {{ __('Informe um nome ou CPF para realizar a pesquisa.') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4">
                    <thead>
                        <tr>
                            <th>{{ __('Nome do Membro') }}</th>
                            <th>{{ __('CPF') }}</th>
                            <th>{{ __('Telefone') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Distrito') }}</th>
                            <th>{{ __('Igreja') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($membros as $membro)
                            <tr>
                                <td>{{ $membro->nome }}</td>
                                <td>{{ $formatCpf($membro->cpf) }}</td>
                                <td>{{ $membro->telefone ?: '-' }}</td>
                                <td>{{ $formatStatus($membro->status) }}</td>
                                <td>{{ $membro->distrito_nome ?: '-' }}</td>
                                <td>{{ $membro->igreja_nome ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    {{ $searched ? __('Nenhum registro encontrado') : __('Realize uma pesquisa para listar os membros.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf');

        if (!cpfInput) {
            return;
        }

        const applyCpfMask = function (value) {
            return value
                .replace(/\D/g, '')
                .slice(0, 11)
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        };

        cpfInput.value = applyCpfMask(cpfInput.value);
        cpfInput.addEventListener('input', function () {
            cpfInput.value = applyCpfMask(cpfInput.value);
        });
    });
</script>
@endsection
