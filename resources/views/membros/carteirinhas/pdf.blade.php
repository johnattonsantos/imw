<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Carteirinhas de Membros') }}</title>
    <style>
        @page {
            margin: 8mm;
            size: A4 landscape;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #263040;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .member-card {
            position: relative;
            display: inline-block;
            width: 137mm;
            height: 77mm;
            margin: 0 2.5mm 6mm 0;
            overflow: hidden;
            border: 1px solid #d5dbe8;
            border-radius: 5mm;
            background: #fff;
            vertical-align: top;
        }

        .member-card__background {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .member-card__logo {
            position: absolute;
            top: 8mm;
            left: 11mm;
            width: 58mm;
            height: auto;
        }

        .member-card__slogan {
            position: absolute;
            top: 7mm;
            right: 7mm;
            width: 30mm;
            color: #073978;
            font-size: 7.2px;
            font-style: italic;
            font-weight: bold;
            line-height: 1.15;
            text-align: right;
            text-transform: uppercase;
        }

        .member-card__photo {
            position: absolute;
            top: 18mm;
            right: 7mm;
            width: 29mm;
            height: 34mm;
            overflow: hidden;
            border: .9mm solid #173f91;
            border-radius: 3mm;
            background: #eef1f8;
        }

        .member-card__photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-card__photo-placeholder {
            width: 100%;
            height: 100%;
            background: #e8eaf3;
            position: relative;
        }

        .member-card__photo-placeholder::before {
            content: "";
            position: absolute;
            top: 9mm;
            left: 50%;
            width: 9mm;
            height: 9mm;
            border-radius: 50%;
            background: #c9d3e6;
            transform: translateX(-50%);
        }

        .member-card__photo-placeholder::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 7mm;
            width: 16mm;
            height: 11mm;
            border-radius: 12mm 12mm 4mm 4mm;
            background: #c9d3e6;
            transform: translateX(-50%);
        }

        .member-card__qr {
            position: absolute;
            top: 54mm;
            right: 13mm;
            width: 15mm;
            height: 15mm;
            padding: .5mm;
            background: #fff;
        }

        .member-card__qr img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .member-card__title {
            position: absolute;
            top: 35mm;
            left: 0;
            width: 100%;
            margin: 0;
            color: #173f91;
            font-size: 13px;
            font-style: italic;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-align: center;
            text-transform: uppercase;
        }

        .member-card__field {
            position: absolute;
            min-height: 9.5mm;
            border: .8mm solid #173f91;
            border-radius: 1.5mm;
            background: rgba(255, 255, 255, .95);
            padding: 1.2mm 2mm .8mm;
        }

        .member-card__field--birth {
            top: 45mm;
            left: 6mm;
            width: 33mm;
        }

        .member-card__field--role {
            top: 45mm;
            left: 46mm;
            width: 43mm;
        }

        .member-card__field--name {
            top: 58mm;
            left: 6mm;
            width: 82mm;
        }

        .member-card__label {
            display: block;
            margin-bottom: .4mm;
            color: #1a2f65;
            font-size: 6.5px;
            font-style: italic;
            font-weight: bold;
        }

        .member-card__value {
            display: block;
            width: 100%;
            color: #3f4652;
            font-size: 9px;
            line-height: 1.15;
            text-align: left;
        }

        .member-card__value--name {
            font-size: 11px;
        }

        .member-card__footer {
            position: absolute;
            right: 5mm;
            bottom: 2.2mm;
            left: 5mm;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            text-shadow: 0 1px 1px rgba(0, 0, 0, .35);
            white-space: nowrap;
        }

        .member-card__footer-text {
            display: inline-block;
            padding: .6mm 2mm;
            border-radius: 2mm;
            background: #fff;
            color: #173f91;
            line-height: 1;
        }

        .empty-message {
            margin-top: 20mm;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    @if($membros->isEmpty())
        <p class="empty-message">{{ __('Nenhum membro ativo encontrado para a seleção informada.') }}</p>
    @else
        @foreach($membros->chunk(4) as $pageMembers)
            <div class="page">
                @foreach($pageMembers as $membro)
                    <section class="member-card">
                        @if($background)
                            <img src="{{ $background }}" class="member-card__background" alt="">
                        @endif

                        @if($logo)
                            <img src="{{ $logo }}" class="member-card__logo" alt="{{ __('Igreja Metodista Wesleyana') }}">
                        @endif

                        <div class="member-card__slogan" style="color: #fff;">"{{ __('Santidade como') }}<br>{{ __('estilo de vida') }}"</div>

                        <div class="member-card__photo">
                            @if($membro->foto_data_uri)
                                <img src="{{ $membro->foto_data_uri }}" alt="{{ __('Foto de :name', ['name' => $membro->nome]) }}">
                            @else
                                <div class="member-card__photo-placeholder"></div>
                            @endif
                        </div>

                        @if($membro->qr_code)
                            <div class="member-card__qr">
                                <img src="{{ $membro->qr_code }}" alt="{{ __('QR Code de validação') }}">
                            </div>
                        @endif

                        <h1 class="member-card__title">{{ __('Credencial') }}</h1>

                        <div class="member-card__field member-card__field--birth">
                            <span class="member-card__label">{{ __('Nascimento') }}:</span>
                            <span class="member-card__value">{{ $membro->data_nascimento ? formatDate($membro->data_nascimento) : __('Não informado') }}</span>
                        </div>

                        <div class="member-card__field member-card__field--role">
                            <span class="member-card__label">{{ __('Função Eclesiástica') }}:</span>
                            <span class="member-card__value">{{ $membro->funcao_eclesiastica ?: __('Não informado') }}</span>
                        </div>

                        <div class="member-card__field member-card__field--name">
                            <span class="member-card__label">{{ __('Nome') }}:</span>
                            <span class="member-card__value member-card__value--name">{{ $membro->nome }}</span>
                        </div>

                        <div class="member-card__footer">
                            <span class="member-card__footer-text">{{ __('Esse documento, vale pelo pedido de 4 anos, a partir de sua impressão') }}</span>
                        </div>
                    </section>
                @endforeach
            </div>
        @endforeach
    @endif
</body>
</html>
