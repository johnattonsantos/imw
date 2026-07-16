@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Membresia', 'url' => '#', 'active' => false],
        ['text' => 'Mapa estatístico de membros', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Relatório Mapa Estatístico de Membros') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div>
                    <form class="form-vertical" id="filter_form"  method="GET">
                        <div class="row col-md-12">
                            <div class="form-group mb-4 col-md-3" id="filtros_data_inicial">
                                <div class="col-md-12">
                                    <label class="control-label">{{ __('* Data Inicial:') }}</label>
                                </div>
                                <div class="col-md-12">
                                    <input type="date" class="form-control @error('data_inicial') is-invalid @enderror"
                                        id="data_inicial" name="data_inicial" value="{{ request()->input('data_inicial') }}"
                                        required>
                                </div>
                            </div>
                            <div class="form-group mb-4 col-md-3" id="filtros_data_final">
                                <div class="col-lg-12">
                                    <label class="control-label">{{ __('* Data Final:') }}</label>
                                </div>
                                <div class="col-md-12">
                                    <input type="date" class="form-control @error('data_final') is-invalid @enderror"
                                        id="data_final" name="data_final" value="{{ request()->input('data_final') }}" required>
                                </div>
                            </div>
                            <div class="form-group mb-4 col-md-3" id="filtros_data_final">
                                <div class="col-lg-12">
                                    <label class="control-label">{{ __('* Tipo de Relatório:') }}</label>
                                </div>
                                <div class="col-md-12">
                                    <select class="form-control " name="relatorio" id="relatorio">
                                        <option value="completo" {{ request('relatorio') == 'completo' ? 'selected' : 'selected' }}>
                                            {{ __('Completo') }}
                                        </option>
                                        <option value="simplificado" {{ request('relatorio') == 'simplificado' ? 'selected' : '' }}>
                                            {{ __('Simplificado') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <button class="btn btn-primary" title="{{ __('Buscar dados') }}" style="margin-top: 30px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" viewBox="0 0 24 24"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z"></path></svg>
                                    {{ __('Buscar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (request()->has('data_inicial'))
    <div class="col-lg-12 col-12 layout-spacing">
        <h6>{{ $titulo }}</h6>
        <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <div class="table-responsive mt-0">
                @if(request('relatorio') == 'completo')
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="ano-eclesiastico">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="12" style="text-align: center;">{{ __('RECEBIDOS') }}</th>
                            <th colspan="14" style="text-align: center;">{{ __('EXCLUÍDOS') }}</th>
                            <th colspan="7" style="text-align: center;">{{ __('ROL') }}</th>
                            <th style="text-align: center;"></th>
                        </tr>
                        <tr>
                            <th>{{ __('DISTRITO') }}</th>
                            <th>{{ __('IGREJA') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('ADESÃO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('BATISMO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('RECONCILIAÇÃO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('TRANSFERÊNCIA') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('CADASTRAMENTO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('PEDIDO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('ABANDONO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('EXCLUSÃO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('FALECIMENTO') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('DUPLICIDADE') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('TRANSFERÊNCIA') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('ANTERIOR') }}</th>
                            <th style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th colspan="2" style="text-align: center;">{{ __('ATUAL') }}</th>
                            <th style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th style="text-align: center;">{{ __('%ROL') }}</th>
                            <th style="text-align: center;">{{ __('VARIAÇÃO') }}</th>
                        </tr>
                        <tr>
                            <th></th>   
                            <th></th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">-</th>
                            <th style="text-align: left;">M</th>
                            <th style="text-align: left;">F</th>
                            <th style="text-align: left;">-</th>
                            <th style="text-align: left;">-</th>
                            <th style="text-align: left;">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membresias as $item)
                            @php 
                                $adesaoM = $item['membrosRecebidos'][1]->sexo_masculino;
                                $totalAdesaoM[] = $adesaoM;
                                $adesaoF = $item['membrosRecebidos'][1]->sexo_feminino;
                                $totalAdesaoF[] = $adesaoF;
                                $batismoM = $item['membrosRecebidos'][0]->sexo_masculino;
                                $totalBatismoM[] = $batismoM;
                                $batismoF = $item['membrosRecebidos'][0]->sexo_feminino;
                                $totalBatismoF[] = $batismoF;
                                $reconciliacaoM = $item['membrosRecebidos'][2]->sexo_masculino;
                                $totalReconciliacaoM[] = $reconciliacaoM;
                                $reconciliacaoF = $item['membrosRecebidos'][2]->sexo_feminino;
                                $totalReconciliacaoF[] = $reconciliacaoF;
                                $transferenciaRecebidosM = $item['membrosRecebidos'][3]->sexo_masculino;
                                $totalTransferenciaRecebidosM[] = $transferenciaRecebidosM;
                                $transferenciaRecebidosF = $item['membrosRecebidos'][3]->sexo_feminino;
                                $totalTransferenciaRecebidosF[] = $transferenciaRecebidosF;
                                $cadastramentoM = $item['membrosRecebidos'][4]->sexo_masculino;
                                $totalCadastramentoM[] = $cadastramentoM;
                                $cadastramentoF = $item['membrosRecebidos'][4]->sexo_feminino;
                                $totalCadastramentoF[] = $cadastramentoF;
                                $totalRecebidoM = $item['membrosRecebidos'][0]->sexo_masculino + $item['membrosRecebidos'][1]->sexo_masculino + $item['membrosRecebidos'][2]->sexo_masculino + $item['membrosRecebidos'][3]->sexo_masculino + $item['membrosRecebidos'][4]->sexo_masculino;
                                $valorTotalRecebidoM[] = $totalRecebidoM;
                                $totalRecebidoF = $item['membrosRecebidos'][0]->sexo_feminino + $item['membrosRecebidos'][1]->sexo_feminino + $item['membrosRecebidos'][2]->sexo_feminino + $item['membrosRecebidos'][3]->sexo_feminino + $item['membrosRecebidos'][4]->sexo_feminino;
                                $valorTotalRecebidoF[] = $totalRecebidoF;
                                $pedidoM = $item['membrosExcluidos'][0]->sexo_masculino;
                                $totalPedidoM[] = $pedidoM;
                                $pedidoF = $item['membrosExcluidos'][0]->sexo_feminino;
                                $totalPedidoF[] = $pedidoF;
                                $abandonoM = $item['membrosExcluidos'][1]->sexo_masculino;
                                $totalAbandonoM[] = $abandonoM;
                                $abandonoF = $item['membrosExcluidos'][1]->sexo_feminino;
                                $totalAbandonoF[] = $abandonoF;
                                $exclusaoM = $item['membrosExcluidos'][2]->sexo_masculino;
                                $totalExclusaoM[] = $exclusaoM;
                                $exclusaoF = $item['membrosExcluidos'][2]->sexo_feminino;
                                $totalExclusaoF[] = $exclusaoF;
                                $falecimentoM = $item['membrosExcluidos'][3]->sexo_masculino;
                                $totalFalecimentoM[] = $falecimentoM;
                                $falecimentoF = $item['membrosExcluidos'][3]->sexo_feminino;
                                $totalFalecimentoF[] = $falecimentoF;
                                $duplicidadeM = $item['membrosExcluidos'][4]->sexo_masculino;
                                $totalDuplicidadeM[] = $duplicidadeM;
                                $duplicidadeF = $item['membrosExcluidos'][4]->sexo_feminino;
                                $totalDuplicidadeF[] = $duplicidadeF;
                                $transferenciaExcluidosM = $item['membrosExcluidos'][5]->sexo_masculino;
                                $totalTransferenciaExcluidosM[] = $transferenciaExcluidosM;
                                $transferenciaExcluidosF = $item['membrosExcluidos'][5]->sexo_feminino;
                                $totalTransferenciaExcluidosF[] = $transferenciaExcluidosF;
                                $totalExcluidoM = $item['membrosExcluidos'][0]->sexo_masculino + $item['membrosExcluidos'][1]->sexo_masculino + $item['membrosExcluidos'][2]->sexo_masculino + $item['membrosExcluidos'][3]->sexo_masculino + $item['membrosExcluidos'][4]->sexo_masculino + $item['membrosExcluidos'][5]->sexo_masculino;
                                $valorTotalExcluidoM[] = $totalExcluidoM;
                                $totalExcluidoF = $item['membrosExcluidos'][0]->sexo_feminino + $item['membrosExcluidos'][1]->sexo_feminino + $item['membrosExcluidos'][2]->sexo_feminino + $item['membrosExcluidos'][3]->sexo_feminino + $item['membrosExcluidos'][4]->sexo_feminino + $item['membrosExcluidos'][5]->sexo_feminino;
                                $valorTotalExcluidoF[] = $totalExcluidoF;
                                $rolAnteriorM = $item['rolAnterior']->sexo_masculino;
                                $totalRolAnteriorM[] = $rolAnteriorM;
                                $rolAnteriorF = $item['rolAnterior']->sexo_feminino;
                                $totalRolAnteriorF[] = $rolAnteriorF;
                                $totalRolAnteriorMF = $item['rolAnterior']->total;
                                $valorTotalRolAnteriorMF[] = $totalRolAnteriorMF;
                                $rolAtualM = $item['rolAtual']->sexo_masculino;
                                $totalRolAtualM[] = $rolAtualM;
                                $rolAtualF = $item['rolAtual']->sexo_feminino;
                                $totalRolAtualF[] = $rolAtualF;
                                $totalRolAtualMF = $item['rolAtual']->total;
                                $valorTotalRolAtualMF[] = $totalRolAtualMF;

                                $rolPorcentagem = $item['rolAnterior']->total > 0 ? decimal(($item['rolAtual']->total - $item['rolAnterior']->total)/$item['rolAnterior']->total * 100) : decimal(0);
                                $totalRolPorcentagem[] = $item['rolAnterior']->total > 0 ? ($item['rolAtual']->total - $item['rolAnterior']->total)/$item['rolAnterior']->total * 100 : 0;
                                $geral = ($totalRecebidoM + $totalRecebidoF) -  ($totalExcluidoM  + $totalExcluidoF);
                                $valorGeral[] = $geral;
                            @endphp
                            <tr>
                                <td>{{ $item['igreja']->distrito }}</td>
                                <td>{{ $item['igreja']->nome }}</td>
                                <td style="text-align: center;">{{ $adesaoM }}</td>
                                <td style="text-align: center;">{{ $adesaoF }}</td>
                                <td style="text-align: center;">{{ $batismoM }}</td>
                                <td style="text-align: center;">{{ $batismoF }}</td>
                                <td style="text-align: center;">{{ $reconciliacaoM }}</td>
                                <td style="text-align: center;">{{ $reconciliacaoF }}</td>
                                <td style="text-align: center;">{{ $transferenciaRecebidosM }}</td>
                                <td style="text-align: center;">{{ $transferenciaRecebidosF }}</td>
                                <td style="text-align: center;">{{ $cadastramentoM }}</td>
                                <td style="text-align: center;">{{ $cadastramentoF }}</td>
                                <td style="text-align: center;">{{ $totalRecebidoM }}</td>
                                <td style="text-align: center;">{{ $totalRecebidoF }}</td>
                                <td style="text-align: center;">{{ $pedidoM }}</td>
                                <td style="text-align: center;">{{ $pedidoF }}</td>
                                <td style="text-align: center;">{{ $abandonoM }}</td>
                                <td style="text-align: center;">{{ $abandonoF }}</td>
                                <td style="text-align: center;">{{ $exclusaoM }}</td>
                                <td style="text-align: center;">{{ $exclusaoF }}</td>
                                <td style="text-align: center;">{{ $falecimentoM }}</td>
                                <td style="text-align: center;">{{ $falecimentoF }}</td>
                                <td style="text-align: center;">{{ $duplicidadeM }}</td>
                                <td style="text-align: center;">{{ $duplicidadeF }}</td>
                                <td style="text-align: center;">{{ $transferenciaExcluidosM }}</td>
                                <td style="text-align: center;">{{ $transferenciaExcluidosF }}</td>
                                <td style="text-align: center;">{{ $totalExcluidoM }}</td>
                                <td style="text-align: center;">{{ $totalExcluidoF }}</td>
                                <td style="text-align: center;">{{ $rolAnteriorM }}</td>
                                <td style="text-align: center;">{{ $rolAnteriorF }}</td>
                                <td style="text-align: center;">{{ $totalRolAnteriorMF }}</td>
                                <td style="text-align: center;">{{ $rolAtualM }}</td>
                                <td style="text-align: center;">{{ $rolAtualF }}</td>
                                <td style="text-align: center;">{{ $totalRolAtualMF }}</td>
                                <td style="text-align: center;">
                                    @if($totalRolAnteriorMF > 0)
                                        {{ decimal(($totalRolAtualMF - $totalRolAnteriorMF) / $totalRolAnteriorMF * 100) }}%
                                    @else
                                        {{ decimal(0) }}%
                                    @endif                                    
                                </td>
                                <td style="text-align: center;">{{ $geral }}</td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                {{ __('Não possui dados') }}
                            </td>
                        </tr>
                        @endforelse
                        <tr>
                            <td>{{ __('TOTAL COLUNAS VERTICAIS') }}</td>   
                            <td></td>
                            <td style="text-align: center;">{{ array_sum($totalAdesaoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalAdesaoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalBatismoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalBatismoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalReconciliacaoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalReconciliacaoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaRecebidosM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaRecebidosF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalCadastramentoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalCadastramentoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalRecebidoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalRecebidoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalPedidoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalPedidoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalAbandonoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalAbandonoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalExclusaoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalExclusaoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalFalecimentoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalFalecimentoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalDuplicidadeM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalDuplicidadeF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaExcluidosM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaExcluidosF) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalExcluidoM) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalExcluidoF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAnteriorM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAnteriorF) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalRolAnteriorMF) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAtualM) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAtualF) }}</td>
                            <td style="text-align: center;">{{ array_sum($valorTotalRolAtualMF) }}</td>
                            <td style="text-align: center;">
                                @if( array_sum($valorTotalRolAnteriorMF) > 0)
                                    {{ decimal((array_sum($valorTotalRolAtualMF) - array_sum($valorTotalRolAnteriorMF))/array_sum($valorTotalRolAnteriorMF)*100) }}%
                                @else
                                    {{ decimal(0) }}%
                                @endif  
                            </td>
                            <td style="text-align: center;">{{ decimal(array_sum($valorGeral)) }}</td>
                        </tr>
                    </tbody>
                </table>
                @else 
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="ano-eclesiastico-simplificado">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="6" style="text-align: center;">{{ __('RECEBIDOS') }}</th>
                            <th colspan="7" style="text-align: center;">{{ __('EXCLUÍDOS') }}</th>
                            <th colspan="3" style="text-align: center;">{{ __('ROL') }}</th>
                            <th style="text-align: center;"></th>
                        </tr>
                        <tr>
                            <th>{{ __('DISTRITO') }}</th>
                            <th>{{ __('IGREJA') }}</th>
                            <th style="text-align: center;">{{ __('ADESÃO') }}</th>
                            <th style="text-align: center;">{{ __('BATISMO') }}</th>
                            <th style="text-align: center;">{{ __('RECONCILIAÇÃO') }}</th>
                            <th style="text-align: center;">{{ __('TRANSFERÊNCIA') }}</th>
                            <th style="text-align: center;">{{ __('CADASTRAMENTO') }}</th>
                            <th style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th style="text-align: center;">{{ __('PEDIDO') }}</th>
                            <th style="text-align: center;">{{ __('ABANDONO') }}</th>
                            <th style="text-align: center;">{{ __('EXCLUSÃO') }}</th>
                            <th style="text-align: center;">{{ __('FALECIMENTO') }}</th>
                            <th style="text-align: center;">{{ __('DUPLICIDADE') }}</th>
                            <th style="text-align: center;">{{ __('TRANSFERÊNCIA') }}</th>
                            <th style="text-align: center;">{{ __('TOTAL') }}</th>
                            <th style="text-align: center;">{{ __('ANTERIOR') }}</th>
                            <th style="text-align: center;">{{ __('ATUAL') }}</th>
                            <th style="text-align: center;">{{ __('%ROL') }}</th>
                            <th style="text-align: center;">{{ __('VARIAÇÃO') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membresias as $item)
                            @php 
                                $totalRecebidoM = $item['membrosRecebidos'][0]->sexo_masculino + $item['membrosRecebidos'][1]->sexo_masculino + $item['membrosRecebidos'][2]->sexo_masculino + $item['membrosRecebidos'][3]->sexo_masculino + $item['membrosRecebidos'][4]->sexo_masculino;
                                $totalRecebidoF = $item['membrosRecebidos'][0]->sexo_feminino + $item['membrosRecebidos'][1]->sexo_feminino + $item['membrosRecebidos'][2]->sexo_feminino + $item['membrosRecebidos'][3]->sexo_feminino + $item['membrosRecebidos'][4]->sexo_feminino;

                                $totalExcluidoM = $item['membrosExcluidos'][0]->sexo_masculino + $item['membrosExcluidos'][1]->sexo_masculino + $item['membrosExcluidos'][2]->sexo_masculino + $item['membrosExcluidos'][3]->sexo_masculino + $item['membrosExcluidos'][4]->sexo_masculino + $item['membrosExcluidos'][5]->sexo_masculino;

                                $totalExcluidoF = $item['membrosExcluidos'][0]->sexo_feminino + $item['membrosExcluidos'][1]->sexo_feminino + $item['membrosExcluidos'][2]->sexo_feminino + $item['membrosExcluidos'][3]->sexo_feminino + $item['membrosExcluidos'][4]->sexo_feminino + $item['membrosExcluidos'][5]->sexo_feminino;
                                $adesao = $item['membrosRecebidos'][1]->sexo_masculino + $item['membrosRecebidos'][1]->sexo_feminino;
                                $totalAdesao[] = $adesao;
                                $batismo = $item['membrosRecebidos'][0]->sexo_masculino + $item['membrosRecebidos'][0]->sexo_feminino;
                                $totalBatismo[] = $batismo;
                                $reconciliacao = $item['membrosRecebidos'][2]->sexo_masculino + $item['membrosRecebidos'][2]->sexo_feminino;
                                $totalReconciliacao[] = $reconciliacao;
                                $transferenciaRecebidos = $item['membrosRecebidos'][3]->sexo_masculino + $item['membrosRecebidos'][3]->sexo_feminino;
                                $totalTransferenciaRecebidos[] = $transferenciaRecebidos;
                                $cadastramento = $item['membrosRecebidos'][4]->sexo_masculino + $item['membrosRecebidos'][4]->sexo_feminino;
                                $totalCadastramento[] = $cadastramento;
                                $recebidos = $totalRecebidoM + $totalRecebidoF;
                                $totalRecebidos[] = $recebidos;
                                $pedido = $item['membrosExcluidos'][0]->sexo_masculino + $item['membrosExcluidos'][0]->sexo_feminino;
                                $totalPedido[] = $pedido;
                                $abandono = $item['membrosExcluidos'][1]->sexo_masculino + $item['membrosExcluidos'][1]->sexo_feminino;
                                $totalAbandono[] = $abandono;
                                $exclusao = $item['membrosExcluidos'][2]->sexo_masculino + $item['membrosExcluidos'][2]->sexo_feminino;
                                $totalExclusao[] = $exclusao;
                                $falecimento = $item['membrosExcluidos'][3]->sexo_masculino + $item['membrosExcluidos'][3]->sexo_feminino;
                                $totalFalecimento[] = $falecimento;
                                $duplicidade = $item['membrosExcluidos'][4]->sexo_masculino + $item['membrosExcluidos'][4]->sexo_feminino;
                                $totalDuplicidade[] = $duplicidade; 
                                $transferenciaExcluidos = $item['membrosExcluidos'][5]->sexo_masculino + $item['membrosExcluidos'][5]->sexo_feminino;
                                $totalTransferenciaExcluidos[] = $transferenciaExcluidos;
                                $excluidos = $totalExcluidoM + $totalExcluidoF;
                                $totalExcluidos[] = $excluidos;
                                $rolAnterior = $item['rolAnterior']->total;
                                $totalRolAnterior[] = $rolAnterior;
                                $rolAtual = $item['rolAtual']->total;
                                $totalRolAtual[] = $rolAtual;
                                $rolPorcentagem = $item['rolAnterior']->total > 0 ? decimal(($item['rolAtual']->total - $item['rolAnterior']->total)/$item['rolAnterior']->total * 100) : decimal(0);
                                $totalRolPorcentagem[] = $item['rolAnterior']->total > 0 ? ($item['rolAtual']->total - $item['rolAnterior']->total)/$item['rolAnterior']->total * 100 : 0;
                                $geral = ($totalRecebidoM + $totalRecebidoF) -  ($totalExcluidoM  + $totalExcluidoF);
                                $valorGeral[] = $geral;
                            @endphp
                            <tr>
                                <td>{{ $item['igreja']->distrito }}</td>
                                <td>{{ $item['igreja']->nome }}</td>
                                <td style="text-align: center;">{{ $adesao }}</td>
                                <td style="text-align: center;">{{ $batismo }}</td>
                                <td style="text-align: center;">{{ $reconciliacao }}</td>
                                <td style="text-align: center;">{{ $transferenciaRecebidos }}</td>
                                <td style="text-align: center;">{{ $cadastramento }}</td>
                                <td style="text-align: center;">{{ $recebidos }}</td>
                                <td style="text-align: center;">{{ $pedido }}</td>
                                <td style="text-align: center;">{{ $abandono }}</td>
                                <td style="text-align: center;">{{ $exclusao }}</td>
                                <td style="text-align: center;">{{ $falecimento }}</td>
                                <td style="text-align: center;">{{ $duplicidade }}</td>
                                <td style="text-align: center;">{{ $transferenciaExcluidos }}</td>
                                <td style="text-align: center;">{{ $excluidos }}</td>
                                <td style="text-align: center;">{{ $rolAnterior }}</td>
                                <td style="text-align: center;">{{ $rolAtual }}</td>
                                <td style="text-align: center;">
                                    @if($rolAnterior > 0)
                                        {{ decimal(($rolAtual - $rolAnterior) / $rolAnterior * 100) }}%
                                    @else
                                        {{ decimal(0) }}%
                                    @endif 
                                </td>
                                <td style="text-align: center;">{{ $geral }}</td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                {{ __('Não possui dados') }}
                            </td>
                        </tr>
                        @endforelse
                        @if($membresias)
                        <tr>
                            <td>{{ __('TOTAL COLUNAS VERTICAIS') }}</td>
                            <td></td>
                            <td style="text-align: center;">{{ array_sum($totalAdesao) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalBatismo) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalReconciliacao) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaRecebidos) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalCadastramento) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRecebidos) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalPedido) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalAbandono) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalExclusao) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalFalecimento) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalDuplicidade) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalTransferenciaExcluidos) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalExcluidos) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAnterior) }}</td>
                            <td style="text-align: center;">{{ array_sum($totalRolAtual) }}</td>
                            <td style="text-align: center;">
                                 @if(array_sum($totalRolAnterior) > 0)
                                    {{ decimal((array_sum($totalRolAtual) - array_sum($totalRolAnterior)) / array_sum($totalRolAnterior) * 100) }}%
                                @else
                                    {{ decimal(0) }}%
                                @endif 
                            <td style="text-align: center;">{{ decimal(array_sum($valorGeral)) }}</td>
                        </tr>
                       @endif                      
                    </tbody>
                </table>   
                @endif        
            </div>
        </div>
        </div>
    </div>
    @endif

    <!-- MODAL DE VISUALIZAÇÃO -->
@endsection

@section('extras-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/dataTables.searchBuilder.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/searchBuilder.dataTables.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.5/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
    <script>
    var titulo = $('#titulo').val();
    var data_inicial = $('#data_inicial').val();
    var data_final = $('#data_final').val();
    $('#btn_buscar').click(function () {
        $('#filter_form').removeAttr('target');
    })
    
    $('#btn_relatorio').click(function () {
        $('#filter_form').attr('target', '_blank');
    })

    new DataTable('#ano-eclesiastico', {
        scrollX: true,
        scrollY: 400,
        scrollCollapse: true,
        layout: {
            //top1: 'searchBuilder',
            topStart: {
            buttons: [
                'pageLength',
                {
                  extend: 'excel',
                  className: 'btn btn-primary btn-rounded',
                  text: '<i class="fas fa-file-excel"></i> Excel',
                  titleAttr: 'Excel',
                  title: titulo
                }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        language: {
        url:"https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
        }
    });

    new DataTable('#ano-eclesiastico-simplificado', {        
        scrollX: true,
        scrollY: 400,
        scrollCollapse: true,
        layout: {
            //top1: 'searchBuilder',
            topStart: {
            buttons: [
                'pageLength',
                {
                  extend: 'excel',
                  className: 'btn btn-primary btn-rounded',
                  text: '<i class="fas fa-file-excel"></i> Excel',
                  titleAttr: 'Excel',
                  title: `{{ $titulo }}`
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-primary btn-rounded',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    titleAttr: 'PDF',
                    title: `{{ $titulo }}`,
                    customize: function (doc) {
                        doc.content.splice(0,1);
                        var now = new Date();
                        var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
                        doc.pageMargins = [20,50,20,30];
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8;

                        const hoje = new Date();
                        const dataFormatada = hoje.toLocaleDateString('pt-BR');
                        const horaFormatada = hoje.toLocaleTimeString('pt-BR');
                        const dataHoraFormatada = `${dataFormatada} ${horaFormatada}`;
                        doc['header']=(function() {
                            return {
                                columns: [

                                    {
                                        alignment: 'center',
                                        italics: false,
                                        text: `{{ $titulo }}`,
                                        fontSize: 14,
                                        //margin: [10,0]
                                    },
                                    // {
                                    //     alignment: 'right',
                                    //     fontSize: 14,
                                    //     text: ``
                                    // }
                                ],
                                margin: [20,20,0,0]
                            }
                        });

                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'left',
                                        text: ['Criado em: ', { text: dataHoraFormatada }]
                                    },
                                    {
                                        alignment: 'right',
                                        text: ['Página ', { text: page.toString() },  ' de ', { text: pages.toString() }]
                                    }
                                ],
                                margin: 20
                            }
                        });

                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        objLayout['paddingRight'] = function(i) { return 4; };
                        doc.content[0].layout = objLayout;
                    },
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        language: {
        url:"https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
        }
    });
    </script>

@endsection
