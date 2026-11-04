@php
    $totalQuantidade = $lancamentos->sum('total');
    $baseTotal = $lancamentos->sum('base_inicial');
    $percentualTotal = $baseTotal > 0 ? $totalQuantidade * 100 / $baseTotal : ($totalQuantidade == 0 ? 0 : null);
@endphp
<p class="text-muted">Crescimento absoluto = membros ativos no fim − membros ativos no início. Crescimento percentual = (crescimento absoluto ÷ membros ativos no início) × 100. Exemplo: de 1 para 2 membros = +1 membro e 100% de crescimento.</p>
<table class="table table-striped display nowrap" id="{{ $tableId }}" style="font-size: 90%; margin-top: 15px; width: 100%;">
    <thead class="thead-dark">
        <tr>
            <th>{{ $instituicaoLabel }}</th>
            <th class="text-center">Membros ativos no início</th>
            <th class="text-center">Crescimento absoluto</th>
            <th class="text-center">Crescimento percentual</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lancamentos as $lancamento)
            <tr>
                <td>{{ $lancamento->nome }}</td>
                <td class="text-center" data-order="{{ $lancamento->base_inicial }}">{{ number_format($lancamento->base_inicial, 0, ',', '.') }}</td>
                <td class="text-center" data-order="{{ $lancamento->total }}">{{ number_format($lancamento->total, 0, ',', '.') }}</td>
                <td class="text-center" data-order="{{ $lancamento->percentual ?? -101 }}">
                    {{ $lancamento->percentual === null ? 'Sem base inicial' : number_format($lancamento->percentual, 2, ',', '.').'%' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total dos resultados</th>
            <th class="text-center">{{ number_format($baseTotal, 0, ',', '.') }}</th>
            <th class="text-center">{{ number_format($totalQuantidade, 0, ',', '.') }}</th>
            <th class="text-center">{{ $percentualTotal === null ? 'Sem base inicial' : number_format($percentualTotal, 2, ',', '.').'%' }}</th>
        </tr>
    </tfoot>
</table>
<small class="text-muted">Quando não há membros ativos no início e há no fim, o percentual não é calculável. Esses registros ficam depois dos percentuais calculáveis na ordenação.</small>
