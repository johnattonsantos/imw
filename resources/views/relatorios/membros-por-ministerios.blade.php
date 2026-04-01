@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Membros por Ministérios', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>Membros por Ministérios</h4>
          </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" method="GET" action="{{ route('relatorio.membros-por-ministerios') }}">
        @php
          $vinculosSelecionados = $vinculosSelecionados ?? ['nao_congregado', 'congregado'];
          $nomeacaoAtiva = $nomeacaoAtiva ?? false;
        @endphp
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Ministério:</label>
          </div>
          <div class="col-lg-6">
            <select class="form-control" name="ministerio" id="ministerio">
              @foreach ($ministerios as $key => $label)
                <option value="{{ $key }}" {{ $ministerioSelecionado === $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Nomeação:</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input type="checkbox" name="nomeacao_ativa" value="1" class="new-control-input" {{ $nomeacaoAtiva ? 'checked' : '' }}>
                  <span class="new-control-indicator"></span>Nomeação ativa
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Vínculo:</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input type="checkbox" name="vinculo[]" value="congregado" class="new-control-input" {{ in_array('congregado', $vinculosSelecionados, true) ? 'checked' : '' }}>
                  <span class="new-control-indicator"></span>Congregado
                </label>
              </div>
            </div>
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox checkbox-outline-success">
                  <input type="checkbox" name="vinculo[]" value="nao_congregado" class="new-control-input" {{ in_array('nao_congregado', $vinculosSelecionados, true) ? 'checked' : '' }}>
                  <span class="new-control-indicator"></span>Não congregado
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button type="submit" class="btn btn-primary btn">
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
      <div class="widget-header">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                <h4 style="text-transform: uppercase">
                  Ministério: {{ $ministerioNome }} | Quantidade de integrantes: {{ $quantidadeIntegrantes }}
                </h4>
            </div>
        </div>
      </div>
      <div class="widget-content widget-content-area">
          <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="membros-por-ministerios-table">
                  <thead>
                      <tr>
                          <th>NOME</th>
                          <th>CONTATOS</th>
                      </tr>
                  </thead>
                  <tbody>
                    @forelse ($integrantes as $integrante)
                      @php
                        $contatoNumerico = preg_replace('/\D+/', '', (string) $integrante->contato);
                        $contatoFormatado = '-';
                        if (strlen($contatoNumerico) === 11) {
                            $contatoFormatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $contatoNumerico);
                        } elseif (strlen($contatoNumerico) === 10) {
                            $contatoFormatado = preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $contatoNumerico);
                        } elseif ($contatoNumerico !== '') {
                            $contatoFormatado = $integrante->contato;
                        }
                      @endphp
                      <tr>
                          <td>{{ $integrante->nome }}</td>
                          <td>{{ $contatoFormatado }}</td>
                      </tr>
                    @empty
                      <tr>
                          <td>Não existem registros para o ministério selecionado</td>
                          <td>-</td>
                      </tr>
                    @endforelse
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
<script>
  new DataTable('#membros-por-ministerios-table', {
    layout: {
      topStart: {
        buttons: [
          'pageLength',
          {
            extend: 'excel',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-excel"></i> Excel',
            titleAttr: 'Excel',
            title: "Membros por Ministérios - {{ $ministerioNome }}"
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            titleAttr: 'PDF',
            title: "Membros por Ministérios - {{ $ministerioNome }}"
          },
          {
            extend: 'print',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-print"></i> Imprimir',
            titleAttr: 'Imprimir',
            title: "Membros por Ministérios - {{ $ministerioNome }}"
          }
        ]
      },
      topEnd: 'search',
      bottomStart: 'info',
      bottomEnd: 'paging'
    },
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
    }
  });
</script>
@endsection
