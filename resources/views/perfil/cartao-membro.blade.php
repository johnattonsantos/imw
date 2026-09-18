@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Perfil', 'url' => route('perfil.index'), 'active' => false],
        ['text' => 'Cartão de Membro', 'url' => route('perfil.cartao-membro'), 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <style>
        .member-card-page {
            display: flex;
            justify-content: center;
            padding: 32px 12px;
        }

        .member-credential {
            position: relative;
            width: min(760px, 100%);
            min-height: 430px;
            overflow: hidden;
            border: 1px solid #d5dbe8;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 45px rgba(15, 27, 67, .18);
            color: #0e2e73;
            font-family: Arial, Helvetica, sans-serif;
        }

        .member-credential::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('{{ asset('theme/images/fundo-carteirinha.png') }}') center center / cover no-repeat;
            pointer-events: none;
        }

        .member-credential::after {
            content: none;
        }

        .member-credential__top {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 155px;
            gap: 22px;
            padding: 24px 28px 0;
        }

        .member-credential__logo {
            width: min(330px, 100%);
            height: auto;
        }

        .member-credential__slogan {
            margin: 3px 0 0;
            color: #fff;
            font-size: 15px;
            font-style: italic;
            font-weight: 800;
            line-height: 1.15;
            text-align: right;
            text-transform: uppercase;
        }

        .member-credential__photo {
            position: absolute;
            top: 92px;
            right: 30px;
            z-index: 2;
            width: 160px;
            height: 190px;
            border: 4px solid #173f91;
            border-radius: 16px;
            background:
                linear-gradient(135deg, rgba(23, 63, 145, .12), rgba(239, 38, 59, .08)),
                #f3f6fb;
            box-shadow: inset 0 0 0 4px #fff;
        }

        .member-credential__photo::before {
            content: "";
            position: absolute;
            top: 35px;
            left: 50%;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #c7d1e4;
            transform: translateX(-50%);
        }

        .member-credential__photo::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 34px;
            width: 88px;
            height: 60px;
            border-radius: 46px 46px 16px 16px;
            background: #c7d1e4;
            transform: translateX(-50%);
        }

        .member-credential__photo--has-image::before,
        .member-credential__photo--has-image::after {
            content: none;
        }

        .member-credential__photo-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .member-credential__title {
            position: relative;
            z-index: 1;
            margin: 18px 0 12px;
            color: #173f91;
            font-size: 24px;
            font-style: italic;
            font-weight: 800;
            letter-spacing: .08em;
            text-align: center;
            text-transform: uppercase;
        }

        .member-credential__body {
            position: relative;
            z-index: 1;
            width: calc(100% - 225px);
            padding: 0 0 0 32px;
        }

        .member-credential__row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 12px;
        }

        .member-credential__field {
            min-height: 55px;
            border: 4px solid #173f91;
            border-radius: 7px;
            background: rgba(255, 255, 255, .96);
            padding: 5px 12px 7px;
        }

        .member-credential__field--name {
            min-height: 40px;
            margin-bottom: 12px;
            padding-top: 1px;
            padding-bottom: 2px;
        }

        .member-credential__label {
            display: block;
            margin-bottom: 2px;
            color: #1a2f65;
            font-size: 11px;
            font-style: italic;
            font-weight: 800;
        }

        .member-credential__value {
            color: #0094c7;
            display: block;
            width: 100%;
            font-size: 20px;
            font-style: italic;
            font-weight: 500;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
        }

        .member-credential__value--name {
            color: #3f4652;
            font-size: clamp(18px, 3vw, 26px);
            font-style: normal;
            overflow-wrap: anywhere;
            text-align: left;
            text-transform: none;
        }

        .member-credential__value--birth-date {
            color: #3f4652;
            font-style: normal;
            text-align: left;
        }

        .member-credential__value--role {
            color: #3f4652;
            font-style: normal;
            text-align: left;
        }

        .member-card__empty {
            max-width: 760px;
            margin: 32px auto;
        }

        @media (max-width: 767px) {
            .member-credential {
                min-height: 620px;
            }

            .member-credential__top {
                grid-template-columns: 1fr;
                padding-right: 24px;
            }

            .member-credential__slogan {
                text-align: left;
            }

            .member-credential__photo {
                position: relative;
                top: auto;
                right: auto;
                margin: 14px auto 0;
            }

            .member-credential__body {
                width: auto;
                padding: 0 24px;
            }

            .member-credential__row {
                grid-template-columns: 1fr;
            }

        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Cartão de Membro') }}</h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                @if($membro)
                    <div class="member-card-page">
                        <section class="member-credential" aria-label="{{ __('Cartão de Membro') }}">
                            <div class="member-credential__top">
                                <img src="{{ asset('theme/images/logo-evento.png') }}" class="member-credential__logo" alt="{{ __('Igreja Metodista Wesleyana') }}">
                                <p class="member-credential__slogan">"{{ __('Santidade como estilo de vida') }}"</p>
                            </div>

                            <div class="member-credential__photo {{ $membro->foto ? 'member-credential__photo--has-image' : '' }}" aria-hidden="{{ $membro->foto ? 'false' : 'true' }}">
                                @if($membro->foto)
                                    <img src="{{ $membro->foto }}" class="member-credential__photo-image" alt="{{ __('Foto de :name', ['name' => $membro->nome]) }}">
                                @endif
                            </div>

                            <h2 class="member-credential__title">{{ __('Credencial') }}</h2>

                            <div class="member-credential__body">
                                <div class="member-credential__row">
                                    <div class="member-credential__field">
                                        <span class="member-credential__label">{{ __('Nascimento') }}:</span>
                                        <div class="member-credential__value member-credential__value--birth-date">{{ $membro->data_nascimento ? formatDate($membro->data_nascimento) : __('Não informado') }}</div>
                                    </div>

                                    <div class="member-credential__field">
                                        <span class="member-credential__label">{{ __('Função Eclesiástica') }}:</span>
                                        <div class="member-credential__value member-credential__value--role">{{ $membro->funcao_eclesiastica ?: __('Não informado') }}</div>
                                    </div>
                                </div>

                                <div class="member-credential__field member-credential__field--name">
                                    <span class="member-credential__label">{{ __('Nome') }}:</span>
                                    <div class="member-credential__value member-credential__value--name">{{ $membro->nome }}</div>
                                </div>
                            </div>

                        </section>
                    </div>
                @else
                    <div class="alert alert-warning member-card__empty" role="alert">
                        {{ __('Não foi encontrado um membro vinculado ao seu usuário para gerar o cartão.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
