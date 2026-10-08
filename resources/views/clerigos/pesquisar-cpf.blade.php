@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Clérigos', 'url' => route('clerigos.index'), 'active' => false],
    ['text' => 'Pesquisar Clérigo', 'url' => '#', 'active' => true],
]" />
@endsection

@section('content')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>{{ __('Pesquisar Clérigo') }}</h4></div>
        <div class="widget-content widget-content-area">
            <p>{{ __('Região logada') }}: <strong>{{ $regiao->nome }}</strong></p>
            <p>{{ __('Informe o CPF completo para localizar o clérigo e conferir cadastros com o mesmo CPF em outras regiões. A pesquisa inclui cadastros inativados.') }}</p>
            <form method="GET" action="{{ route('clerigos.pesquisar-cpf') }}" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label for="cpf">CPF</label>
                        <input id="cpf" name="cpf" type="text" inputmode="numeric" maxlength="14" value="{{ old('cpf', $cpf) }}" class="form-control @error('cpf') is-invalid @enderror" autocomplete="off" placeholder="000.000.000-00" required>
                        @error('cpf')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-7 mb-2">
                        <button type="submit" class="btn btn-primary btn-rounded">{{ __('Pesquisar') }}</button>
                        <a href="{{ route('clerigos.pesquisar-cpf') }}" class="btn btn-light btn-rounded">{{ __('Limpar') }}</a>
                    </div>
                </div>
            </form>
            @if ($searched)
                @if ($multiplasRegioes)
                    <div class="alert alert-warning">{{ __('O mesmo CPF possui cadastros em mais de uma região. Confira os registros abaixo, inclusive os inativados, antes de concluir que há duplicidade.') }}</div>
                @endif
                @if ($duplicadoNaRegiao)
                    <div class="alert alert-warning">{{ __('Existe mais de um cadastro com este CPF na região logada.') }}</div>
                @endif
                @foreach (['Região logada' => $daRegiao, 'Outras regiões' => $outrasRegioes] as $titulo => $registros)
                    <h5>{{ __($titulo) }} ({{ $registros->count() }})</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>{{ __('Nome') }}</th><th>CPF</th><th>{{ __('Região') }}</th><th>{{ __('Cadastro') }}</th><th>{{ __('Situação ministerial') }}</th></tr></thead>
                            <tbody>
                                @forelse ($registros as $clerigo)
                                    <tr>
                                        <td>{{ $clerigo->nome }}</td>
                                        <td>{{ substr($cpf, 0, 3) }}.{{ substr($cpf, 3, 3) }}.{{ substr($cpf, 6, 3) }}-{{ substr($cpf, 9, 2) }}</td>
                                        <td>{{ $clerigo->regiao_nome ?: __('Não informada') }}</td>
                                        <td>{{ $clerigo->deleted_at ? __('Inativado') : __('Ativo') }}</td>
                                        <td>{{ $clerigo->situacao_nome ?: __('Não informada') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">{{ __('Nenhum cadastro encontrado.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">{{ __('Informe um CPF para realizar a pesquisa.') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('cpf');
        const mask = function (value) {
            return value.replace(/\D/g, '').slice(0, 11)
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        };
        input.value = mask(input.value);
        input.addEventListener('input', function () { input.value = mask(input.value); });
    });
</script>
@endsection
