@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
        ['text' => 'Membros Ativos e Inativos', 'url' => '#', 'active' => true],
    ]" />
@endsection

@section('content')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>Membros Ativos e Inativos — {{ $regiao->nome }}</h4></div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('regiao.estatistica.membrosAtivosInativos') }}">
                <div class="row align-items-end">
                    <div class="col-md-3 form-group">
                        <label for="nivel">Agrupar por</label>
                        <select id="nivel" name="nivel" class="form-control">
                            @foreach (['regiao' => 'Região', 'distrito' => 'Distrito', 'igreja' => 'Igreja'] as $valor => $nome)
                                <option value="{{ $valor }}" @selected($nivel === $valor)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="periodo">Período eclesiástico</label>
                        <select id="periodo" name="periodo" class="form-control">
                            @foreach ([1 => 'Anuênio', 2 => 'Biênio', 3 => '3 anos', 4 => '4 anos', 5 => '5 anos', 6 => 'Sexênio'] as $valor => $nome)
                                <option value="{{ $valor }}" @selected($periodo === $valor)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="referencia">Data de referência</label>
                        <input id="referencia" name="referencia" type="date" class="form-control" value="{{ $referencia->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        @error('referencia')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-3 form-group"><button class="btn btn-primary" type="submit">Gerar relatório</button></div>
                </div>
            </form>
            <p>Contagem no encerramento de cada ano eclesiástico (novembro a outubro). O ano em andamento vai até a data de referência. Cada membro é contado uma vez no agrupamento escolhido, conforme sua última recepção até aquela data.</p>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>{{ ['regiao' => 'Região', 'distrito' => 'Distrito', 'igreja' => 'Igreja'][$nivel] }}</th><th>Período</th><th class="text-right">Ativos</th><th class="text-right">Inativos</th><th class="text-right">Total</th><th class="text-right">Crescimento dos ativos (%)</th></tr></thead>
                    <tbody>
                        @forelse ($linhas as $linha)
                            <tr>
                                <td>{{ $linha['instituicao'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($linha['inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($linha['fim'])->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format($linha['ativos'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($linha['inativos'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($linha['ativos'] + $linha['inativos'], 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @if ($linha['crescimento_ativos'] === null)
                                        <span class="text-muted">Sem base inicial</span>
                                    @else
                                        <strong class="{{ $linha['crescimento_ativos'] > 0 ? 'text-success' : ($linha['crescimento_ativos'] < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ $linha['crescimento_ativos'] > 0 ? '+' : '' }}{{ number_format($linha['crescimento_ativos'], 2, ',', '.') }}%
                                        </strong>
                                    @endif
                                    <small class="d-block text-muted">{{ number_format($linha['ativos_iniciais'], 0, ',', '.') }} para {{ number_format($linha['ativos'], 0, ',', '.') }} ativos</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Nenhuma instituição encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-muted small">Crescimento dos ativos = (ativos finais − ativos iniciais) ÷ ativos iniciais × 100. Quando não há ativos no início e há no fim, o percentual não é calculável.</p>
        </div>
    </div>
</div>
@endsection
