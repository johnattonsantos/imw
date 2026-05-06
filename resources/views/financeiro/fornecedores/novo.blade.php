@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Financeiro', 'url' => '/', 'active' => false],
        ['text' => 'Fornecedor', 'url' => '/financeiro/fornecedor', 'active' => false],
        ['text' => 'Novo', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection
@include('extras.alerts')
@section('content')
    <div class="container-fluid">

    </div>
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Inserir novo registro</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <!-- Conteúdo -->
                <form autocomplete="off" class="form-horizontal" method="POST" action="{{ route('fornecedor.store') }}">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="cpf_cnpj">* CPF/CPNJ <small>(somente números)</small></label>
                                    <input class="form-control @error('cpf_cnpj') is-invalid @enderror" id="cpf_cnpj"
                                        name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" type="text" required>
                                    @error('cpf_cnpj')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-9 col-md-9 col-sm-6">
                                    <label for="nome">* Nome</label>
                                    <input class="form-control @error('nome') is-invalid @enderror" id="nome"
                                        name="nome" maxlength="100" value="{{ old('nome') }}" type="text"
                                        placeholder="" required>
                                    @error('nome')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                    <label for="email">E-mail</label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" maxlength="100" type="email" value="{{ old('email') }}">
                                    @error('email')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                    <label for="site">Site</label>
                                    <input class="form-control @error('site') is-invalid @enderror" id="site"
                                        name="site" maxlength="100" type="text" value="{{ old('site') }}">
                                    @error('site')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Localização</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="cep">* CEP</label>
                                    <input class="form-control cep @error('cep') is-invalid @enderror" id="cep"
                                        name="cep" maxlength="8" value="{{ old('cep') }}" type="number" required>
                                    @error('cep')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-9 col-md-9 col-sm-12">
                                    <label for="endereco">Logradouro (Rua/Av/Beco)</label>
                                    <input class="form-control @error('endereco') is-invalid @enderror" id="endereco"
                                        name="endereco" maxlength="255" type="text" value="{{ old('endereco') }}">
                                    @error('endereco')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="numero">Número</label>
                                    <input class="form-control @error('numero') is-invalid @enderror" id="numero"
                                        name="numero" maxlength="20" type="number" value="{{ old('numero') }}">
                                    @error('numero')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="complemento">Complemento</label>
                                    <input class="form-control @error('complemento') is-invalid @enderror" id="complemento"
                                        name="complemento" maxlength="255" value="{{ old('complemento') }}" type="text">
                                    @error('complemento')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-6 col-md-6 col-sm-6">
                                    <label for="bairro">Bairro</label>
                                    <input class="form-control @error('bairro') is-invalid @enderror" id="bairro"
                                        name="bairro" maxlength="255" value="{{ old('bairro') }}" type="text">
                                    @error('bairro')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                    <label for="cidade">Cidade</label>
                                    <input class="form-control @error('cidade') is-invalid @enderror" id="cidade"
                                        name="cidade" maxlength="255" value="{{ old('cidade') }}" type="text">
                                    @error('cidade')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-6">
                                    <label for="uf">UF</label>
                                    <select class="form-control select2 @error('uf') is-invalid @enderror"
                                        data-bs-toggle="select2" name="uf" id="uf">
                                        <option value="" disabled {{ old('uf') == '' ? 'selected' : '' }}>Nenhum
                                        </option>
                                        <option value="AC" {{ old('uf') == 'AC' ? 'selected' : '' }}>Acre</option>
                                        <option value="AL" {{ old('uf') == 'AL' ? 'selected' : '' }}>Alagoas
                                        </option>
                                        <option value="AP" {{ old('uf') == 'AP' ? 'selected' : '' }}>Amapá
                                        </option>
                                        <option value="AM" {{ old('uf') == 'AM' ? 'selected' : '' }}>Amazonas
                                        </option>
                                        <option value="BA" {{ old('uf') == 'BA' ? 'selected' : '' }}>Bahia
                                        </option>
                                        <option value="CE" {{ old('uf') == 'CE' ? 'selected' : '' }}>Ceará
                                        </option>
                                        <option value="DF" {{ old('uf') == 'DF' ? 'selected' : '' }}>Distrito
                                            Federal</option>
                                        <option value="ES" {{ old('uf') == 'ES' ? 'selected' : '' }}>Espírito
                                            Santo</option>
                                        <option value="GO" {{ old('uf') == 'GO' ? 'selected' : '' }}>Goiás
                                        </option>
                                        <option value="MA" {{ old('uf') == 'MA' ? 'selected' : '' }}>Maranhão
                                        </option>
                                        <option value="MT" {{ old('uf') == 'MT' ? 'selected' : '' }}>Mato Grosso
                                        </option>
                                        <option value="MS" {{ old('uf') == 'MS' ? 'selected' : '' }}>Mato Grosso
                                            do Sul</option>
                                        <option value="MG" {{ old('uf') == 'MG' ? 'selected' : '' }}>Minas Gerais
                                        </option>
                                        <option value="PA" {{ old('uf') == 'PA' ? 'selected' : '' }}>Pará</option>
                                        <option value="PB" {{ old('uf') == 'PB' ? 'selected' : '' }}>Paraíba
                                        </option>
                                        <option value="PR" {{ old('uf') == 'PR' ? 'selected' : '' }}>Paraná
                                        </option>
                                        <option value="PE" {{ old('uf') == 'PE' ? 'selected' : '' }}>Pernambuco
                                        </option>
                                        <option value="PI" {{ old('uf') == 'PI' ? 'selected' : '' }}>Piauí
                                        </option>
                                        <option value="RJ" {{ old('uf') == 'RJ' ? 'selected' : '' }}>Rio de
                                            Janeiro</option>
                                        <option value="RN" {{ old('uf') == 'RN' ? 'selected' : '' }}>Rio Grande do
                                            Norte</option>
                                        <option value="RS" {{ old('uf') == 'RS' ? 'selected' : '' }}>Rio Grande do
                                            Sul</option>
                                        <option value="RO" {{ old('uf') == 'RO' ? 'selected' : '' }}>Rondônia
                                        </option>
                                        <option value="RR" {{ old('uf') == 'RR' ? 'selected' : '' }}>Roraima
                                        </option>
                                        <option value="SC" {{ old('uf') == 'SC' ? 'selected' : '' }}>Santa
                                            Catarina</option>
                                        <option value="SP" {{ old('uf') == 'SP' ? 'selected' : '' }}>São Paulo
                                        </option>
                                        <option value="SE" {{ old('uf') == 'SE' ? 'selected' : '' }}>Sergipe
                                        </option>
                                        <option value="TO" {{ old('uf') == 'TO' ? 'selected' : '' }}>Tocantins
                                        </option>
                                    </select>
                                    @error('uf')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                    <label for="pais">* Pais</label>
                                    <input class="form-control @error('pais') is-invalid @enderror" id="pais"
                                        name="pais" maxlength="20" value="{{ old('pais') }}" type="text"
                                        required>
                                    @error('pais')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="telefone">Telefone</label>
                                    <input class="form-control telefone @error('telefone') is-invalid @enderror"
                                        id="telefone" name="telefone" maxlength="20" value="{{ old('telefone') }}"
                                        type="text" placeholder="">
                                    @error('telefone')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3 col-lg-3 col-md-3 col-sm-6">
                                    <label for="celular">Celular*</label>
                                    <input class="form-control celular @error('celular') is-invalid @enderror"
                                        id="celular" name="celular" maxlength="20" value="{{ old('celular') }}"
                                        type="text" placeholder="" required>
                                    @error('celular')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="submit" value="Salvar" class="btn btn-primary btn-lg mt-3">
            </div>
            </form>
            <!-- Fim Conteúdo -->
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#telefone').mask('(00) 0000-0000');
            $('#celular').mask('(00) 00000-0000');
        });
    </script>

    <script>
        var options = {
            onKeyPress: function(cpf, ev, el, op) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                $('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
            }
        }

        $('#cpf_cnpj').length > 11 ? $('#cpf_cnpj').mask('00.000.000/0000-00', options) : $('#cpf_cnpj').mask(
            '000.000.000-00#', options);
    </script>
    
    <script>
        $(document).ready(function() {
            function limpa_formulário_cep() {
                $("#endereco").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
                $("#pais").val("");
            }

            $("#cep").blur(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if(validacep.test(cep)) {
                        $("#endereco").val("...");
                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");
                        $("#pais").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("/api/cep/" + cep, function(
                            dados) {

                            if (!("erro" in dados)) {
                                $("#endereco").val(dados.logradouro);
                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#uf").val(dados.uf);
                                $("#pais").val("Brasil");
                            } else {
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    }
                    else {
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } else {
                    limpa_formulário_cep();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#site').mask('https://www.*****************', {
                translation: {
                    '*': {
                        pattern: /[a-zA-Z0-9-.]/,
                        optional: true
                    }
                }
            });
        });
    </script>
@endsection
