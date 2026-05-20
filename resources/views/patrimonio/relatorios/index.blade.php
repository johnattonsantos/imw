@extends('template.layout')

@section('content')
    @include('extras.alerts')

    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-12">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3"><h4 class="mb-0">Relatórios Patrimoniais</h4></div>
                    <div class="widget-content widget-content-area">
                        <form method="GET" action="{{ route('patrimonio.relatorios.index') }}">
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label>Relatório *</label>
                                    <select name="relatorio" class="form-control" required>
                                        @foreach ($reportOptions as $key => $label)
                                            <option value="{{ $key }}" {{ ($filters['relatorio'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label>Igreja/Unidade</label>
                                    <select name="igreja_id" class="form-control">
                                        <option value="">Todas</option>
                                        @foreach ($igrejas as $igreja)
                                            <option value="{{ $igreja->id }}" {{ (string) ($filters['igreja_id'] ?? '') === (string) $igreja->id ? 'selected' : '' }}>{{ $igreja->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label>Categoria</label>
                                    <select name="categoria" class="form-control">
                                        <option value="">Todas</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria }}" {{ (string) ($filters['categoria'] ?? '') === (string) $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach ($statusOptions as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}" {{ (string) ($filters['status'] ?? '') === (string) $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label>Período inicial</label>
                                    <input type="date" name="periodo_inicio" class="form-control" value="{{ $filters['periodo_inicio'] ?? '' }}">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label>Período final</label>
                                    <input type="date" name="periodo_fim" class="form-control" value="{{ $filters['periodo_fim'] ?? '' }}">
                                </div>
                            </div>

                            <div class="d-flex" style="gap:.5rem;">
                                <button class="btn btn-primary">Aplicar filtros</button>
                                <a href="{{ route('patrimonio.relatorios.index') }}" class="btn btn-secondary">Limpar</a>
                                <a href="{{ route('patrimonio.relatorios.export.xlsx', request()->query()) }}" class="btn btn-success">Exportar Excel</a>
                                <a href="{{ route('patrimonio.relatorios.export.pdf', request()->query()) }}" class="btn btn-danger">Exportar PDF</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $report['title'] }}</h5>
                        <span class="badge badge-info">{{ number_format(count($report['rows']), 0, ',', '.') }} registro(s)</span>
                    </div>
                    <div class="widget-content widget-content-area">
                        <small class="text-muted d-block mb-2">Filtros são aplicados conforme os campos disponíveis em cada relatório.</small>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        @foreach ($report['headings'] as $heading)
                                            <th>{{ $heading }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($report['rows'] as $row)
                                        <tr>
                                            @foreach ($row as $cell)
                                                <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($report['headings']) }}" class="text-center">Nenhum dado encontrado para os filtros informados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
