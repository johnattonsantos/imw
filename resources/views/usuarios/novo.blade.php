@extends('template.layout')
@section('extras-css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />

<style>
    .swal2-popup .swal2-styled.swal2-cancel {
        color: white !important;
    }
</style>

@endsection
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Usuários', 'url' => route('usuarios.index'), 'active' => false],
        ['text' => 'Incluir', 'url' => '', 'active' => true],
    ]">
</x-breadcrumb>
@endsection

@include('extras.alerts')
@section('content')
<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Incluir Usuário') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form autocomplete="off" class="form-horizontal" method="POST" id="formCreatePaciente"
                action="{{ route('usuarios.store') }}">
                @csrf
                <div class="row mb-1">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('* E-mail') }}</label>
                            <div class="input-group">
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" autocomplete="off"
                                    value="{{ old('email_hidden') }}" @if(old('email_hidden')) disabled @endif />
                                <input type="hidden" name="email_hidden" id="email_hidden"
                                    value="{{ old('email_hidden') }}" />
                                <button type="button" id="checkEmailButton" class="btn btn-secondary">
                                    <i class="fa fa-search"></i> {{ __('Analisar') }}
                                </button>
                            </div>
                            @error('email')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="ocultar" hidden>
                    <div class="row mt-4">
                        <div class="col-lg-4" id="col-perfil">
                            <div class="form-group">
                                <label>{{ __('* Perfil de Acesso') }}</label>
                                <select class="form-control @error('perfil_id') is-invalid @enderror" name="perfil_id"
                                    required>
                                    <option value="">{{ __('Selecione um perfil') }}</option>
                                    @foreach ($perfis as $perfil)
                                        <option value="{{ $perfil->id }}" @if(old('perfil_id') == $perfil->id) selected
                                        @endif>{{ $perfil->nome }}</option>
                                    @endforeach
                                </select>
                                @error('perfil_id')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6" id="col-nome">
                            <div class="form-group">
                                <label>{{ __('* Nome Completo') }}</label>
                                <div class="controls">
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" minlength="8"
                                        autocomplete="off" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-lg-5" id="col-cpf">
                            <div class="form-group">
                                <label>{{ __('* CPF') }}</label>
                                <div class="input-group">
                                    <input type="text" name="cpf" id="cpf"
                                        class="form-control @error('cpf') is-invalid @enderror" autocomplete="off"
                                        value="{{ old('cpf') }}" />
                                </div>
                                @error('cpf')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-5" id="col-telefone">
                            <div class="form-group">
                                <label>{{ __('* Telefone') }}</label>
                                <div class="input-group">
                                    <input type="text" name="telefone" id="telefone"
                                        class="form-control @error('telefone') is-invalid @enderror" autocomplete="off"
                                        value="{{ old('telefone') }}" />
                                </div>
                                @error('telefone')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-5" id="col-senha">
                            <div class="form-group">
                                <label>{{ __('* Senha') }}</label>
                                <div class="controls">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password" />
                                    @error('password')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group" id="col-confirmar-senha">
                                <label>{{ __('* Confirmar Senha') }}</label>
                                <div class="controls">
                                    <input type="password" name="password_confirmation" id="confirmPassword"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        autocomplete="new-password" />
                                    @error('password_confirmation')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4" id="check_clerigo_container" {{ old('pessoa_id') ? '' : 'hidden' }}>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="n-chk">
                                    <label class="new-control new-checkbox checkbox-outline-success ">
                                        <input id="chk_clerigo_id" data-clerigoId="{{ old('pessoa_id') }}"
                                            name="chk_clerigo_id" type="checkbox" class="new-control-input" {{ old('pessoa_id') ? 'checked' : '' }}> <span
                                            class="new-control-indicator"></span>
                                        <p>{{ __('Vincular Vincular Clérigo(a)') }} <span id="clerigo_nome"
                                                class="font-weight-bold font-italic">
                                                {{ old('pessoa_id') ? \App\Models\PessoasPessoa::find(old('pessoa_id'))->nome : '' }}
                                            </span> {{ __('ao cadastro?') }}</p>
                                    </label>
                                    <input type="hidden" name="pessoa_id" id="pessoa_id" value="{{ old('pessoa_id') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="text" name="tipo" id="tipo" hidden />
                <br><br>
                <div class="ocultar" hidden>
                    <button type="button" id="btn-reset" class="btn btn-secondary btn-rounded mr-2">{{ __('Resetar') }}</button>
                    <button type="submit" id="btn-salvar" class="btn btn-primary btn-rounded">{{ __('Salvar') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#telefone').mask('(00) 00000-0000');
        $('#cpf').mask('000.000.000-00');

        $('#email').on('input', function () {
            $('#email_hidden').val($(this).val());
        });

        $('#btn-reset').on('click', function () {
            location.reload();
        });

        function validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }

        function analyzeEmail() {
            var email = $('#email').val();
            if (email) {

                $.ajax({
                    url: '{{ route('usuarios.checkEmail') }}',
                    type: 'GET',
                    data: {
                        email: email
                    },
                    success: function (response) {
                        $('#email').prop('disabled', true);
                        $('.ocultar').removeAttr('hidden');
                        if (response.exists) {
                            if (response.context === 'institution') {
                                Swal.fire({
                                    title: 'Usuário já vinculado',
                                    text: 'Este usuário já está vinculado a esta instituição, você pode alterar o perfil pela edição do usuário.',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else if (response.context === 'general') {
                                Swal.fire({
                                    title: 'Este usuário esta cadastrado em outra instituição',
                                    text: 'Se deseja vinculá-lo a esta instituição, selecione um perfil e clique no botão "Salvar".',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                });
                                $('#telefone').val(response.user.telefone).prop('disabled', true);
                                $('#cpf').val(response.user.cpf).prop('disabled', true);
                                $('#name').val(response.user.name).prop('disabled', true);
                                $('#password').prop('disabled', true);
                                $('#confirmPassword').prop('disabled', true);
                                $('#tipo').val('vinculo');
                            }
                        } else {
                            $('#tipo').val('cadastro');
                        }

                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });

            }
        }


        $('#checkEmailButton').on('click', function () {
            var email = $('#email').val();
            // Verificar se o email possui pelo menos 4 caracteres e está no padrão de email
            if (email.length >= 4 && validateEmail(email)) {
                analyzeEmail();
            } else {
                // Exibir mensagem de erro ou tomar outra ação apropriada
                alert('Formato de e-mail inválido.');
            }
        });

        // Chamando analyzeEmail() novamente em caso de erro de validação
        @if ($errors->any())
            analyzeEmail();
            $('.ocultar').removeAttr('hidden');
        @endif

    });
</script>
<script src="{{ asset('usuarios/js/vincula-clerigo.js') }}?t={{ time() }}"></script>
@endsection