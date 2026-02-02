@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Auditorias', 'url' => '', 'active' => true],
    ]">
</x-breadcrumb>
@endsection

@include('extras.alerts')
@include('extras.alerts-error-all')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Auditorias do Sistema</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('auditorias.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="user_id">Usuario</label>
                        <select name="user_id" id="user_id" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="event">Evento</label>
                        <select name="event" id="event" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}" @selected(request('event') === $event)>
                                    {{ strtoupper($event) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="auditable_type">Entidade</label>
                        <select name="auditable_type" id="auditable_type" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            @foreach($entidades as $entidade)
                                <option value="{{ $entidade }}" @selected(request('auditable_type') === $entidade)>
                                    {{ class_basename($entidade) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="periodo_inicio">Periodo inicio</label>
                        <input type="date" name="periodo_inicio" id="periodo_inicio" class="form-control form-control-sm"
                            value="{{ request('periodo_inicio') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="periodo_fim">Periodo fim</label>
                        <input type="date" name="periodo_fim" id="periodo_fim" class="form-control form-control-sm"
                            value="{{ request('periodo_fim') }}">
                    </div>
                </div>

                <div class="mt-2 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm ">Filtrar</button>
                    <a href="{{ route('auditorias.index') }}" class="btn btn-light btn-sm">Limpar filtros</a>
                    <a href="{{ route('auditorias.export.xlsx', request()->query()) }}" class="btn btn-success btn-sm">Exportar XLSX</a>
                    <a href="{{ route('auditorias.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">Exportar PDF</a>
                </div>
            </form>

            <div class="mb-3">
                <strong>Total:</strong> {{ $audits->total() }} registro(s)
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuario</th>
                            <th>Evento</th>
                            <th>Entidade</th>
                            <th>Registro</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            @php
                                $oldValues = is_array($audit->old_values) ? $audit->old_values : (json_decode($audit->old_values ?? '', true) ?: []);
                                $newValues = is_array($audit->new_values) ? $audit->new_values : (json_decode($audit->new_values ?? '', true) ?: []);
                            @endphp
                            <tr>
                                <td>{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    {{ optional($audit->user)->name ?? 'Sistema' }}
                                    @if(optional($audit->user)->email)
                                        <br><small class="text-muted">{{ $audit->user->email }}</small>
                                    @endif
                                </td>
                                <td><span class="badge badge-info">{{ strtoupper($audit->event) }}</span></td>
                                <td>{{ class_basename($audit->auditable_type) }}</td>
                                <td>#{{ $audit->auditable_id }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse"
                                        data-target="#audit-{{ $audit->id }}" aria-expanded="false"
                                        aria-controls="audit-{{ $audit->id }}">
                                        Ver
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="p-0 border-top-0">
                                    <div class="collapse" id="audit-{{ $audit->id }}">
                                        <div class="p-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Antes</h6>
                                                    <pre class="mb-0" style="max-height: 240px; overflow:auto;">{{ json_encode($oldValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Depois</h6>
                                                    <pre class="mb-0" style="max-height: 240px; overflow:auto;">{{ json_encode($newValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum registro encontrado para os filtros informados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $audits->links('vendor.pagination.index') }}
            </div>
        </div>
    </div>
</div>
@endsection
