@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Relatório Secretaria/Membresia', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>{{ __('Relatório de Membresia') }}</h4>
          </div>
      </div>
  </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form">
        
        {{-- Congregação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Onde congrega:') }}</label>
          </div>
          <div class="col-lg-6">
            <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
              <option value="" {{ !request()->get('congregacao_id') ? 'selected' : '' }}>{{ __('TODOS') }}</option>
              @foreach ($congregacoes as $congregacao)
                <option value="{{ $congregacao->id }}" {{ request()->get('congregacao_id') == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Vínculo --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Vínculo:') }}</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input {{ !request()->get('vinculo') || request()->get('vinculo') == 'M' ? 'checked' : '' }} 
                         type="radio" name="vinculo" id="vinculo_membro" value="M" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Membro') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input {{ request()->get('vinculo') == 'C' ? 'checked' : '' }}
                         type="radio" name="vinculo" value="C" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Congregado') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input {{ request()->get('vinculo') == 'V' ? 'checked' : '' }} type="radio" name="vinculo" value="V" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Visitante') }}
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Situação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Situação:') }}</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('situacao') == 'ativos' ? 'checked' : '' }}
                         type="radio" name="situacao" value="ativos" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Ativos') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('situacao') == 'inativos' ? 'checked' : '' }}
                         type="radio" name="situacao" value="inativos" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Inativos') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('situacao') == 'todos' || !request()->get('situacao') ? 'checked' : '' }} 
                         type="radio" name="situacao" value="todos" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Todos') }}
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Seletor do tipo de data --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Filtro:') }}</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ !request()->get('dt_filtro') ? 'checked' : '' }}
                         type="radio" name="dt_filtro" value="" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Nenhuma') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('dt_filtro') == 'data_nascimento' ? 'checked' : '' }}
                         type="radio" name="dt_filtro" value="data_nascimento" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Nascimento') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('dt_filtro') == 'dt_recepcao' ? 'checked' : '' }}
                         type="radio" name="dt_filtro" value="dt_recepcao" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Recepção') }}
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('dt_filtro') == 'dt_exclusao' ? 'checked' : '' }}
                         type="radio" name="dt_filtro" value="dt_exclusao" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Exclusão') }}
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Inserção de data --}}
        <div class="form-group row mb-4 {{ !request()->get('dt_filtro') ? 'd-none' : '' }}" id="filtros_data">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Período (Inicial e Final):') }}</label>
          </div>
          <div class="col-lg-3">
            <input type="date" class="form-control @error('dt_inical') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->get('dt_inicial') }}" placeholder="{{ __('ex: 31/12/2000') }}">
          </div>
          <div class="col-lg-3">
            <input type="date" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->get('dt_final') }}" placeholder="{{ __('ex: 31/12/2000') }}">
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
              <x-bx-search /> {{ __('Buscar') }} 
            </button>
            <!-- <button id="btn_relatorio" type="submit" name="action" value="relatorio" title="{{ __('Gerar Relatório PDF') }}" class="btn btn-secondary btn ml-4">
              <x-bx-file /> {{ __('Relatório') }}
            </button> -->
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TABELA -->
@isset($membros)
  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
          <div class="row">
              <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                  <h4 style="text-transform: uppercase">RELATÓRIO SECRETARIA {{ isset($vinculos) ? $vinculos : '' }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h4>
                  <p class="pl-3">Registros Encontrados: {{ $membros->count() }}</p>
                  <p class="pl-3">Vínculo: {{ isset($vinculos) ? $vinculos : '' }}</p>
                  <p class="pl-3">Situação: {{ $situacao }}</p>
                  <p class="pl-3">Onde Congrega: {{ $ondeCongrega }}</p>
              </div>
          </div>
        </div>
        <div class="widget-content widget-content-area">
          
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4"  id="aniversariantes">
                    <thead>
                        <tr>
                            <th>{{ __('ROL') }}</th>
                            <th>{{ __('NOME') }}</th>
                            <th>{{ __('TELEFONE') }}</th>
                            <th>{{ __('SITUAÇÃO') }}</th>
                            <th>{{ __('VÍNCULO') }}</th>
                            <th>{{ __('NASCIMENTO') }}</th>
                            <th>{{ __('RECEPÇÃO') }}</th>
                            <th>{{ __('MODO') }}</th>
                            <th>{{ __('EXCLUSÃO') }}</th>
                            <th>{{ __('MODO') }}</th>
                            <th>{{ __('LOCAL') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($membros as $membro)
                        <tr>
                          <td>{{ $membro->rol_atual ?? 0 }}</td>
                          <td>{{ $membro->nome }}</td>
                          <td>{{ formatStr($membro->telefone, '## (##) #####-####') }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                              {{ $membro->status == 'A' ? 'ATIVO' : 'INATIVO' }}
                            @else
                              {{ $membro->statusText }}
                            @endif
                          </td>
                          <td>{{ $membro->vinculoText }}</td>
                          <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                                {{ $membro->dt_recepcao }}
                            @else
                              {{ optional($membro->created_at)->format('d/m/Y') }}
                            @endif
                          </td>
                          <td>{{ $membro->modo_recepcao ? $membro->modo_recepcao : '-' }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                              {{ $membro->dt_exclusao }}
                            @else
                              
                            @endif
                          </td>
                          <td>{{ $membro->modo_exclusao ? $membro->modo_exclusao : '-' }}</td>
                          <td>{{ optional($membro->congregacao)->nome ?? 'SEDE' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
@endisset

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
  // exibe o campo de "datas" caso seja selecionada uma data para filtro
  $('[name="dt_filtro"]').change(function () {
    if ($(this).val()) {
      $('#filtros_data').removeClass('d-none');
      resetDateFields()
    } else {
      $('#filtros_data').addClass('d-none');
      resetDateFields()
    }
  });

  $('#btn_buscar').click(function () {
    $('#filter_form').removeAttr('target');
  })
  
  $('#btn_relatorio').click(function () {
    $('#filter_form').attr('target', '_blank');
  })

  function resetDateFields() {
    $('#dt_inicial').val('')
    $('#dt_final').val('')
  }
 

new DataTable('#aniversariantes', {
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
              title: "RELATÓRIO SECRETARIA {{ isset($vinculos) ? $vinculos : '' }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}"
            },
            {
              extend: 'pdfHtml5',
              orientation: 'landscape',
              pageSize: 'LEGAL',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              titleAttr: 'PDF',
              title: "RELATÓRIO SECRETARIA {{ isset($vinculos) ? $vinculos : '' }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}",

              customize: function (doc) {
                        doc.content.splice(0,1);
                        var now = new Date();
                        var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
                        //doc.pageMargins = [20,50,20,30];
                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader.fontSize = 9;
                        

                        const hoje = new Date();
                        const dataFormatada = hoje.toLocaleDateString('pt-BR');
                        const horaFormatada = hoje.toLocaleTimeString('pt-BR');
                        const dataHoraFormatada = `${dataFormatada} ${horaFormatada}`;
                        doc['header']=(function() {
                            return {
                                columns: [

                                    {
                                        alignment: 'left',
                                        italics: false,
                                        text: `RELATÓRIO SECRETARIA {{ isset($vinculos) ? $vinculos : '' }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}`,
                                        fontSize: 14,
                                        margin: [10,0]
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

                        var numColumns = doc.content[0].table.body[0].length; 
                        doc.content[0].table.widths = Array(numColumns).fill('*');


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