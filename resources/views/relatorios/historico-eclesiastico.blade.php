@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Relatório Membros por Função Ministerial', 'url' => '#', 'active' => true]
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
              <h4>Membros por Função Ministerial</h4>
          </div>
      </div>
  </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form">
        
        {{-- Congregação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Membro:</label>
          </div>
          <div class="col-lg-6">
            <select class="form-control select2 @error('membro_id') is-invalid @enderror" data-bs-toggle="select2" name="membro_id" id="membro_id">              
              <!-- <option value="" {{ old('membro_id') == '' ? 'selected' : '' }} hidden disabled>selecione</option> -->
              <option value="todos" {{ $select == 'todos' ? 'selected' : '' }} >TODOS</option>
              @foreach ($membros as $membro)
                <option value="{{ $membro->id }}" {{ $select == $membro->id  ? 'selected' : '' }} >{{ $membro->nome }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Nomeação --}}
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Nomeação:</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input type="radio" name="nomeacao_ativa" value="0" class="new-control-input" checked>
                  <span class="new-control-indicator"></span>Todas as Nomeações
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input type="radio" name="nomeacao_ativa" value="1" class="new-control-input">
                  <span class="new-control-indicator"></span>Nomeação Ativa
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
            <button id="btn_relatorio" type="submit" name="action" value="relatorio" title="Gerar Relatório PDF" class="btn btn-secondary btn ml-4">
              <x-bx-file /> Relatório
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TABELA -->

@if($membro_unico)
  @isset($historicoEclesiastico)
    <div class="col-lg-12 col-12 layout-spacing">
      <div class="statbox widget box box-shadow">
          <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4 style="text-transform: uppercase">RELATÓRIO MEMBROS POR FUNÇÃO MINISTERIAL - {{ $membroEclesiastico->nome }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h4>
                </div>
            </div>
          </div>
          <div class="widget-content widget-content-area">
            
              <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover mb-4" id="membros-por-ministerio">
                      <thead>
                          <tr>
                              <th>ROL</th>
                              <th>NOME</th>
                              <th>CELULAR</th>
                              <th>MINISTÉRIO</th>
                              <th>FUNÇÃO</th>
                              <th>NOMEAÇÃO</th>
                              <th>EXONERAÇÃO</th>
                          </tr>
                      </thead>
                      <tbody>
                        @forelse ($historicoEclesiastico as $historico)
                          <tr>
                              <td>{{ $membroEclesiastico->rol_atual }}</td>
                              <td>{{ $membroEclesiastico->nome }}</td>
                              <td>{{ formatStr($membroEclesiastico->telefone, '## (##) #####-####') }}</td>
                              <td>{{ $historico->ministerio->descricao }}</td>
                              <td>{{ $historico->tipoAtuacao->descricao }}</td>
                              <td>{{ optional($historico->data_entrada)->format('d/m/Y') }}</td>
                              <td>{{ optional($historico->data_saida)->format('d/m/Y') }}</td>
                          </tr>
                        @empty
                          <tr>
                              <td colspan="6" style="text-align: center">Não existem registros para este membro</td>
                          </tr>
                        @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  @endisset
@else
@isset($todos_membros)
  <div class="col-lg-12 col-12 layout-spacing">
      <div class="statbox widget box box-shadow">
          <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4 style="text-transform: uppercase">RELATÓRIO MEMBROS POR FUNÇÃO MINISTERIAL - TODOS - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h4>
                </div>
            </div>
          </div>
          <div class="widget-content widget-content-area">

              <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover mb-4" id="membros-por-ministerio">
                      <thead>
                          <tr>
                              <th>ROL</th>
                              <th>NOME</th>
                              <th>CELULAR</th>
                              <th>MINISTÉRIO</th>
                              <th>FUNÇÃO</th>
                              <th>NOMEAÇÃO</th>
                              <th>EXONERAÇÃO</th>
                          </tr>
                      </thead>
                      <tbody>
                        @forelse ($todos_membros as $membroEclesiastico)
                          @foreach($membroEclesiastico['historicoEclesiastico'] as $historico)
                            <tr>
                                <td>{{ $membroEclesiastico['membro']->rol_atual }}</td>
                                <td>{{ $membroEclesiastico['membro']->nome }}</td>
                                <td>{{ formatStr($membroEclesiastico['membro']->telefone, '## (##) #####-####') }}</td>
                                <td>{{ $historico->ministerio->descricao }}</td>
                                <td>{{ $historico->tipoAtuacao->descricao }}</td>
                                <td>{{ optional($historico->data_entrada)->format('d/m/Y') }}</td>
                                <td>{{ optional($historico->data_saida)->format('d/m/Y') }}</td>
                            </tr>
                          @endforeach
                        @empty
                          <tr>
                              <td colspan="6" style="text-align: center">Não existem registros para este membro</td>
                          </tr>
                        @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  @endisset
@endif
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

  $('#membro_id').change(function () {
    if($(this).val()) {
      $('#btn_buscar').removeAttr('disabled')
      $('#btn_relatorio').removeAttr('disabled')
    } else {
      $('#btn_buscar').addAttr('disabled', true)
      $('#btn_relatorio').addAttr('disabled', true)
    }
  })

  new DataTable('#membros-por-ministerio', {
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
              title: "RELATÓRIO MEMBROS POR FUNÇÃO MINISTERIAL - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}"
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              titleAttr: 'PDF',
              title: "RELATÓRIO MEMBROS POR FUNÇÃO MINISTERIAL - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}",
              pageSize: 'A4',
                exportOptions: {
                    columns: ':visible',
                    search: 'applied',
                    order: 'applied'
                },
            },
            {
              extend: 'print',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-print"></i> Imprimir',
              titleAttr: 'Imprimir',
              title: "RELATÓRIO MEMBROS POR FUNÇÃO MINISTERIAL - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}",
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