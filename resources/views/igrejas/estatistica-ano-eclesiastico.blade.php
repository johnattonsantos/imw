@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Igrejas', 'url' => '/igreja', 'active' => false],
    ['text' => 'Estatística do Ano Eclesiástico', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <style>
    .centralizado {
        display: flex;
        justify-content: center;
        align-items: center;
    }
  </style>
@endsection
@section('content')
@include('extras.alerts-error-all')
@include('extras.alerts')

<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="pt-3 pl-3 mb-4">
          <h5>Estatística Membresia - {{ $igreja->nome }}</h5>
          <h6>Ano Eclesiástico: {{ $ano }}</h6>
      </div>
    </div>

    <div class="widget-content widget-content-area">
      <div class="table-responsive mb-4">
        <table class="table mb-4">
          <thead>
            <tr>
              <th style="width: 90%; position: relative; bottom: 15px;">{{ __('ROL ANTERIOR') }}</th>
              <th class="text-center">MAS {{ $rolAnterior->sexo_masculino }}</th>
              <th class="text-center">FEM {{ $rolAnterior->sexo_feminino }}</th>
              <th class="text-center">TOTAL {{ $rolAnterior->total }}</th>
            </tr>
          </thead>
        </table>
      </div>

      <div class="table-responsive mb-4">
        <table class="table mb-4">
          <thead>
            <tr>
              <th style="width: 90%">{{ __('MEMBROS RECEBIDOS') }}</th>
              <th class="text-center">{{ __('MAS') }}</th>
              <th class="text-center">{{ __('FEM') }}</th>
              <th class="text-center">{{ __('TOTAL') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($membrosRecebidos as $mr)
              <tr>
                <td>{{ $mr->descricao }}</td>
                <td class="text-center">{{ $mr->sexo_masculino }}</td>
                <td class="text-center">{{ $mr->sexo_feminino }}</td>
                <td class="text-center">{{ $mr->total }}</td>
              </tr>
            @endforeach
            <tr>
              <td>{{ __('TOTAL') }}</td>
              <td class="text-center">{{ $membrosRecebidos->sum(fn($item) => $item->sexo_masculino) }}</td>
              <td class="text-center">{{ $membrosRecebidos->sum(fn($item) => $item->sexo_feminino) }}</td>
              <td class="text-center">{{ $membrosRecebidos->sum(fn($item) => $item->total) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-responsive mb-4">
        <table class="table mb-4">
          <thead>
            <tr>
              <th style="width: 90%">{{ __('MEMBROS DESLIGADOS') }}</th>
              <th class="text-center">{{ __('MAS') }}</th>
              <th class="text-center">{{ __('FEM') }}</th>
              <th class="text-center">{{ __('TOTAL') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($membrosExcluidos as $me)
              <tr>
                <td>{{ $me->descricao }}</td>
                <td class="text-center">{{ $me->sexo_masculino }}</td>
                <td class="text-center">{{ $me->sexo_feminino }}</td>
                <td class="text-center">{{ $me->total }}</td>
              </tr>
            @endforeach
            <tr>
              <td>{{ __('TOTAL') }}</td>
              <td class="text-center">{{ $membrosExcluidos->sum(fn($item) => $item->sexo_masculino) }}</td>
              <td class="text-center">{{ $membrosExcluidos->sum(fn($item) => $item->sexo_feminino) }}</td>
              <td class="text-center">{{ $membrosExcluidos->sum(fn($item) => $item->total) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-responsive mb-4">
        <table class="table mb-4">
          <thead>
            <tr>
              <th style="width: 90%; position: relative; bottom: 15px;">{{ __('ROL ATUAL') }}</th>
              <th class="text-center">MAS {{ $rolAtual->sexo_masculino }}</th>
              <th class="text-center">FEM {{ $rolAtual->sexo_feminino }}</th>
              <th class="text-center">TOTAL {{ $rolAtual->total }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extras-scripts')
@endsection