@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
        ['text' => 'Estatísticas Total Membresia', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
<style>
    .toggle-icon {
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }
    .collapsed .toggle-icon {
        transform: rotate(180deg);
    }
    .hidden-row {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <h4>{{ __('Estatística Total de Membresia') }}</h4>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form" id="filter_form" method="GET">
                <div class="form-group row mb-4">
                    <div class="col-lg-3 d-flex align-items-center">
                        <select class="form-control" name="checkIgreja" id="checkIgreja">
                            <option value="distrito" {{ request()->input('checkIgreja') == 'distrito' ? 'selected' : '' }}>{{ __('Filtro por Distrito') }}</option>
                            <option value="igreja" {{ request()->input('checkIgreja') == 'igreja' ? 'selected' : '' }}>{{ __('Filtro por Igreja') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button id="btn_buscar" type="submit" class="btn btn-primary">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                    </div>
                </div>
            </form>

            @if (!empty($dados))
            <div class="table-responsive mt-4">
                <table class="table table-striped" style="font-size: 90%;">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: left;">{{ __('Nome') }}</th>
                            <th style="text-align: right;">{{ __('Total de Membros') }}</th>
                            @if ($tipo == 'igreja')
                                <th style="text-align: right;" width="150px">{{ __('% do Distrito') }}</th>
                            @endif
                            <th style="text-align: right;" width="150px">{{ __('% da Região') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $ultimoDistrito = null; @endphp

                        @foreach ($dados as $dado)
                            @if ($tipo == 'igreja')
                                @if ($ultimoDistrito !== $dado->distrito_id)
                                    @php
                                        $totalDistrito = $dados->where('distrito_id', $dado->distrito_id)->sum('total_membros');
                                    @endphp
                                    <tr class="distrito-row" data-distrito="{{ $dado->distrito_id }}">
                                        <td>
                                            <span class="toggle-icon" onclick="toggleDistrito({{ $dado->distrito_id }})">▼</span>
                                            <strong>{{ $dado->distrito_nome }}</strong>
                                        </td>
                                        <td style="text-align: right;"><strong>{{ $totalDistrito }}</strong></td>
                                        <td></td>
                                        <td style="text-align: right;">
                                            @if ($totalGeral > 0)
                                                <strong>{{ number_format(($totalDistrito / $totalGeral) * 100, 2, ',', '.') }}%</strong>
                                            @else
                                                0,00%
                                            @endif
                                        </td>
                                    </tr>
                                    @php $ultimoDistrito = $dado->distrito_id; @endphp
                                @endif
                                <tr class="igreja-row hidden-row" data-parent="{{ $dado->distrito_id }}">
                                    <td style="padding-left: 30px;">- {{ $dado->igreja_nome }}</td>
                                    <td style="text-align: right;">{{ $dado->total_membros }}</td>
                                    <td style="text-align: right;">
                                        @if ($totalDistrito > 0)
                                            {{ number_format(($dado->total_membros / $totalDistrito) * 100, 2, ',', '.') }}%
                                        @else
                                            0,00%
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ $dado->distrito_nome }}</td>
                                    <td style="text-align: right;">{{ $dado->total_membros }}</td>
                                    <td style="text-align: right;">
                                        @if ($totalGeral > 0)
                                            {{ number_format(($dado->total_membros / $totalGeral) * 100, 2, ',', '.') }}%
                                        @else
                                            0,00%
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: left;">{{ __('Total da 6 Região') }}</th>
                            <th style="text-align: right;">{{ $totalGeral }}</th>
                            @if ($tipo == 'igreja')
                                <th></th>
                            @endif
                            <th style="text-align: right;">100,00%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

@section('extras-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleDistrito(distritoId) {
        let distritoRow = $(`.distrito-row[data-distrito="${distritoId}"]`);
        let igrejaRows = $(`.igreja-row[data-parent="${distritoId}"]`);

        igrejaRows.toggleClass('hidden-row');
        distritoRow.toggleClass('collapsed');
    }
</script>
@endsection
@endsection
