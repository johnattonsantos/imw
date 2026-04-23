@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Relatório Lista de Funções', 'url' => '#', 'active' => true]
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
              <h4>Relatório de funções de GCEU da Região: {{ $igreja }}</h4>
          </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form">
        
        {{-- Congregação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-3">
            <label class="control-label">Igreja:</label>
            <select id="instituicao_id" name="instituicao_id" class="form-control @error('instituicao_id') is-invalid @enderror" >
              <option value="" {{ request()->instituicao_id == '' ? 'selected' : '' }}>TODAS</option>
              @foreach($igrejas as $igreja)
                <option value="{{ $igreja->id_igreja }}" {{ request()->instituicao_id == $igreja->id_igreja ? 'selected' : '' }}>{{ $igreja->igreja_nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2">
            <label class="control-label">Função:</label>
            <select id="funcao_id" name="funcao_id" class="form-control @error('funcao_id') is-invalid @enderror" >
              <option value="" {{ request()->funcao_id == '' ? 'selected' : '' }}>TODAS</option>
              @foreach ($funcoes as $funcao)
                <option value="{{ $funcao->id }}" {{ request()->funcao_id == $funcao->id ? 'selected' : '' }}>{{ $funcao->funcao }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2">
            <label class="control-label">Tipo:</label>
            <select id="tipo" name="tipo" class="form-control">
              <option value="" {{ request()->tipo == '' ? 'selected' : '' }}>TODOS</option>
              <option value="M" {{ request()->tipo == 'M' ? 'selected' : '' }}>Membro</option>
              <option value="C" {{ request()->tipo == 'C' ? 'selected' : '' }}>Congregado</option>
              <option value="V" {{ request()->tipo == 'V' ? 'selected' : '' }}>Visitante</option>
              <option value="N" {{ request()->tipo == 'N' ? 'selected' : '' }}>Novo Convertido</option>
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
          <h4>{{ $titulo }}</h4>          
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="aniversariantes">
                    <thead>
                        <tr>
                           <th>#</th>
                            <th>IGREJA</th>
                            <th>MEMBRO</th>
                            <th>TIPO</th>
                            <th>NOVO CONVERTIDO</th>
                            <th>DATA CADASTRO</th>
                            <th>CONTATO</th>
                            <th>FUNÇÃO</th>
                            <th>GCEU</th>
                            <th>ENDEREÇO GCEU</th>
                            <th>ANFITRIAO</th>
                            <th>CONTATO</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($dados as $key => $item)
                          <tr>
                            <td>{{ $key += 1 }}</td>
                            <td>{{ $item->igreja_nome }}</td>
                            <td>{{ $item->lider }}</td>
                            <td>{{ $item->tipo }}</td>
                            <td>{{ in_array(strtoupper((string) ($item->novo_convertido ?? '')), ['1', 'S', 'SIM', 'Y', 'TRUE'], true) ? 'Sim' : 'Não' }}</td>
                            <td>{{ !empty($item->data_cadastro) ? \Carbon\Carbon::parse($item->data_cadastro)->format('d/m/Y') : '-' }}</td>
                            <td>{{ formatStr($item->telefone_preferencial, '## (##) #####-####') }}</td>
                            <td>{{ $item->funcao }}</td>
                            <td>{{ $item->nome }}</td>
                            <td>{{ $item->endereco }} {{ $item->numero }}, {{ $item->bairro }}, {{ $item->cidade }}, {{ $item->uf }}</td>
                            <td>{{ $item->anfitriao }}</td>
                            <td>{{ formatStr($item->contato, '## (##) #####-####') }}</td>
                          </tr>
                      @endforeach
                    </tbody>
                    @isset($key)
                    <tfoot>
                      <tr>
                          <td>{{ $key }}</td>
                          <td colspan="11"></td>
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
