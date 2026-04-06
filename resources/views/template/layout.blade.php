<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/assets/img/favicon.ico') }}" />
    <link href="{{ asset('theme/assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('theme/assets/js/loader.js') }}"></script>
    <script src="{{ asset('theme/assets/js/libs/jquery-3.1.1.js') }}"></script>

    <!-- DATEPICKER -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- MASK -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="{{ asset('theme/assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('theme/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('theme/assets/css/components/tabs-accordian/custom-tabs.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- ALERT TOASTR -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="{{ asset('theme/assets/css/elements/miscellaneous.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/breadcrumb.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/tooltip.css') }}" rel="stylesheet" type="text/css" />
    <!--  END CUSTOM STYLE FILE  -->
    <!-- font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
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

    @yield('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        .menu-heading {
            margin-top: -20px !important;
        }

        .logo {
            margin-top: 20px;
            margin-bottom: 20px;
            margin-left: 40px;
            margin-right: auto;
            display: block;
            /* Para garantir que o margin-left e margin-right funcionem */
            width: 130px;
            /* Define a largura do logotipo */
            height: auto;
            /* Mantém a proporção da imagem */
        }


        .user-info {
            padding: 15px;
            margin-bottom: 20px;
        }

        .user-photo img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-block;
        }

        .user-details {
            text-align: center;
            font-size: 14px;
        }

        .active-menu-item {
            background-color: #4361ee;
            color: white !important;
        }

        .active-menu-item svg {
            stroke: #fff;
            /* Muda a cor do ícone para branco */
        }

        .submenu-fixo span{
            padding: 0 0 0 35px!important; 
           
        }

    #datatable_processing {
        inset: 0;
        border: none;
        margin: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.8);
    }

    #datatable_processing .load-datatable {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

    .table-responsive {
        position: relative;
    }
    .ordenar{
        cursor: pointer;
    }

        img{
    width: 140px;
    vertical-align: middle;
    border-style: none 
}

.field-clear-wrapper {
    position: relative;
}

.field-clear-invalid {
    position: absolute;
    right: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #e7515a;
    font-size: 1.4rem;
    line-height: 1;
    padding: 0;
    cursor: pointer;
    display: none;
    z-index: 4;
}
    </style>
</head>

<body>
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->
    <!-- NAVBAR -->
    @include('template.navbar')
    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        @include('template.menu-sidebar')
        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="widget widget-card-two">
                            <div class="widget-content">
                                @yield('breadcrumb')
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('template.footer')
        </div>

        <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
        <script src="{{ asset('theme/bootstrap/js/popper.min.js') }}"></script>
        <script src="{{ asset('theme/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('theme/assets/js/app.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>
            $(document).ready(function() {
                App.init();
            });
        </script>
        <script src="{{ asset('theme/assets/js/custom.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="{{ asset('theme/assets/js/elements/tooltip.js') }}"></script>

        <script src="{{ asset('theme/plugins/blockui/jquery.blockUI.min.js') }}"></script>
        <script src="{{ asset('theme/plugins/blockui/custom-blockui.js') }}"></script>

        <!-- END GLOBAL MANDATORY SCRIPTS -->
        @yield('extras-scripts')
        <script>
            (function() {
                function limparValorDoCampo($campo) {
                    if (!$campo || !$campo.length) return;

                    const tagName = (($campo.prop('tagName') || '').toLowerCase());
                    if (tagName === 'select') {
                        $campo.val('');
                    } else {
                        $campo.val('');
                    }

                    $campo.removeClass('is-invalid');
                    if (typeof $campo[0].setCustomValidity === 'function') {
                        $campo[0].setCustomValidity('');
                    }

                    $campo.trigger('input').trigger('change').focus();
                }

                function atualizarVisibilidadeBotao($campo) {
                    if (!$campo || !$campo.length) return;
                    const $wrapper = $campo.parent('.field-clear-wrapper');
                    if (!$wrapper.length) return;

                    const $botao = $wrapper.find('.field-clear-invalid');
                    const mostrar = $campo.hasClass('is-invalid');
                    $botao.toggle(mostrar);

                    if (mostrar) {
                        $campo.css('padding-right', '2.3rem');
                    } else {
                        $campo.css('padding-right', '');
                    }
                }

                function bindCampo($campo) {
                    if (!$campo || !$campo.length) return;
                    const tipo = (($campo.attr('type') || '').toLowerCase());
                    if (tipo === 'hidden' || $campo.prop('disabled')) return;

                    if ($campo.closest('.input-group').length) return;

                    if (!$campo.parent().hasClass('field-clear-wrapper')) {
                        $campo.wrap('<div class="field-clear-wrapper"></div>');
                    }

                    const $wrapper = $campo.parent('.field-clear-wrapper');
                    if (!$wrapper.find('.field-clear-invalid').length) {
                        const $botao = $('<button type="button" class="field-clear-invalid" title="Limpar campo" aria-label="Limpar campo">&times;</button>');
                        $botao.on('click', function() {
                            limparValorDoCampo($campo);
                            atualizarVisibilidadeBotao($campo);
                        });
                        $wrapper.append($botao);
                    }

                    $campo.off('input.fieldclearglobal change.fieldclearglobal')
                        .on('input.fieldclearglobal change.fieldclearglobal', function() {
                            atualizarVisibilidadeBotao($(this));
                        });

                    atualizarVisibilidadeBotao($campo);
                }

                function inicializarCamposComErro() {
                    $('input.form-control, textarea.form-control, select.form-control, select.custom-select').each(function() {
                        bindCampo($(this));
                    });
                }

                $(document).ready(function() {
                    inicializarCamposComErro();

                    const observer = new MutationObserver(function(mutations) {
                        let precisaRebind = false;
                        for (const mutation of mutations) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                const $target = $(mutation.target);
                                if ($target.is('input.form-control, textarea.form-control, select.form-control, select.custom-select')) {
                                    bindCampo($target);
                                }
                            }

                            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                                precisaRebind = true;
                            }
                        }

                        if (precisaRebind) {
                            inicializarCamposComErro();
                        }
                    });

                    observer.observe(document.body, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['class']
                    });
                });
            })();
        </script>
        {{--  <script>
            $(document).ready(function() {
                // Restaurar o estado do menu e submenu após a recarga
                const menuAtivo = localStorage.getItem('menuAtivo');
                if (menuAtivo) {
                    $(`a[href="${menuAtivo}"]`).addClass('active-menu-item');
                    // Abre o submenu pai e marca como ativo, se necessário
                    $(`a[href="${menuAtivo}"]`).parents('.collapse').addClass('show').prev('.dropdown-toggle').attr('aria-expanded', 'true');
                }

                // Evento de clique para os itens do menu e submenu
                $('#sidebar .dropdown-toggle, #sidebar .submenu a').click(function(e) {
                    // Salva o href do item clicado
                    const href = $(this).attr('href');

                    // Remover classes ativas anteriores e fechar submenus não ativos
                    $('#sidebar .dropdown-toggle, #sidebar .submenu a').removeClass('active-menu-item');
                    $('#sidebar .collapse').not($(this).parents('.collapse')).removeClass('show').prev('.dropdown-toggle').attr('aria-expanded', 'false');

                    // Adiciona a classe ativa ao item clicado e ao submenu pai, se houver
                    $(this).addClass('active-menu-item');
                    $(this).parents('.collapse').addClass('show').prev('.dropdown-toggle').attr('aria-expanded', 'true');

                    // Armazena o estado no localStorage
                    localStorage.setItem('menuAtivo', href);
                });
            });
        </script> --}}


</body>

</html>
