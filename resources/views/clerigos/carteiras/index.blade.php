@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Clérigos', 'url' => route('clerigos.index'), 'active' => false],
    ['text' => 'Impressão de Carteiras', 'url' => '#', 'active' => true],
]" />
@endsection

@section('content')
@include('extras.alerts')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>{{ __('Impressão de Carteiras de Clérigos') }}</h4></div>
        <div class="widget-content widget-content-area">
            <p>{{ __('Região logada') }}: <strong>{{ $regiao->nome }}</strong></p>
            <p>{{ __('Selecione os clérigos para gerar o PDF. Serão impressas quatro carteiras por folha A4, no modelo da carteira digital, com QR code de validação.') }}</p>
            <p class="text-muted">{{ __('Ao imprimir o PDF, use tamanho real (100%), sem ajustar à página.') }}</p>
            <form id="form-carteiras-clerigos" action="{{ route('clerigos.carteiras.pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="d-flex flex-wrap align-items-center mb-3">
                    <button id="imprimir-carteiras" type="submit" class="btn btn-primary mr-3 mb-2">{{ __('Gerar PDF dos selecionados') }}</button>
                    <span id="total-selecionados" class="mb-2" aria-live="polite">0 {{ __('selecionados') }}</span>
                </div>
                @error('clerigos')<p class="text-danger">{{ $message }}</p>@enderror
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead><tr>
                            <th><label class="mb-0"><input type="checkbox" id="selecionar-todos"> {{ __('Todos') }}</label></th>
                            <th>{{ __('Clérigo') }}</th><th>{{ __('Rol') }}</th><th>{{ __('Categoria') }}</th><th>{{ __('Situação ministerial') }}</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($clerigos as $clerigo)
                                <tr>
                                    <td><input type="checkbox" class="selecao-clerigo" name="clerigos[]" value="{{ $clerigo->id }}" aria-label="{{ __('Selecionar') }} {{ $clerigo->nome }}"></td>
                                    <td>{{ $clerigo->nome }}</td>
                                    <td>{{ $clerigo->rol ?: '-' }}</td>
                                    <td>{{ $clerigo->categoria ? mb_convert_case($clerigo->categoria, MB_CASE_TITLE, 'UTF-8') : '-' }}</td>
                                    <td>{{ $clerigo->situacao_nome ?: __('Não informada') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">{{ __('Nenhum clérigo disponível nesta região.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = Array.from(document.querySelectorAll('.selecao-clerigo'));
    const all = document.getElementById('selecionar-todos');
    const button = document.getElementById('imprimir-carteiras');
    function atualizar() {
        const count = items.filter(item => item.checked).length;
        document.getElementById('total-selecionados').textContent = count + ' {{ __('selecionados') }}';
        all.checked = items.length > 0 && count === items.length;
        all.indeterminate = count > 0 && count < items.length;
        button.disabled = count === 0;
    }
    all.addEventListener('change', function () { items.forEach(item => { item.checked = all.checked; }); atualizar(); });
    items.forEach(item => item.addEventListener('change', atualizar));
    document.getElementById('form-carteiras-clerigos').addEventListener('submit', function (event) {
        if (!items.some(item => item.checked)) { event.preventDefault(); }
    });
    atualizar();
});
</script>
@endsection
