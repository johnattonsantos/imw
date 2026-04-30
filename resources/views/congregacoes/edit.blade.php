@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Congregações', 'url' => '/congregacao', 'active' => false],
    ['text' => 'Editar congregação', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <style>
    .centralizado {
        display: flex;
        justify-content: center;
        align-items: center;
    }
  </style>
@endsection
@section('content')
@include('extras.alerts-error-all')
@include('extras.alerts')

<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
      <div class="widget-header">
          <div class="row">
              <div class="col-12">
                  <h4>Editar congregação</h4>
              </div>
          </div>
      </div>

      <div class="widget-content widget-content-area">
        <form method="POST" action="{{ route('congregacao.update', $congregacao->id) }}">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="form-group col-12 col-md-8">
              <label for="nome" class="control-label">* Nome</label>
              <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"  minlength="4" value="{{ old('nome', $congregacao->nome) }}" maxlength="100">
              @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-md-4">
              <label for="data_abertura" class="control-label">* Data de abertura</label>
              <input type="date" name="data_abertura" class="form-control @error('data_abertura') is-invalid @enderror"  minlength="4" value="{{ old('data_abertura', $congregacao->data_abertura) }}">
              @error('data_abertura')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12">
              <label for="host_dirigente" class="control-label">* Dirigente</label>
              <input type="text" name="host_dirigente" class="form-control @error('host_dirigente') is-invalid @enderror" value="{{ old('host_dirigente', $congregacao->host_dirigente) }}" maxlength="100">
              @error('host_dirigente')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mt-5">
            <div class="col-12 mb-2">
              <h4 class="text-italic" style="color: #888ea8; font-style:italic">Informações de endereço</h4>
            </div>

            <div class="form-group col-12 col-md-3">
              <label for="cep" class="control-label">* CEP</label>
              <input type="text" name="cep" id="cep" class="form-control @error('cep') is-invalid @enderror" data-mask="00000-000" minlength="4" value="{{ old('cep', $congregacao->cep) }}">
              @error('cep')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-sm-9 col-md-9">
              <label for="endereco" class="control-label">* Endereço</label>
              <input type="text" name="endereco" id="endereco" class="form-control @error('endereco') is-invalid @enderror" value="{{ old('endereco', $congregacao->endereco) }}" maxlength="100">
              @error('endereco')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-sm-3 col-md-2">
              <label for="numero" class="control-label">* Número</label>
              <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero', $congregacao->numero) }}" maxlength="20">
              @error('numero')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-md-10">
              <label for="complemento" class="control-label">Complemento</label>
              <input type="text" name="complemento" class="form-control @error('complemento') is-invalid @enderror" value="{{ old('complemento', $congregacao->complemento) }}" maxlength="30">
              @error('complemento')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-md-6">
              <label for="bairro" class="control-label">* Bairro</label>
              <input type="text" name="bairro" id="bairro" class="form-control @error('bairro') is-invalid @enderror"  minlength="4" value="{{ old('bairro', $congregacao->bairro) }}" maxlength="100">
              @error('bairro')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-sm-7 col-md-6">
              <label for="cidade" class="control-label">* Cidade</label>
              <input type="text" name="cidade" id="cidade" class="form-control @error('cidade') is-invalid @enderror"  minlength="4" value="{{ old('cidade', $congregacao->cidade) }}" maxlength="100">
              @error('cidade')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-sm-5 col-md-4">
              <label for="uf" class="control-label">* UF</label>
              <select name="uf" id="uf" class="form-control @error('uf') is-invalid @enderror" >
                <option value="" {{ old('uf', $congregacao->uf) == '' ? 'selected' : '' }}>Selecione</option>
                @foreach ($ufs as $abrev => $uf)
                  <option value="{{ $abrev }}" {{ old('uf', $congregacao->uf) == $abrev ? 'selected' : '' }}>{{ $uf }}</option>
                @endforeach
              </select>
              @error('uf')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mt-5">
            <div class="col-12 mb-2">
              <h4 class="text-italic" style="color: #888ea8; font-style:italic">Informações de contato</h4>
            </div>

            <div class="form-group col-4 col-md-2 col-xl-2">
              <label for="ddd" class="control-label">DDD</label>
              <input type="number" name="ddd" class="form-control @error('ddd') is-invalid @enderror" value="{{ old('ddd', $congregacao->ddd) }}" max="99">
              @error('numero')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-8 col-md-10 col-lg-7 col-xl-4">
              <label for="telefone" class="control-label">Telefone</label>
              <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" data-mask="00000-0000" value="{{ old('telefone', $congregacao->telefone) }}">
              @error('telefone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-md-6 col-xl-6">
              <label for="email" class="control-label">E-Mail</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $congregacao->email) }}" maxlength="255">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group col-12 col-md-6 col-xl-6">
              <label for="site" class="control-label">Site</label>
              <input type="text" name="site" class="form-control @error('site') is-invalid @enderror" value="{{ old('site', $congregacao->site) }}" maxlength="255">
              @error('site')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group mt-4">
            <button type="submit" title="Salvar" class="btn btn-primary btn-lg">Salvar</button>
          </div>
        </form>
      </div>
  </div>
</div>

@endsection
@section('extras-scripts')
    <script>
        $(document).ready(function() {
            // Aplica a máscara ao campo CEP
            $('#cep').mask('00000-000');

            // Função para buscar endereço pelo CEP
            $("#cep").blur(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if(validacep.test(cep)) {
                        $("#endereco").val("...");
                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");

                        $.getJSON("/api/cep/" + cep, function(dados) {
                            if (!("erro" in dados)) {
                                $("#endereco").val(dados.logradouro);
                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#uf").val(dados.uf);
                            } else {
                                alert("CEP não encontrado.");
                            }
                        });
                    } else {
                        alert("Formato de CEP inválido.");
                    }
                }
            });
        });
    </script>
@endsection
