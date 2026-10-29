@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'GCEU', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Módulo GCEU') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="row">
                    @php
                        $cards = [
                            ['titulo' => 'GCEUs', 'total' => $totalGceus],
                            ['titulo' => 'Membros', 'total' => $totalMembros],
                            ['titulo' => 'Cartas Pastorais', 'total' => $totalCartasPastorais],
                            ['titulo' => 'Diários', 'total' => $totalDiarios],
                            ['titulo' => 'Cadastros de Reunião', 'total' => $totalReuniaoPessoas],
                        ];
                    @endphp

                    @foreach ($cards as $card)
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-uppercase text-muted mb-2">{{ $card['titulo'] }}</h6>
                                    <h2 class="mb-0 text-dark">{{ $card['total'] }}</h2>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
