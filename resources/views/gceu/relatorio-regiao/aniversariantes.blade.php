@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Relatório de Aniversariantes GCEU', 'url' => '#', 'active' => true]
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
              <h4>{{ $titulo }}</h4>
          </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form">
        
        {{-- Congregação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-3">
            <label class="control-label">Distrito:</label>
            <select id="regiao_id" name="distrito_id" class="form-control @error('distrito_id') is-invalid @enderror" >
              <option value="" {{ request()->distrito_id == '' ? 'selected' : '' }}>TODOS</option>
              @foreach($distritos as $distrito)
                <option value="{{ $distrito->id }}" {{ request()->distrito_id == $distrito->id ? 'selected' : '' }}>{{ $distrito->distrito_nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-4">
            <label class="control-label">Igreja:</label>
            <select id="igreja_id" name="igreja_id" class="form-control @error('igreja_id') is-invalid @enderror" >
              <option value="" {{ request()->igreja_id == '' ? 'selected' : '' }}>TODAS</option>
              @foreach($igrejas as $igreja)
                <option value="{{ $igreja->id_igreja }}" {{ request()->igreja_id == $igreja->id_igreja ? 'selected' : '' }}>{{ $igreja->igreja_nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-3">
            <label class="control-label">GCEU:</label>
            <select id="gceu_id" name="gceu_id" class="form-control @error('gceu_id') is-invalid @enderror" >
              <option value="" {{ request()->gceu_id == '' ? 'selected' : '' }}>TODOS</option>
              @foreach ($gceus as $gceu)
                <option value="{{ $gceu->id }}" {{ request()->gceu_id == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2">
            <button id="btn_buscar" type="submit" name="action" value="buscar" title="Buscar dados do Relatório" class="btn btn-primary btn" style="margin-top: 30px;">
              <x-bx-search /> Buscar 
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
          
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="aniversariantes">
                    <thead>
                        <tr>
                          <th>Nº</th>
                          <th>DISTRITO</th>
                          <th>IGREJA</th>
                          <th>NOME</th>
                          <th>ANIVERSÁRIO</th>
                          <th>NASCIMENTO</th>
                          <th>IDADE</th>
                          <th>CONTATO</th>
                          <th>GCEU</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($dados as $key => $item)
                          <tr>
                            <td>{{ $key += 1 }}</td>
                            <td>{{ $item->distrito_nome }}</td>
                            <td>{{ $item->igreja_nome }}</td>
                            <td>{{ $item->nome }}</td>
                            <td>{{ $item->aniversario }}</td>
                            <td>{{ formatDate($item->data_nascimento) }}</td>
                            <td>{{ $item->idade }}</td>
                            <td>{{ formatStr($item->contato, '## (##) #####-####') }}</td>
                            <td>{{ $item->gceu }}</td>
                          </tr>
                      @endforeach
                    </tbody>
                    @isset($key)
                    <tfoot>
                      <tr>
                          <td>{{ $key }}</td>
                          <td colspan="8"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
  </div>

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
  
  $('#btn_relatorio').click(function () {
    $('#filter_form').attr('target', '_blank');
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
              title: "{{ $titulo }}"
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              titleAttr: 'PDF',
              pageSize: 'LETTER',
              orientation: 'landscape',
              title: "{{ $titulo }}",

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
                                        text: `{{ $titulo }}`,
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
