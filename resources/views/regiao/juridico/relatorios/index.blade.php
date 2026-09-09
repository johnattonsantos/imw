@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Jurídico Regional', 'url' => route('regiao.juridico.acoes.index'), 'active' => false],
    ['text' => 'Relatórios Jurídicos', 'url' => route('regiao.juridico.relatorios'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><div class="row"><div class="col-12"><h4>Relatórios Jurídicos</h4></div></div></div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Resultado</label>
                        <select name="resultado" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach ($resultadoOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('resultado') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Instituição</label>
                        <select name="instituicao_id" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            @foreach ($instituicoes as $instituicao)
                                <option value="{{ $instituicao->id }}" {{ (string) request('instituicao_id') === (string) $instituicao->id ? 'selected' : '' }}>{{ $instituicao->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>Advogado</label>
                        <select name="advogado_id" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach ($advogados as $advogado)
                                <option value="{{ $advogado->id }}" {{ (string) request('advogado_id') === (string) $advogado->id ? 'selected' : '' }}>{{ $advogado->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="row mb-4">
                <div class="col-md-2"><div class="alert alert-info mb-2"><strong>Total</strong><br>{{ $resumo->total }}</div></div>
                <div class="col-md-2"><div class="alert alert-warning mb-2"><strong>Em curso</strong><br>{{ $resumo->em_curso }}</div></div>
                <div class="col-md-2"><div class="alert alert-secondary mb-2"><strong>Transitada</strong><br>{{ $resumo->transitada_julgado }}</div></div>
                <div class="col-md-2"><div class="alert alert-success mb-2"><strong>Favor</strong><br>{{ $resumo->favor }}</div></div>
                <div class="col-md-2"><div class="alert alert-danger mb-2"><strong>Contra</strong><br>{{ $resumo->contra }}</div></div>
                <div class="col-md-2"><div class="alert alert-dark mb-2"><strong>Custo</strong><br>R$ {{ number_format($resumo->custo_total, 2, ',', '.') }}</div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Instituição</th>
                            <th>Autor</th>
                            <th>Ré</th>
                            <th>Advogado da Causa</th>
                            <th>Advogado da Oposição</th>
                            <th>Status</th>
                            <th>Resultado</th>
                            <th>Teor da Decisão</th>
                            <th>Custo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($acoes as $acao)
                            <tr>
                                <td>{{ $acao->numero_processo ?: '-' }}</td>
                                <td>{{ optional($acao->instituicao)->nome ?: '-' }}</td>
                                <td>{{ $acao->autor }}</td>
                                <td>{{ $acao->reu }}</td>
                                <td>{{ optional($acao->advogadoCausa)->nome ?: '-' }}</td>
                                <td>{{ optional($acao->advogadoOposicao)->nome ?: ($acao->advogado_oposicao_nome ?: '-') }}</td>
                                <td>{{ $statusOptions[$acao->status] ?? $acao->status }}</td>
                                <td>{{ $resultadoOptions[$acao->resultado] ?? $acao->resultado }}</td>
                                <td>{{ $acao->teor_decisao ?: '-' }}</td>
                                <td>{{ $acao->custo_demanda !== null ? 'R$ ' . number_format((float) $acao->custo_demanda, 2, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">Nenhum registro encontrado.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="9">TOTAL</th>
                            <th>R$ {{ number_format($resumo->custo_total, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
