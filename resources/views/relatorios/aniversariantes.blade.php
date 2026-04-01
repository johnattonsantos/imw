@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Relatório Aniversariantes', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <style>
    .old {
        color: red;
    }
    .middleaged {
        color: blue;
    }
    .young {
        color: green;
    }
  </style>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>Relatório de Aniversariantes</h4>
          </div>
      </div>
  </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form">
        
        {{-- Congregação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Onde congrega:</label>
          </div>
          <div class="col-lg-6">
            <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
              <option value="" {{ old('congregacao_id') == '' ? 'selected' : '' }}>TODOS</option>
              @foreach ($congregacoes as $congregacao)
                <option value="{{ $congregacao->id }}" {{ old('congregacao_id') == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Meses --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Meses:</label>
          </div>
          <div class="col-lg-6">
            <select id="mes" name="mes" class="form-control @error('mes') is-invalid @enderror" >
              <option value="" {{ old('mes') == '' ? 'selected hidden' : '' }}>Selecione</option>
              <option value="1" {{ old('mes') == '1' ? 'selected' : '' }}>JANEIRO</option>
              <option value="2" {{ old('mes') == '2' ? 'selected' : '' }}>FEVEREIRO</option>
              <option value="3" {{ old('mes') == '3' ? 'selected' : '' }}>MARÇO</option>
              <option value="4" {{ old('mes') == '4' ? 'selected' : '' }}>ABRIL</option>
              <option value="5" {{ old('mes') == '5' ? 'selected' : '' }}>MAIO</option>
              <option value="6" {{ old('mes') == '6' ? 'selected' : '' }}>JUNHO</option>
              <option value="7" {{ old('mes') == '7' ? 'selected' : '' }}>JULHO</option>
              <option value="8" {{ old('mes') == '8' ? 'selected' : '' }}>AGOSTO</option>
              <option value="9" {{ old('mes') == '9' ? 'selected' : '' }}>SETEMBRO</option>
              <option value="10" {{ old('mes') == '10' ? 'selected' : '' }}>OUTUBRO</option>
              <option value="11" {{ old('mes') == '11' ? 'selected' : '' }}>NOVEMBRO</option>
              <option value="12" {{ old('mes') == '12' ? 'selected' : '' }}>DEZEMBRO</option>
            </select>
          </div>
        </div>

        {{-- Vínculo --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Vínculo:</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input checked type="checkbox" name="vinculo[]" id="vinculo_membro" value="M" class="new-control-input">
                  <span class="new-control-indicator"></span>Membro
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input checked type="checkbox" name="vinculo[]" value="C" class="new-control-input">
                  <span class="new-control-indicator"></span>Congregado
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input checked type="checkbox" name="vinculo[]" value="V" class="new-control-input">
                  <span class="new-control-indicator"></span>Visitante
                </label>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group row mb-4">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button id="btn_buscar" type="submit" name="action" value="buscar" title="Buscar dados do Relatório" class="btn btn-primary btn">
              <x-bx-search /> Buscar 
            </button>
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
                  <h4 style="text-transform: uppercase">RELATÓRIO SECRETARIA ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h4>
                  <p class="pl-3">Vínculo: {{ $vinculos }}</p>
                  <p class="pl-3">Meses: {{ $mes }}</p>
                  <p class="pl-3">Onde Congrega: {{ $ondeCongrega }}</p>
                  <p class="pl-3">Registros Encontrados: {{ $membros->count() }}</p>
              </div>
          </div>
        </div>
        <div class="widget-content widget-content-area">
          
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="aniversariantes">
                    <thead>
                        <tr>
                            <th>NOME</th>
                            <th>ANIVERSÁRIO</th>
                            <th>NASCIMENTO</th>
                            <th>IDADE</th>
                            <th>TELEFONE</th>
                            <th>ONDE CONGREGA</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($membros as $membro)
                          <tr>
                            <td>{{ $membro->nome }}</td>
                            <td>{{ $membro->aniversario }}</td>
                            <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                            <td>{{ $membro->idade }}</td>
                            <td>{{ formatStr($membro->contato, '## (##) #####-####') }}</td>
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
  $('#btn_buscar').click(function () {
    $('#filter_form').removeAttr('target');
  })
  
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
              title: "RELATÓRIO SECRETARIA ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}"
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              titleAttr: 'PDF',
              title: "RELATÓRIO SECRETARIA ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}",

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
                                        text: `RELATÓRIO SECRETARIA ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}`,
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
            },
            {
              extend: 'print',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-print"></i> Imprimir',
              titleAttr: 'Imprimir',
              title: "RELATÓRIO SECRETARIA ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}",
              customize: function ( win ) {
                $(win.document.body)
                  .css( 'font-size', '14pt' )
                  .find( 'h1' )
                        .css( 'text-align', 'center' ).css( 'font-size', '18pt' ).css( 'font-weight', 'bold');

                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', 'inherit');
              }
            }]
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
