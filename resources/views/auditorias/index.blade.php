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

@section('extras-css')
<link href="{{ asset('theme/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container {
        width: 100% !important;
    }

    .auditorias-filtros .select2-container--default .select2-selection--single {
        height: 43px;
        border: 1px solid #bfc9d4;
        border-radius: 4px;
    }

    .auditorias-filtros .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 41px;
        padding-left: 20px;
        padding-right: 36px;
    }

    .auditorias-filtros .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 41px;
        right: 8px;
    }

    .auditorias-filtros .select2-container--default .select2-selection--single .select2-selection__clear {
        height: 41px;
        line-height: 41px;
        margin-right: 12px;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Auditorias do Sistema') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('auditorias.index') }}" class="mb-4 auditorias-filtros">
                <div class="row align-items-start">
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <label for="user_id">{{ __('Usuario') }}</label>
                        <select name="user_id" id="user_id" class="form-control form-control-sm select2-auditoria" data-placeholder="{{ __('Todos') }}">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2 mb-3">
                        <label for="instituicao_id">{{ __('Instituicao') }}</label>
                        <select name="instituicao_id" id="instituicao_id" class="form-control form-control-sm select2-auditoria" data-placeholder="{{ __('Todas') }}">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach($instituicoes as $instituicao)
                                <option value="{{ $instituicao->id }}" @selected((string) request('instituicao_id') === (string) $instituicao->id)>
                                    {{ $instituicao->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                        <label for="event">{{ __('Evento') }}</label>
                        @php
                            $eventLabels = [
                                'login' => 'Login',
                                'login_failed' => 'Login Falho',
                                'logout' => 'Logout',
                                'created' => 'Criado',
                                'updated' => 'Atualizado',
                                'deleted' => 'Deletado',
                                'CREATED' => 'Criado',
                                'UPDATED' => 'Atualizado',
                                'DELETED' => 'Deletado',
                            ];
                        @endphp
                        <select name="event" id="event" class="form-control form-control-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}" @selected(request('event') === $event)>
                                    {{ $eventLabels[$event] ?? strtoupper($event) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-1 mb-3">
                        <label for="auditable_type">{{ __('Entidade') }}</label>
                        <select name="auditable_type" id="auditable_type" class="form-control form-control-sm">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach($entidades as $entidade)
                                <option value="{{ $entidade }}" @selected(request('auditable_type') === $entidade)>
                                    {{ class_basename($entidade) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2 mb-3">
                        <label for="periodo_inicio">{{ __('Periodo inicio') }}</label>
                        <input type="date" name="periodo_inicio" id="periodo_inicio" class="form-control form-control-sm"
                            value="{{ request('periodo_inicio') }}">
                    </div>

                    <div class="col-12 col-md-6 col-lg-2 mb-3">
                        <label for="periodo_fim">{{ __('Periodo fim') }}</label>
                        <input type="date" name="periodo_fim" id="periodo_fim" class="form-control form-control-sm"
                            value="{{ request('periodo_fim') }}">
                    </div>
                </div>

                <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm ">{{ __('Filtrar') }}</button>
                    <a href="{{ route('auditorias.index') }}" class="btn btn-light btn-sm">{{ __('Limpar filtros') }}</a>
                    <a href="{{ route('auditorias.export.xlsx', request()->query()) }}" class="btn btn-success btn-sm">{{ __('Exportar XLSX') }}</a>
                    <a href="{{ route('auditorias.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">{{ __('Exportar PDF') }}</a>
                </div>
            </form>

            <div class="mb-3">
                <strong>{{ __('Total:') }}</strong> {{ $audits->total() }} registro(s)
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>{{ __('Data/Hora') }}</th>
                            <th>{{ __('Usuario') }}</th>
                            <th>{{ __('Instituicao') }}</th>
                            <th>{{ __('Evento') }}</th>
                            <th>{{ __('Entidade') }}</th>
                            <th>{{ __('Registro') }}</th>
                            <th>{{ __('IP') }}</th>
                            <th>{{ __('Detalhes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            @php
                                $oldValues = is_array($audit->old_values) ? $audit->old_values : (json_decode($audit->old_values ?? '', true) ?: []);
                                $newValues = is_array($audit->new_values) ? $audit->new_values : (json_decode($audit->new_values ?? '', true) ?: []);
                                $auditInstituicaoId = $audit->instituicao_id ?? ($newValues['instituicao_id'] ?? ($oldValues['instituicao_id'] ?? null));
                            @endphp
                            <tr>
                                <td>{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    {{ optional($audit->user)->name ?? 'Sistema' }}
                                    @if(optional($audit->user)->email)
                                        <br><small class="text-muted">{{ $audit->user->email }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($auditInstituicaoId)
                                        {{ $instituicaoMap[$auditInstituicaoId] ?? 'Instituicao nao encontrada' }}
                                        <br><small class="text-muted">#{{ $auditInstituicaoId }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="badge badge-info">{{ strtoupper($audit->event) }}</span></td>
                                <td>{{ class_basename($audit->auditable_type) }}</td>
                                <td>#{{ $audit->auditable_id }}</td>
                                <td>{{ $audit->ip_address ?: '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse"
                                        data-target="#audit-{{ $audit->id }}" aria-expanded="false"
                                        aria-controls="audit-{{ $audit->id }}">
                                        {{ __('Ver') }}
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" class="p-0 border-top-0">
                                    <div class="collapse" id="audit-{{ $audit->id }}">
                                        <div class="p-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>{{ __('Antes') }}</h6>
                                                    <pre class="mb-0" style="max-height: 240px; overflow:auto;">{{ json_encode($oldValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>{{ __('Depois') }}</h6>
                                                    <pre class="mb-0" style="max-height: 240px; overflow:auto;">{{ json_encode($newValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('Nenhum registro encontrado para os filtros informados.') }}</td>
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

@section('extras-scripts')
<script src="{{ asset('theme/plugins/select2/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.select2-auditoria').select2({
            allowClear: true,
            width: '100%',
            placeholder: function () {
                return $(this).data('placeholder');
            }
        });
    });
</script>
@endsection
