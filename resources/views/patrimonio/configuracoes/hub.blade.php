@extends('template.layout')

@section('content')
@include('extras.alerts')
@php
    $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar');
@endphp

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-12 mb-3">
            <h4 class="mb-1">Configurações Patrimoniais</h4>
            <p class="text-muted mb-0">Cadastros auxiliares globais do módulo de patrimônio (válidos para todas as igrejas/unidades).</p>
        </div>
        <div class="col-12">
            @include('patrimonio.configuracoes._menu-tipos')
        </div>

        @foreach ($tipos as $tipo => $label)
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">{{ $label }}</h5>
                        <p class="text-muted mb-3">Total cadastrado: {{ (int) ($counts[$tipo] ?? 0) }}</p>
                        <div class="d-flex" style="gap:.5rem;">
                            <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo]) }}" class="btn btn-sm btn-outline-primary">Abrir</a>
                            @if ($podeCriar)
                                <a href="{{ route('patrimonio.configuracoes.tipos.create', ['tipo' => $tipo]) }}" class="btn btn-sm btn-primary">Novo</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
