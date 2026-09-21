<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Validar Membro') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(135deg, #eef3ff 0%, #ffffff 48%, #f5f7fb 100%);
            color: #263040;
            font-family: Arial, Helvetica, sans-serif;
        }

        .validation-card {
            width: min(520px, 100%);
            border: 1px solid #d8dfed;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 50px rgba(15, 27, 67, .14);
            overflow: hidden;
        }

        .validation-card__header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 30px;
            background: #173f91;
            color: #fff;
        }

        .validation-card__logo {
            display: block;
            width: 170px;
            height: auto;
            margin: 0;
            padding: 10px 14px;
            border-radius: 12px;
            background: #fff;
            flex: 0 0 auto;
        }

        .validation-card__heading {
            flex: 1;
            min-width: 0;
        }

        .validation-card__title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .validation-card__subtitle {
            margin: 8px 0 0;
            opacity: .9;
            font-size: 14px;
        }

        .validation-card__body {
            padding: 24px;
        }

        .validation-card__row {
            padding: 14px 0;
            border-bottom: 1px solid #edf1f7;
        }

        .validation-card__row:last-child {
            border-bottom: 0;
        }

        .validation-card__label {
            display: block;
            margin-bottom: 5px;
            color: #687185;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .validation-card__value {
            display: block;
            color: #263040;
            font-size: 18px;
            font-weight: 600;
        }

        .validation-card__status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            color: #fff;
            background: #173f91;
            font-size: 14px;
            font-weight: 700;
        }

        .validation-card__status--inactive {
            background: #d94848;
        }

        .validation-card__empty {
            padding: 28px 24px;
            color: #6c7280;
            font-size: 16px;
            line-height: 1.5;
        }

        @media (max-width: 560px) {
            .validation-card__header {
                align-items: flex-start;
                flex-direction: column;
                gap: 16px;
            }

            .validation-card__logo {
                width: min(240px, 100%);
            }
        }
    </style>
</head>
<body>
    <main class="validation-card">
        <header class="validation-card__header">
            <img src="{{ asset('theme/images/logo-evento.png') }}" class="validation-card__logo" alt="{{ __('Igreja Metodista Wesleyana') }}">
            <div class="validation-card__heading">
                <h1 class="validation-card__title">{{ __('Validação de Membro') }}</h1>
                <p class="validation-card__subtitle">{{ __('Igreja Metodista Wesleyana') }}</p>
            </div>
        </header>

        @if($membro)
            <section class="validation-card__body">
                <div class="validation-card__row">
                    <span class="validation-card__label">{{ __('Membro') }}</span>
                    <span class="validation-card__value">{{ $membro->nome }}</span>
                </div>

                <div class="validation-card__row">
                    <span class="validation-card__label">{{ __('Igreja') }}</span>
                    <span class="validation-card__value">{{ $membro->igreja_nome ?: __('Não informada') }}</span>
                </div>

                <div class="validation-card__row">
                    <span class="validation-card__label">{{ __('Pastor') }}</span>
                    <span class="validation-card__value">{{ $membro->pastor_nome ?: __('Não informado') }}</span>
                </div>

                <div class="validation-card__row">
                    <span class="validation-card__label">{{ __('Status do membro') }}</span>
                    <span class="validation-card__status {{ $membro->status === \App\Models\MembresiaMembro::STATUS_INATIVO ? 'validation-card__status--inactive' : '' }}">
                        {{ $membro->status_descricao }}
                    </span>
                </div>
            </section>
        @else
            <div class="validation-card__empty">
                {{ __('Não foi possível validar esta carteirinha. Verifique se o QR Code foi lido corretamente.') }}
            </div>
        @endif
    </main>
</body>
</html>
