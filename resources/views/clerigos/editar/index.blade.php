@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Clérigos', 'url' => '/clerigos', 'active' => false],
        ['text' => 'Editar', 'url' => '*', 'active' => true],
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

        .tr-green>td {
            color: green !important;
        }

        .tr-red>td {
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="d-flex flex-column align-items-start justify-content-start m-0 p-0" style="list-style: none">

                    <li>{{ $errors->first() }}</li>

                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('clerigos.update', ['id' => $clerigo->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <!-- conteudo -->
                    <div class="widget-content widget-content-area border-top-tab">
                        <ul class="nav nav-tabs mb-3 mt-3" id="borderTop" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="border-top-dados-pessoais" data-toggle="tab"
                                    href="#border-top-dados-pessoal" role="tab"
                                    aria-controls="border-top-dados-pessoais" aria-selected="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    Dados
                                    Clérigos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-enderecos" data-toggle="tab" href="#border-top-endereco"
                                    role="tab" aria-controls="border-top-endereco" aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                        </path>
                                    </svg>
                                    Endereço
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-registros-geral" data-toggle="tab"
                                    href="#border-top-registro-geral" role="tab"
                                    aria-controls="border-top-registro-geral" aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    Registro
                                    Geral
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-pisp-paseps" data-toggle="tab"
                                    href="#border-top-pisp-pasep" role="tab" aria-controls="border-top-pisp-pasep"
                                    aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    Pis/Pasep
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-carteira-trabalhos" data-toggle="tab"
                                    href="#border-top-carteira-trabalho" role="tab"
                                    aria-controls="border-top-carteira-trabalho" aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    Carteira de Trabalho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-titulos-eleitor" data-toggle="tab"
                                    href="#border-top-titulo-eleitor" role="tab"
                                    aria-controls="border-top-titulo-eleitor" aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    Título de Eleitor
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="border-top-habilitacoes" data-toggle="tab"
                                    href="#border-top-habilitacao" role="tab" aria-controls="border-top-habilitacao"
                                    aria-selected="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    Carteira de
                                    Habilitação
                                </a>
                            </li>

                        </ul>
                        <div class="tab-content" id="borderTopContent">
                            @include('clerigos.editar.tab-dados-pessoais')
                            @include('clerigos.editar.tab-endereco')
                            @include('clerigos.editar.tab-registro-geral')
                            @include('clerigos.editar.tab-pisp-pasep')
                            @include('clerigos.editar.tab-carteira-trabalho')
                            @include('clerigos.editar.tab-habilitacao')
                            @include('clerigos.editar.tab-titulo-eleitor')

                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-4" style="margin-top: -25px !important;">
                <button type="submit" title="Salvar" class="btn btn-primary btn-lg ml-4">Atualizar</button>
            </div>

        </form>
    </div>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/fullcalendar/moment.min.js') }}"></script>
    <script>
        // Funcionalidade de preenchimento automático de endereço pelo CEP
        $('#cep').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length != 8) {
                return;
            }
            $.getJSON('/api/cep/' + cep, function(data) {
                if (!("erro" in data)) {
                    $('#endereco').val(data.logradouro);
                    // Preencha os outros campos de endereço aqui, se necessário
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                } else {
                    toastr.warning('CEP não encontrado.');
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>
    <script>
        $('#upload-picture').click(function (event) {
            event.preventDefault();
            $('#upload-picture-input').click();
        });

        $('#upload-picture-input').change(function (e) {
             const file = e.target.files[0];
                const fileName = file.name;
                const extension = fileName.substring(fileName.lastIndexOf('.') + 1)
                if(extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'webp'){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        $('#showImage').attr('src',e.target.result);
                    }
                    reader.readAsDataURL(e.target.files['0']);
                }else{
                    toastr.warning('Formato de arquivo inválido.');
                    return false;
                }   

        });
        
        $('#delete-picture').click(function (event) {
            event.preventDefault();
            $('#showImage').attr('src', "{{ asset('theme/images/sem-foto.jpg') }}");
            $('#upload-picture-input').val('');
        });


        document.addEventListener('DOMContentLoaded', function() {
            Inputmask("999.999.999-99").mask(document.getElementById("cpf"));
            Inputmask("99999-999").mask(document.getElementById("cep"));
            Inputmask("(99) 99999-9999").mask(document.getElementById("telefone_preferencial"));
            Inputmask("(99) 99999-9999").mask(document.getElementById("telefone_alternativo"));
            Inputmask("9999 9999 9999").mask(document.getElementById("titulo_eleitor"));
            Inputmask("99.999.999-9").mask(document.getElementById("identidade"));
        });

    </script>
    @stack('tab-scripts')
@endsection
