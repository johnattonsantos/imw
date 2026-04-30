@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'EBD', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Módulo EBD</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="row">
                    @php
                        $cards = [
                            ['titulo' => 'Liderança', 'total' => $totalLiderancas, 'rota' => route('ebd.liderancas.index')],
                            ['titulo' => 'Professores', 'total' => $totalProfessores, 'rota' => route('ebd.professores.index')],
                            ['titulo' => 'Alunos', 'total' => $totalAlunos, 'rota' => route('ebd.alunos.index')],
                            ['titulo' => 'Classes', 'total' => $totalClasses, 'rota' => route('ebd.classes.index')],
                            ['titulo' => 'Turmas', 'total' => $totalTurmas, 'rota' => route('ebd.turmas.index')],
                            ['titulo' => 'Diários', 'total' => $totalDiarios, 'rota' => route('ebd.diarios.index')],
                            ['titulo' => 'Agenda', 'total' => $totalAgendas, 'rota' => route('ebd.agendas.index')],
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
