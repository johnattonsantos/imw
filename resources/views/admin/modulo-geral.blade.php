@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Segurança', 'url' => '/', 'active' => false],
        ['text' => 'Módulo Geral', 'url' => '/admin/modulo-geral', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="container-fluid">
        @php
            $queryParams = request()->query();
        @endphp
        <div class="widget-header mb-3 d-flex flex-wrap align-items-start justify-content-between">
            <div>
                <h4>Módulo Geral Multi-Regional</h4>
                <p class="mb-0">Visão consolidada de usuários, instituições, clérigos e nomeações.</p>
                <small class="text-muted">Período de auditoria: {{ $periodoResumo }}</small>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="{{ route('admin.modulo-geral.export.xlsx', $queryParams) }}" class="btn btn-success btn-sm mr-2">
                    Exportar XLSX
                </a>
                <a href="{{ route('admin.modulo-geral.export.pdf', $queryParams) }}" class="btn btn-danger btn-sm">
                    Exportar PDF
                </a>
            </div>
        </div>

        <div class="statbox widget box box-shadow mb-4">
            <div class="widget-header p-3">
                <h5 class="mb-0">Filtros</h5>
            </div>
            <div class="widget-content p-3">
                <form method="GET" action="{{ route('admin.modulo-geral') }}">
                    <div class="row">
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="mb-1">Região</label>
                            <select name="regiao_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($regioes as $regiao)
                                    <option value="{{ $regiao->id }}" {{ (int) ($filtros['regiao_id'] ?? 0) === (int) $regiao->id ? 'selected' : '' }}>
                                        {{ $regiao->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="mb-1">Distrito</label>
                            <select name="distrito_id" class="form-control" {{ empty($filtros['regiao_id']) ? 'disabled' : '' }}>
                                <option value="">Todos</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}" {{ (int) ($filtros['distrito_id'] ?? 0) === (int) $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="mb-1">Igreja</label>
                            <select name="igreja_id" class="form-control" {{ empty($filtros['regiao_id']) ? 'disabled' : '' }}>
                                <option value="">Todas</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" {{ (int) ($filtros['igreja_id'] ?? 0) === (int) $igreja->id ? 'selected' : '' }}>
                                        {{ $igreja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="mb-1">Período início</label>
                            <input type="date" name="periodo_inicio" class="form-control" value="{{ $filtros['periodo_inicio'] ?? '' }}">
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
                            <label class="mb-1">Período fim</label>
                            <input type="date" name="periodo_fim" class="form-control" value="{{ $filtros['periodo_fim'] ?? '' }}">
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">Aplicar</button>
                            <a href="{{ route('admin.modulo-geral') }}" class="btn btn-outline-secondary">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Total de Usuários</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalUsuarios }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Total de Instituições</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalInstituicoes }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Total de Clérigos</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalClerigos }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Nomeações Ativas</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalNomeacoesAtivas }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Usuários Admin Sistema</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalUsuariosAdminSistema }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Usuários CRIE</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalUsuariosCrie }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Usuários Sem Região</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalUsuariosSemRegiao }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Auditorias no Período</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalAuditoriasPeriodo }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Auditorias Hoje</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalAuditoriasHoje }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h6 class="mb-0">Login Falho (Período)</h6>
                    </div>
                    <div class="widget-content p-3">
                        <h3 class="mb-0">{{ $totalAuditoriasLoginFalho }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Usuários por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuariosPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Instituições Ativas por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($instituicoesPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Clérigos por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clerigosPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Nomeações Ativas por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nomeacoesAtivasPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Perfis Estratégicos por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Admin Sistema</th>
                                    <th>CRIE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($perfisEstrategicosPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total_admin_sistema }}</td>
                                        <td>{{ $item->total_crie }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Auditorias por Região</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Região</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditoriasPorRegiao as $item)
                                    <tr>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Auditorias por Evento</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditoriasPorEvento as $item)
                                    <tr>
                                        <td>{{ $item->evento }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Top Usuários em Auditoria</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditoriasPorUsuario as $item)
                                    <tr>
                                        <td>{{ $item->usuario_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Top 25 Nomeações Ativas por Instituição</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Instituição</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nomeacoesPorInstituicao as $item)
                                    <tr>
                                        <td>{{ $item->instituicao_nome }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 mb-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header p-3">
                        <h5 class="mb-0">Últimos Eventos de Auditoria (30)</h5>
                    </div>
                    <div class="widget-content p-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Usuário</th>
                                    <th>Evento</th>
                                    <th>Entidade</th>
                                    <th>Registro</th>
                                    <th>Instituição</th>
                                    <th>Região</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditoriasRecentes as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $item->usuario_nome }}</td>
                                        <td>{{ strtoupper($item->event ?? '-') }}</td>
                                        <td>{{ class_basename((string) $item->auditable_type) }}</td>
                                        <td>{{ $item->auditable_id }}</td>
                                        <td>{{ $item->instituicao_nome }}</td>
                                        <td>{{ $item->regiao_nome }}</td>
                                        <td>{{ $item->ip_address ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">Sem dados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
