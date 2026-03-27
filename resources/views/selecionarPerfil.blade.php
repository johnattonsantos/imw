<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/assets/img/favicon.ico') }}" />
    <script src="{{ asset('theme/assets/js/libs/jquery-3.1.1.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="{{ asset('theme/assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('theme/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/elements/miscellaneous.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/tooltip.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    @php
        $user = Auth::user();
        $firstName = '';
        $lastName = '';
        if ($user) {
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
        }
    @endphp
    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
        .logo {
            display: block;
            margin: 0 auto; /* Centralizar o logotipo */
            max-width: 40%; /* Garantir que a imagem não ultrapasse o contêiner */
    
        }
    </style>

    @include('extras.alerts')

    @section('extras-scripts')
        <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
        <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    @endsection
</head>

<body>
    <section class="row flexbox-container justify-content-center align-items-center mt-3">
        <div class="col-xl-8 col-md-8 col-8">
            <div >
                <div class="card-content">
                    <img src="{{ asset('auth/images/login.png') }}" alt="Logotipo" class="logo mt-4 p-3">
                    <div class="card-body text-center">
                        <h5 class="mb-4"><b>Olá {{ $firstName }} {{ $lastName }}, você precisa selecionar um perfil de acesso:</b></h5>
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($perfils->isEmpty())
                            <p>Nenhum perfil vinculado a este usuário.</p>
                        @else
                            <div class="row justify-content-center align-items-center">
                                @foreach ($perfils as $perfil)
                                    <div class="col-lg-4 col-md-6 mb-4 ">
                                        <div class="card">
                                            <div class="card-header bg-primary">
                                                <h6 class="text-white text-bold-600"> {{ $perfil->instituicao_nome }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <h6>{{ $perfil->perfil_nome }}</h6>
                                            </div>
                                            <div class="card-footer text-center">
                                                <form action="{{ route('postPerfil') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="instituicao_id"
                                                        value="{{ $perfil->instituicao_id }}">
                                                    <input type="hidden" name="instituicao_nome"
                                                        value="{{ $perfil->instituicao_nome }}">
                                                    <input type="hidden" name="perfil_id"
                                                        value="{{ $perfil->perfil_id }}">
                                                    <input type="hidden" name="perfil_nome"
                                                        value="{{ $perfil->perfil_nome }}">
                                                    <button type="submit" class="btn btn-dark">
                                                        Selecionar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('theme/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('theme/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>

</html>
