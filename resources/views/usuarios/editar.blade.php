@extends('template.layout')

@section('extras-css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Usuários', 'url' => route('usuarios.index'), 'active' => false],
        ['text' => 'Editar', 'url' => '', 'active' => true],
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
                        <h4>{{ __('Editar Usuário') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form autocomplete="off" class="form-horizontal" method="POST" id="formEditarUsuario"
                    action="{{ route('usuarios.update', $id) }}">
                    @csrf
                    <div class="row mb-1">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('* E-mail') }}</label>
                                <div class="controls">
                                    <input type="email" name="email_hidden" id="email_hidden" class="form-control @error('email') is-invalid @enderror"
                                        autocomplete="off" value="{{ $user->email }}" disabled/>

                                    <input type="hidden" name="email" id="email" value="{{ $user->email }}"/>

                                    @error('email')
                                         <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-4" id="col-perfil">
                            <div class="form-group">
                                <label>{{ __('* Perfil de Acesso') }}</label>
                                <select class="form-control @error('perfil_id') is-invalid @enderror" name="perfil_id">
                                    <option value="">{{ __('Selecione um perfil') }}</option>

                                    @php
                                    $perfisInstituicao = $user->perfilUser;
                                    @endphp

                                    @foreach ($perfisInstituicao as $index => $perfilUser)
                                        @if ($perfilUser->instituicao_id == session()->get('session_perfil')->instituicao_id)
                                            @foreach ($perfis as $perfil)
                                                <option value="{{ $perfil->id }}"
                                                    {{ $perfilUser->perfil_id == $perfil->id ? 'selected' : '' }}>
                                                    {{ $perfil->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                @error('perfil_id')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('* Nome completo') }}</label>
                                <div class="controls">
                                    <input type="text" name="name_hidden" id="name" class="form-control"  value="{{ $user->name }}" disabled>
                                    <input type="hidden" name="name" id="name" class="form-control" value="{{ $user->name }}">

                                </div>
                            </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-lg-5" id="col-perfil">
                            <div class="form-group">
                                <label>{{ __('* CPF') }}</label>
                                <div class="input-group">
                                    <input type="text" name="cpf" id="cpf"
                                        class="form-control @error('cpf') is-invalid @enderror" autocomplete="off"
                                        value="{{ $user->cpf }}" />
                                </div>
                                @error('cpf')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-5" id="col-perfil">
                            <div class="form-group">
                                <label>{{ __('* Telefone') }}</label>
                                <div class="input-group">
                                    <input type="text" name="telefone" id="telefone"
                                        class="form-control @error('telefone') is-invalid @enderror" autocomplete="off"
                                        value="{{ $user->telefone }}" />
                                </div>
                                @error('telefone')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4" id="check_clerigo_container" {{ !$user->pessoa_id ? 'hidden' : '' }}>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="n-chk">
                                    <label class="new-control new-checkbox checkbox-outline-success ">
                                        <input type="checkbox" {{ $user->pessoa_id ? 'checked' : '' }} id="chk_clerigo_id" data-clerigoId="{{ $user->pessoa_id }}" name="chk_clerigo_id"  class="new-control-input">
                                        <span class="new-control-indicator"></span>
                                        <p>{{ __('Vincular Clérigo(a)') }} <span id="clerigo_nome" class="font-weight-bold font-italic">{{ optional($user->pessoa)->nome }}</span> {{ __('ao cadastro?') }}</p>
                                    </label>
                                    <input type="hidden" name="pessoa_id" id="pessoa_id" value="{{ $user->pessoa_id }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5" id="col-senha">
                            <div class="form-group">
                                <label>{{ __('* Senha') }} </label> <button type="button"  class="btn btn-danger" id="editar-senha" style="float: right;">{{ __('Editar Senha') }}</button>
                                <div class="controls">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password" disabled />
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
                                        autocomplete="new-password" disabled />
                                    @error('password_confirmation')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <button type="submit" class="btn btn-primary btn-rounded" id="atualizar">{{ __('Atualizar') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script>
        $(document).ready(function() {
            $('#telefone').mask('(00) 00000-0000');
            $('#cpf').mask('000.000.000-00');
        });
        $('#editar-senha').on('click', function () {
           $('#password').prop('disabled', false)
        });
        $('#password').on('click', function () {
           $('#confirmPassword').prop('disabled', false)
        });
    </script>
    <script src="{{ asset('usuarios/js/vincula-clerigo.js') }}?t={{ time() }}"></script>
@endsection


