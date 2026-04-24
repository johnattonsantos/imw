@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Cadastro Reunião', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@include('extras.alerts')
@include('extras.alerts-error-all')

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Cadastro de Visitantes e Novos Convertidos por Reunião - {{ $igreja }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('gceu.reuniao-pessoas.store') }}" class="form-vertical">
                @csrf
                <div class="form-group row mb-4">
                    <div class="col-lg-3">
                        <label class="control-label">* GCEU</label>
                        <select id="gceu_cadastro_id" name="gceu_cadastro_id" class="form-control @error('gceu_cadastro_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($gceus as $gceu)
                                <option value="{{ $gceu->id }}" {{ old('gceu_cadastro_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                            @endforeach
                        </select>
                        @error('gceu_cadastro_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-2">
                        <label class="control-label">* Data da Reunião</label>
                        <input type="date" name="data_reuniao" class="form-control @error('data_reuniao') is-invalid @enderror" value="{{ old('data_reuniao', date('Y-m-d')) }}" required>
                        @error('data_reuniao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-3">
                        <label class="control-label">* Nome</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" maxlength="150" required>
                        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-2">
                        <label class="control-label">Contato</label>
                        <input type="text" name="contato" class="form-control @error('contato') is-invalid @enderror" value="{{ old('contato') }}" maxlength="20" placeholder="(00) 00000-0000">
                        @error('contato')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-2">
                        <label class="control-label">* Tipo</label>
                        <select id="tipo" name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                            <option value="V" {{ old('tipo', 'V') === 'V' ? 'selected' : '' }}>Visitante</option>
                            <option value="N" {{ old('tipo') === 'N' ? 'selected' : '' }}>Novo Convertido</option>
                        </select>
                        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group row mb-4" id="cpf-container" style="{{ old('tipo', 'V') === 'N' ? '' : 'display:none;' }}">
                    <div class="col-lg-3">
                        <label class="control-label">* CPF (Novo Convertido)</label>
                        <input
                            type="text"
                            id="cpf"
                            name="cpf"
                            class="form-control @error('cpf') is-invalid @enderror"
                            value="{{ old('cpf') }}"
                            maxlength="14"
                            placeholder="000.000.000-00">
                        @error('cpf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><x-bx-save /> Salvar</button>
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
                    <h4>Registros Cadastrados</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="form-vertical mb-4">
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label class="control-label">GCEU</label>
                        <select id="filtro_gceu_id" name="filtro_gceu_id" class="form-control">
                            <option value="">Todos</option>
                            @foreach($gceus as $gceu)
                                <option value="{{ $gceu->id }}" {{ request('filtro_gceu_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="control-label">Tipo</label>
                        <select name="filtro_tipo" class="form-control">
                            <option value="">Todos</option>
                            <option value="V" {{ request('filtro_tipo') === 'V' ? 'selected' : '' }}>Visitante</option>
                            <option value="N" {{ request('filtro_tipo') === 'N' ? 'selected' : '' }}>Novo Convertido</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="control-label">Data da Reunião</label>
                        <input type="date" name="filtro_data_reuniao" class="form-control" value="{{ request('filtro_data_reuniao') }}">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary" style="margin-top: 30px;"><x-bx-search /> Buscar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="reuniao-pessoas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>GCEU ID</th>
                            <th>INSTITUIÇÃO ID</th>
                            <th>GCEU</th>
                            <th>Data Reunião</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Contato</th>
                            <th>Tipo</th>
                            <th>Criado Em</th>
                            <th>Atualizado Em</th>
                            <th>Excluído Em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registros as $registro)
                            <tr>
                                <td>{{ $registro->id }}</td>
                                <td>{{ $registro->gceu_cadastro_id }}</td>
                                <td>{{ $registro->instituicao_id }}</td>
                                <td>{{ $registro->gceu_nome }}</td>
                                <td>{{ !empty($registro->data_reuniao) ? \Carbon\Carbon::parse($registro->data_reuniao)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $registro->nome }}</td>
                                <td>{{ !empty($registro->cpf) ? formatStr($registro->cpf, '###.###.###-##') : '-' }}</td>
                                <td>{{ !empty($registro->contato) ? formatStr($registro->contato, '(##) #####-####') : '-' }}</td>
                                <td>
                                    @if($registro->tipo === 'N')
                                        <span class="badge badge-success">Novo Convertido</span>
                                    @else
                                        <span class="badge badge-warning">Visitante</span>
                                    @endif
                                </td>
                                <td>{{ !empty($registro->created_at) ? \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ !empty($registro->updated_at) ? \Carbon\Carbon::parse($registro->updated_at)->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ !empty($registro->deleted_at) ? \Carbon\Carbon::parse($registro->deleted_at)->format('d/m/Y H:i:s') : '-' }}</td>
                                <td style="white-space: nowrap;">
                                    @if(empty($registro->deleted_at) && $registro->tipo === 'V')
                                        <form action="{{ route('gceu.reuniao-pessoas.converter', ['id' => $registro->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Virar Novo Convertido</button>
                                        </form>
                                    @endif
                                    @if(empty($registro->deleted_at))
                                        <form action="{{ route('gceu.reuniao-pessoas.deletar', ['id' => $registro->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja remover este registro?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script>
    $('#gceu_cadastro_id').select2({ width: '100%', placeholder: 'Selecione' });
    $('#filtro_gceu_id').select2({ width: '100%', placeholder: 'Todos' });

    new DataTable('#reuniao-pessoas-table', {
      layout: {
        topStart: {
          buttons: [
            'pageLength',
            {
              extend: 'excel',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-excel"></i> Excel',
              title: 'Cadastro de Visitantes e Novos Convertidos por Reunião - {{ $igreja }}',
              exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11]
              }
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary btn-rounded',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'Cadastro de Visitantes e Novos Convertidos por Reunião - {{ $igreja }}',
              exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11]
              }
            }
          ]
        },
        topEnd: 'search',
        bottomStart: 'info',
        bottomEnd: 'paging'
      },
      language: {
        emptyTable: 'Nenhum registro encontrado',
        info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',
        infoEmpty: 'Mostrando 0 até 0 de 0 registros',
        lengthMenu: 'Mostrar _MENU_ registros',
        loadingRecords: 'Carregando...',
        processing: 'Processando...',
        search: 'Pesquisar:',
        zeroRecords: 'Nenhum registro encontrado',
        paginate: {
          first: 'Primeira',
          last: 'Última',
          next: 'Próxima',
          previous: 'Anterior'
        }
      }
    });

    function toggleCpfField() {
      const tipo = $('#tipo').val();
      const cpfContainer = $('#cpf-container');
      const cpfInput = $('#cpf');
      if (tipo === 'N') {
        cpfContainer.show();
        cpfInput.prop('required', true);
      } else {
        cpfContainer.hide();
        cpfInput.prop('required', false).val('');
      }
    }

    function mascaraCpf(valor) {
      return valor
        .replace(/\D/g, '')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    $('#tipo').on('change', toggleCpfField);
    $('#cpf').on('input', function () {
      this.value = mascaraCpf(this.value).substring(0, 14);
    });
    toggleCpfField();
</script>
@endsection
