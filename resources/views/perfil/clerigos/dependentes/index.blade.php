@extends('template.layout')
@section('breadcrumb')
  <x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Visitantes', 'url' => '#', 'active' => true],
  ]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css">

  <style>
    .swal2-popup .swal2-styled.swal2-cancel {
      color: white !important;
    }
  </style>
@endsection

@section('content')
  @include('extras.alerts')
  <div class="container-fluid d-flex justify-content-between">
    <div>
      <a href="{{ route('clerigos.perfil.dependentes.create') }}" class="btn btn-primary position-relative mt-3 mb-3 ml-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
        <span class="ml-2">{{ __('INCLUIR DEPENDENTE') }}</span>
      </a>
    </div>
  </div>

  <!-- TABELA -->
  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
      <div class="widget-header">
        <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
            <h4>{{ __('Lista de Dependentes') }}</h4>
          </div>
        </div>
      </div>
      <div class="widget-content widget-content-area">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover mb-4" id="datatable">
            <thead>
              <tr>
                <th>{{ __('NOME') }}</th>
                <th>{{ __('DATA DE NASCIMENTO') }}</th>
                <th>{{ __('PARENTESCO') }}</th>
                <th>{{ __('DECLARADO EM IRPF') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($dependentes as $dependente)
                <tr>
                  <td>{{ $dependente->nome }}</td>
                  <td>{{ $dependente->data_nascimento->format('d/m/Y') }}</td>
                  <td>{{ $dependente->parentesco }}</td>
                  <td>{{ $dependente->declarar_em_irpf ? 'Sim' : 'Não' }}</td>
                  <td>
                    <a href="{{ route('clerigos.perfil.dependentes.edit', $dependente) }}" class="btn btn-primary btn-sm btn-rounded bs-tooltip" title="{{ __('Editar dependente') }}">
                      <x-bx-edit/>
                    </a>
                    
                    <form action="{{ route('clerigos.perfil.dependentes.delete', $dependente) }}" method="POST" style="display: inline-block;" id="form_delete_dependente_{{ $dependente->id }}">
                      @csrf
                      @method('DELETE')
                      <button 
                          type="button"
                          class="btn btn-danger btn-sm btn-rounded bs-tooltip btn-confirm-delete" 
                          title="{{ __('Excluir dependente') }}" 
                          data-form-id="form_delete_dependente_{{ $dependente->id }}">
                        <x-bx-trash/>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">{{ __('Ainda não há dependentes cadastrados.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL DE VISUALIZAÇÃO -->
  <div class="modal fade" tabindex="-1" id="visualizarVisitantesModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content loadable">
        <div class="modal-body" style="min-height: 200px"></div>
      </div>
    </div>
  </div>
@endsection

@section('extras-scripts')
  <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
  <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
  <script src="{{ asset('custom/js/imw_datatables.js') }}?time={{ time() }}"></script>
  <script src="{{ asset('perfil/clerigos/dependentes/js/index.js') }}?time={{ time() }}"></script>
@endsection
