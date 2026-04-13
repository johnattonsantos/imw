@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Instituições', 'url' => '/instituicoes-regiao', 'active' => false],
        ['text' => 'Novo', 'url' => '#', 'active' => true],
    ]">
    </x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }

        .modal-xl {
            max-width: 90% !important;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
@endsection

@include('extras.alerts')

@section('content')
    <div class="container-fluid" style="background: #fff">
        <div class="widget-header">
            <h4>Dados da instituição</h4>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="py-2 px-3" method="POST" action="{{ route('instituicoes-regiao.store') }}">
            @csrf

            <!-- Dados da Instituição -->
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nome"><span>*</span> Nome</label>
                    <input class="form-control @error('nome') is-invalid @enderror" type="text" id="nome"
                        name="nome" value="{{ old('nome') }}">
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="cnpj"><span>*</span> CNPJ</label>
                    <input class="form-control @error('cnpj') is-invalid @enderror" type="text" id="cnpj"
                        name="cnpj" value="{{ old('cnpj') }}">
                    @error('cnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="data_abertura"><span>*</span> Data de Abertura</label>
                    <input class="form-control @error('data_abertura') is-invalid @enderror" type="date"
                        id="data_abertura" name="data_abertura" value="{{ old('data_abertura') }}"
                        min="1967-01-05"
                        max="{{ date('Y-m-d') }}">
                    @error('data_abertura')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="ativo"><span>*</span> Status</label>
                    <select class="form-control @error('ativo') is-invalid @enderror" id="ativo" name="ativo">
                        <option value="1" {{ old('ativo', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('ativo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="data_encerramento">Data de Encerramento</label>
                    <input class="form-control @error('data_encerramento') is-invalid @enderror" type="date"
                        id="data_encerramento" name="data_encerramento" value="{{ old('data_encerramento') }}"
                        min="2023-01-01" max="{{ date('Y-m-d') }}">
                    @error('data_encerramento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tipo e Instituição Pai -->
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="tipo_instituicao_id"><span>*</span> Tipo da Instituição</label>
                    <select name="tipo_instituicao_id" id="tipo_instituicao_id"
                        class="form-control @error('tipo_instituicao_id') is-invalid @enderror">
                        <option value="" disabled {{ !request('tipo_instituicao_id') ? 'selected' : '' }}>Selecione
                        </option>
                        <option value="1" {{ old('tipo_instituicao_id') == 1 ? 'selected' : '' }}>Igreja</option>
                        <option value="2" {{ old('tipo_instituicao_id') == 2 ? 'selected' : '' }}>Distrito</option>
                        <option value="5" {{ old('tipo_instituicao_id') == 5 ? 'selected' : '' }}>Secretaria Regional
                        </option>
                    </select>
                    @error('tipo_instituicao_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 form-group">
                    <label for="instituicao_pai_id"><span>*</span> Instituição Pai</label>
                    <select name="instituicao_pai_id" id="instituicao_pai_id"
                        class="form-control @error('instituicao_pai_id') is-invalid @enderror">
                        <option value="" disabled selected>Selecione</option>
                        <!-- As opções serão preenchidas dinamicamente pelo JavaScript -->
                    </select>
                    @error('instituicao_pai_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <input type="hidden" name="regiao_id" id="regiao_id" value="23">
            </div>

            <!-- Endereço -->
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="cep"><span>*</span> CEP</label>
                    <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep"
                        name="cep" value="{{ old('cep') }}">
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-7 form-group">
                    <label for="endereco"><span>*</span> Logradouro (Rua/Av/Beco)</label>
                    <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco"
                        name="endereco" value="{{ old('endereco') }}">
                    @error('endereco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2 form-group">
                    <label for="numero"><span>*</span>Número</label>
                    <input type="number" class="form-control @error('numero') is-invalid @enderror" id="numero"
                        name="numero" value="{{ old('numero') }}">
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="bairro"><span>*</span> Bairro</label>
                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro"
                        name="bairro" value="{{ old('bairro') }}">
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" class="form-control @error('complemento') is-invalid @enderror" maxlength="30"
                        id="complemento" name="complemento" value="{{ old('complemento') }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 form-group">
                    <label for="cidade"><span>*</span>Cidade</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade"
                        name="cidade" value="{{ old('cidade') }}">
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 form-group">
                    <label for="uf"><span>*</span> Estado</label>
                    <select class="form-control @error('uf') is-invalid @enderror" id="estado" name="uf">
                        <option value="">Selecione</option>
                        @foreach ($ufs as $key => $value)
                            <option value="{{ $key }}" {{ old('uf') == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                    @error('uf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="pais"><span>*</span> País</label>
                    <input type="text" class="form-control @error('pais') is-invalid @enderror" id="pais"
                        name="pais" value="{{ old('pais') }}">
                    @error('pais')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-1 form-group">
                    <label for="DDD"><span>*</span> DDD</label>
                    <input type="text" maxlength="3" class="form-control p-2 @error('ddd') is-invalid @enderror"
                        id="ddd" name="ddd" value="{{ old('ddd') }}">
                    @error('ddd')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="Telefone"><span>*</span> Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone"
                        name="telefone" value="{{ old('telefone') }}">
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button class="btn btn-primary">Salvar</button>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function() {
            $('#cep').mask('00000.000');
            $('#telefone').mask('00000-0000');
            $('#cnpj').mask('00.000.000/0000-00', {
                reverse: true
            });

            function validarDataAbertura() {
                const minDate = '1967-01-05';
                const valor = $('#data_abertura').val();
                if (valor && valor < minDate) {
                    $('#data_abertura')[0].setCustomValidity('A data de abertura não pode ser anterior a 05/01/1967.');
                } else {
                    $('#data_abertura')[0].setCustomValidity('');
                }
            }

            function toggleDataEncerramento() {
                const inativo = $('#ativo').val() === '0';
                $('#data_encerramento').prop('disabled', !inativo);
                if (!inativo) {
                    $('#data_encerramento').val('');
                }
            }

            $('#data_abertura').on('change blur', validarDataAbertura);
            $('#ativo').on('change', toggleDataEncerramento);
            validarDataAbertura();
            toggleDataEncerramento();
        })
    </script>
    <script src="{{ asset('theme/plugins/fullcalendar/moment.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Função para carregar as instituições pai baseadas no tipo
            function loadInstituicoesPai(tipo_instituicao_id) {
                var instituicoesPai = @json($instituicoes_pai); // Passa o array PHP para o JS

                // Filtra as instituições pai conforme o tipo
                var filteredInstituicoes = instituicoesPai.filter(function(instituicao) {
                    if (tipo_instituicao_id == 1 && instituicao.tipo_instituicao_id == 2 && instituicao.ativo == 1) {
                        return true;
                    }
                    if (tipo_instituicao_id == 2 && instituicao.tipo_instituicao_id == 3 && instituicao.ativo == 1) {
                        return true;
                    }
                    if (tipo_instituicao_id == 5 && instituicao.tipo_instituicao_id == 3 && instituicao.ativo == 1) {
                        return true;
                    }
                    return false;
                });

                // Limpa o select antes de adicionar as novas opções
                $('#instituicao_pai_id').empty();
                $('#instituicao_pai_id').append('<option value="" disabled selected>Selecione</option>');

            // Adiciona as opções filtradas
            filteredInstituicoes.forEach(function(instituicao) {
                $('#instituicao_pai_id').append('<option value="' + instituicao.id + '">' + instituicao.nome + `(${instituicao.instituicao_pai_nome})` + '</option>');
            });
        }

            // Quando o select tipo_instituicao_id for alterado, atualiza o select de instituicao_pai_id
            $('#tipo_instituicao_id').change(function() {
                var tipo_instituicao_id = $(this).val();
                loadInstituicoesPai(tipo_instituicao_id);
            });
        });


        // Preenchimento automático de endereço pelo CEP
        $('#cep').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length != 8) return;

            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                    $('#pais').val('Brasil');
                } else {
                    toastr.warning('CEP não encontrado.');
                }
            });
        });
    </script>
@endsection
