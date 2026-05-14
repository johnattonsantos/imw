@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
        ['text' => 'EBD - Dashboard', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <style>
        .ebd-filtros .row { row-gap: 8px; }
        .ebd-filtros .filtro-acoes {
            display: flex;
            gap: 8px;
            align-items: flex-end;
            height: 100%;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Dashboard EBD (Distrital)</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" class="form-vertical ebd-filtros mb-4">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <label class="control-label">Distrito:</label>
                            <select name="distrito_id" id="distrito_id" class="form-control">
                                <option value="">Todos</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}" {{ (string) ($filters['distrito_id'] ?? '') === (string) $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="control-label">Igreja:</label>
                            <select name="igreja_id" id="igreja_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" {{ (string) ($filters['igreja_id'] ?? '') === (string) $igreja->id ? 'selected' : '' }}>
                                        {{ $igreja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-12 filtro-acoes">
                            <button type="submit" class="btn btn-primary"><x-bx-search /> Aplicar</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="row">
                    @php
                        $cards = [
                            ['titulo' => 'Liderança', 'total' => $totalLiderancas, 'rota' => route('distrito.relatorio.ebd.liderancas')],
                            ['titulo' => 'Professores', 'total' => $totalProfessores, 'rota' => route('distrito.relatorio.ebd.professores')],
                            ['titulo' => 'Alunos', 'total' => $totalAlunos, 'rota' => route('distrito.relatorio.ebd.alunos')],
                            ['titulo' => 'Classes', 'total' => $totalClasses, 'rota' => route('distrito.relatorio.ebd.classes')],
                            ['titulo' => 'EBDs', 'total' => $totalTurmas, 'rota' => route('distrito.relatorio.ebd.turmas')],
                            ['titulo' => 'Diários', 'total' => $totalDiarios, 'rota' => route('distrito.relatorio.ebd.diarios')],
                            ['titulo' => 'Agenda', 'total' => $totalAgendas, 'rota' => route('distrito.relatorio.ebd.agendas')],
                            ['titulo' => 'Relatório Geral', 'total' => $totalAlunos, 'rota' => route('distrito.relatorio.ebd.geral')],
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

@section('extras-scripts')
    <script>
        const distritoSelect = document.getElementById('distrito_id');
        const igrejaSelect = document.getElementById('igreja_id');
        const todasIgrejasHtml = igrejaSelect ? igrejaSelect.innerHTML : '';

        distritoSelect?.addEventListener('change', function() {
            const distritoId = this.value;
            const igrejaSelecionadaAtual = igrejaSelect.value;

            if (!distritoId) {
                igrejaSelect.innerHTML = todasIgrejasHtml;
                igrejaSelect.value = '';
                return;
            }

            igrejaSelect.innerHTML = '<option value="">Carregando...</option>';

            fetch(`/instituicoes/igrejasByDistrito/${distritoId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.ok ? response.json() : Promise.reject())
            .then(igrejas => {
                let options = '<option value="">Todas</option>';
                igrejas.forEach(igreja => {
                    const selected = String(igreja.id) === String(igrejaSelecionadaAtual) ? 'selected' : '';
                    options += `<option value="${igreja.id}" ${selected}>${igreja.nome}</option>`;
                });
                igrejaSelect.innerHTML = options;
            })
            .catch(() => {
                igrejaSelect.innerHTML = '<option value="">Todas</option>';
            });
        });
    </script>
@endsection
