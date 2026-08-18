@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Clérigos', 'url' => '#', 'active' => false],
        ['text' => 'Clérigos Dados', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }

        .toggle-icon {
            cursor: pointer;
            margin-right: 5px;
        }

        .child-row {
            display: none;
            /* Filhos ficam escondidos inicialmente */
        }
    </style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>{{ __('Relatório Clérigos Dados') }}</h4>
          </div>
      </div>
  </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form"  method="GET">

        <input type="hidden" name="buscar" value="todos">
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Status:') }}</label>
          </div>
          <div class="col-lg-6">
            <div class="form-check form-check-inline">
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('status') == '' ? 'checked' : '' }} type="radio" name="status" value="" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Todos') }}
                </label>
              </div>
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('status') == 'ativo' ? 'checked' : '' }} type="radio" name="status" value="ativo" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Ativos') }}
                </label>
              </div>
              <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                  <input {{ request()->get('status') == 'inativo' ? 'checked' : '' }} type="radio" name="status" value="inativo" class="new-control-input">
                  <span class="new-control-indicator"></span>{{ __('Inativos') }}
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group row mb-4">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
              <x-bx-search /> {{ __('Buscar') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@if (request()->has('buscar'))
  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
      <div class="widget-content widget-content-area">
        <div class="table-responsive mt-0">
          <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="dados-clerigos">
            <thead>
              <tr>
                  <th>{{ __('NOME') }}</th>
                  <th>{{ __('SITUAÇÃO') }}</th>
                  <th>{{ __('CATEGORIA') }}</th>
                  <th>{{ __('SEXO') }}</th>
                  <th>{{ __('ESTADO CIVIL') }}</th>
                  <th>{{ __('FORMAÇÃO') }}</th>
                  <th>{{ __('NASCIMENTO') }}</th>
                  <th>{{ __('CONSAGRAÇÃO') }}</th>
                  <th>{{ __('ORDENAÇÃO') }}</th>
                  <th>{{ __('INTEGRALIZAÇÃO') }}</th>
                  <th>{{ __('IDADE') }}</th>
                  <th>{{ __('ROL') }}</th>
                  <th>{{ __('NATURALIDADE') }}</th>
                  <th>{{ __('CÔNJUGE') }}</th>
                  <th>{{ __('MÃE') }}</th>
                  <th>{{ __('PAI') }}</th>
                  <th>{{ __('E-MAIL') }}</th>
                  <th>{{ __('TELEFONE') }}</th>
                  <th>{{ __('TELEFONE ALTERNATIVO') }}</th>
                  <th>{{ __('IDENTIDADE') }}</th>
                  <th>{{ __('UF IDENTIDADE') }}</th>
                  <th>{{ __('ÓRGÃO EMISSOR') }}</th>
                  <th>{{ __('DATA EMISSÃO') }}</th>
                  <th>{{ __('CPF') }}</th>
                  <th>{{ __('RESIDÊNCIA PRÓPRIA') }}</th>
                  <th>{{ __('FGTS') }}</th>
                  <th>{{ __('REGIÃO') }}</th>
                  <th>{{ __('DISTRITO') }}</th>
                  <th>{{ __('IGREJA') }}</th>
                  <th>{{ __('INSTITUIÇÕES NOMEADAS') }}</th>
                  <th>{{ __('FUNÇÕES NOMEADAS') }}</th>
                  <th>{{ __('PAÍS') }}</th>
                  <th>{{ __('UF') }}</th>
                  <th>{{ __('CEP') }}</th>
                  <th>{{ __('CIDADE') }}</th>
                  <th>{{ __('BAIRRO') }}</th>
                  <th>{{ __('ENDEREÇO') }}</th>
                  <th>{{ __('NÚMERO') }}</th>
                  <th>{{ __('COMPLEMENTO') }}</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($clerigos as $item)
                  <tr>
                      <td>{{ $item->nome }}</td>
                      <td>{{ $item->situacao }}</td>
                      <td>{{ $item->categoria }}</td>
                      <td>{{ __($item->sexo ?: '-') }}</td>
                      <td>{{ __($item->estado_civil ?: '-') }}</td>
                      <td>{{ $item->formacao }}</td>
                      <td>{{ $item->data_nascimento }}</td>
                      <td>{{ $item->data_consagracao }}</td>
                      <td>{{ $item->data_ordenacao }}</td>
                      <td>{{ $item->data_integralizacao }}</td>
                      <td>{{ $item->idade }}</td>
                      <td>{{ $item->rol }}</td>
                      <td>{{ trim(($item->natural_cidade ?? '') . '/' . ($item->natural_uf ?? ''), '/') }}</td>
                      <td>{{ $item->nome_conjuge }}</td>
                      <td>{{ $item->nome_mae }}</td>
                      <td>{{ $item->nome_pai }}</td>
                      <td>{{ $item->email }}</td>
                      <td>{{ $item->telefone_preferencial ? formatStr($item->telefone_preferencial, '## (##) #####-####') : '' }}</td>
                      <td>{{ $item->telefone_alternativo ? formatStr($item->telefone_alternativo, '## (##) #####-####') : '' }}</td>
                      <td>{{ $item->identidade }}</td>
                      <td>{{ $item->identidade_uf }}</td>
                      <td>{{ $item->orgao_emissor }}</td>
                      <td>{{ $item->data_emissao }}</td>
                      <td>{{ $item->cpf ? formatStr($item->cpf, '###.###.###-##') : '' }}</td>
                      <td>{{ __($item->residencia_propria ?: '-') }}</td>
                      <td>{{ __($item->residencia_propria_fgts ?: '-') }}</td>
                      <td>{{ $item->regiao }}</td>
                      <td>{{ $item->distrito }}</td>
                      <td>{{ $item->igreja }}</td>
                      <td>{{ $item->instituicoes_nomeadas }}</td>
                      <td>{{ $item->funcoes_nomeadas }}</td>
                      <td>{{ $item->pais }}</td>
                      <td>{{ $item->uf }}</td>
                      <td>{{ $item->cep ? formatStr($item->cep, '#####-###') : '' }}</td>
                      <td>{{ $item->cidade }}</td>
                      <td>{{ $item->bairro }}</td>
                      <td>{{ $item->endereco }}</td>
                      <td>{{ $item->numero }}</td>
                      <td>{{ $item->complemento }}</td>
                  </tr>
                @empty
                  <p class="text-center text-muted">{{ __('Nenhum resultado encontrado para o período selecionado.') }}</p>
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
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
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script>
    $('#btn_buscar').click(function () {
        $('#filter_form').removeAttr('target');
    })

    new DataTable('#dados-clerigos', {
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
                  title: "IMW - RELATÓRIO CLÉRIGOS DADOS"
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
