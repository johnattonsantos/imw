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
                        <h4>Módulo GCEU</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="row">
                    @php
                        $cards = [
                            ['titulo' => 'GCEUs', 'total' => $totalGceus, 'rota' => route('gceu.index')],
                            ['titulo' => 'Membros', 'total' => $totalMembros, 'rota' => route('gceu.membros')],
                            ['titulo' => 'Cartas Pastorais', 'total' => $totalCartasPastorais, 'rota' => route('gceu.carta-pastoral')],
                            ['titulo' => 'Diários', 'total' => $totalDiarios, 'rota' => route('gceu.diario')],
                            ['titulo' => 'Cadastros de Reunião', 'total' => $totalReuniaoPessoas, 'rota' => route('gceu.reuniao-pessoas')],
                        ];
                    @endphp

                    @foreach ($cards as $card)
                        <div class="col-md-6 col-xl-3 mb-4">
                            <a href="{{ $card['rota'] }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-muted mb-2">{{ $card['titulo'] }}</h6>
                                        <h2 class="mb-0 text-dark">{{ $card['total'] }}</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
