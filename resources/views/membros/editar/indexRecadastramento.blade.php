@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membro', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Editar', 'url' => '#', 'active' => true]
]"></x-breadcrumb>

@endsection
@section('extras-css')
  <link href="{{ asset('theme/assets/css/components/tabs-accordian/custom-tabs.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/plugins/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />
  <style>
    .centralizado {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .tr-green > td {
      color: green !important;
    }

    .tr-red > td {
      color: red !important;
    }

    .loader-sm {
      width: 1.2rem;
      height: 1.2rem;
    }
  </style>
@endsection
@section('content')
@include('extras.alerts-error-all')
@include('extras.alerts')
<div style="margin: 0px 23px;">
    <form method="POST" action="{{ route('recadastramento-membro.update', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
      @csrf
    <div class="row">
      <div class="col-md-12">
          <!-- conteudo -->
          <div class="widget-content widget-content-area border-top-tab">
            <ul class="nav nav-tabs mb-3 mt-3" id="borderTop" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="border-top-dados-pessoais" data-toggle="tab" href="#border-top-dados-pessoal" role="tab" aria-controls="border-top-dados-pessoais" aria-selected="true">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      Dados Pessoais
                    </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="border-top-contatos" data-toggle="tab" href="#border-top-contato" role="tab" aria-controls="border-top-contato" aria-selected="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    Contatos
                  </a>
                </li>
            </ul>
            <div class="tab-content" id="borderTopContent">
                @include('membros.editar.tab-dados-pessoais')
                @include('membros.editar.tab-contato')
            </div>
          </div>
      </div>
    </div>

    <div class="form-group mt-4" style="margin-top: -25px !important;">
      <button type="submit" title="Salvar" class="btn btn-primary btn-lg ml-4">Validar</button>
    </div>

</form>
</div>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/fullcalendar/moment.min.js') }}"></script>
    <script src="{{ asset('membros/js/editar.js') }}"></script>
    <script>
      
      $(document).ready(function(){
          // Validação das datas de formação eclesiástica
          function validateFormacaoEclesiastica() {
              let valid = true;
              const dataNascimento = new Date($('input[name="data_nascimento"]').val());

              
              $('#formacao-tbody tr').each(function() {
                  const $row = $(this);
                  const dataInicioInput = $row.find('input[name="curso-data-inicio[]"]');
                  const dataConclusaoInput = $row.find('input[name="curso-data-conclusao[]"]');

                  const dataInicio = dataInicioInput.val() ? new Date(dataInicioInput.val()) : null;
                  const dataConclusao = dataConclusaoInput.val() ? new Date(dataConclusaoInput.val()) : null;

                  // Limpar mensagens de erro anteriores
                  $row.find('.invalid-feedback').remove();
                  dataInicioInput.removeClass('is-invalid');
                  dataConclusaoInput.removeClass('is-invalid');

                  if (dataInicio && dataInicio < dataNascimento) {
                      valid = false;
                      const errorMsg = $('<div class="invalid-feedback">A data de início não pode ser anterior à data de nascimento.</div>');
                      dataInicioInput.addClass('is-invalid').after(errorMsg);
                  }

                  if (dataConclusao && dataConclusao < dataInicio) {
                      valid = false;
                      const errorMsg = $('<div class="invalid-feedback">A data de conclusão não pode ser anterior à data de início.</div>');
                      dataConclusaoInput.addClass('is-invalid').after(errorMsg);
                  }
              });

              return valid;
          }

          // Validação das datas ministeriais
          function validateMinisterialDates() {
              let valid = true;
              const dataNascimento = new Date($('input[name="data_nascimento"]').val());

              $('#ministerial-tbody tr').each(function() {
                  const $row = $(this);
                  const dataNomeacaoInput = $row.find('input[name="ministerial-nomeacao[]"]');
                  const dataExoneracaoInput = $row.find('input[name="ministerial-exoneracao[]"]');

                  const dataNomeacao = dataNomeacaoInput.val() ? new Date(dataNomeacaoInput.val()) : null;
                  const dataExoneracao = dataExoneracaoInput.val() ? new Date(dataExoneracaoInput.val()) : null;

                  // Limpar mensagens de erro anteriores
                  $row.find('.invalid-feedback').remove();
                  dataNomeacaoInput.removeClass('is-invalid');
                  dataExoneracaoInput.removeClass('is-invalid');

                  if (dataExoneracao && dataExoneracao < dataNomeacao) {
                      valid = false;
                      const errorMsg = $('<div class="invalid-feedback">A data de exoneração não pode ser anterior à data de nomeação.</div>');
                      dataExoneracaoInput.addClass('is-invalid').after(errorMsg);
                  }

                  if (dataExoneracao && dataExoneracao < dataNascimento) {
                      valid = false;
                      const errorMsg = $('<div class="invalid-feedback">A data de exoneração não pode ser anterior à data de nascimento.</div>');
                      dataExoneracaoInput.addClass('is-invalid').after(errorMsg);
                  }

                  if (dataNomeacao && dataNomeacao < dataNascimento) {
                      valid = false;
                      const errorMsg = $('<div class="invalid-feedback">A data de nomeação não pode ser anterior à data de nascimento.</div>');
                      dataNomeacaoInput.addClass('is-invalid').after(errorMsg);
                  }
              });

              return valid;
          }

          $('form').on('submit', function (event) {
              if (!validateFormacaoEclesiastica() || !validateMinisterialDates()) {
                  event.preventDefault();
                  toastr.warning('Por favor, corrija os erros de data antes de enviar.');
              }
          });

          // Funcionalidade de preenchimento automático de endereço pelo CEP
          $('#cep').blur(function(){
              var cep = $(this).val().replace(/\D/g, '');
              if(cep.length != 8){
                  return;
              }
              $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data){
                  if(!("erro" in data)){
                      $('#endereco').val(data.logradouro);
                      // Preencha os outros campos de endereço aqui, se necessário
                      $('#bairro').val(data.bairro);
                      $('#cidade').val(data.localidade);
                      $('#estado').val(data.uf);
                  }else{
                    toastr.warning('CEP não encontrado.');
                  }
              });
          });

          // Funcionalidade de confirmação de deleção
          $('.btn-confirm-delete').on('click', function () {
              const formId = $(this).data('form-id')
              swal({
                  title: 'Deseja realmente apagar os registros deste congregado?',
                  type: 'error',
                  showCancelButton: true,
                  confirmButtonText: "Deletar",
                  confirmButtonColor: "#d33",
                  cancelButtonText: "Cancelar",
                  cancelButtonColor: "#3085d6",
                  padding: '2em'
              }).then(function(result) {
                  if(result.value) document.getElementById(formId).submit()
              })
          });
      });
    </script>
  @stack('tab-scripts')
@endsection
