<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Carteirinhas de Membros') }}</title>
    <style>
        @page {
            margin: 8mm;
            size: A4 portrait;
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
            width: 85.6mm;
            height: 54mm;
            margin: 0 4mm 5mm 0;
            overflow: hidden;
            border: .35mm solid #d5dbe8;
            border-radius: 3mm;
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
            top: 5mm;
            left: 6mm;
            width: 37mm;
            height: auto;
        }

        .member-card__slogan {
            position: absolute;
            top: 4.5mm;
            right: 5mm;
            width: 22mm;
            color: #073978;
            font-size: 5.2px;
            font-style: italic;
            font-weight: bold;
            line-height: 1.15;
            text-align: right;
            text-transform: uppercase;
        }

        .member-card__photo {
            position: absolute;
            top: 10.8mm;
            right: 5mm;
            width: 15.8mm;
            height: 21.8mm;
            overflow: hidden;
            border: .55mm solid #173f91;
            border-radius: 2mm;
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
            top: 5.8mm;
            left: 50%;
            width: 5.6mm;
            height: 5.6mm;
            border-radius: 50%;
            background: #c9d3e6;
            transform: translateX(-50%);
        }

        .member-card__photo-placeholder::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 4.5mm;
            width: 10mm;
            height: 7.2mm;
            border-radius: 12mm 12mm 4mm 4mm;
            background: #c9d3e6;
            transform: translateX(-50%);
        }

        .member-card__qr {
            position: absolute;
            top: 35mm;
            right: 6mm;
            width: 15mm;
            height: 15mm;
            padding: 0;
            background: transparent;
        }

        .member-card__qr img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .member-card__title {
            position: absolute;
            top: 22.4mm;
            left: 6mm;
            width: 55mm;
            margin: 0;
            color: #173f91;
            font-size: 10.8px;
            font-style: italic;
            font-weight: bold;
            letter-spacing: 1.7px;
            text-align: center;
            text-transform: uppercase;
        }

        .member-card__title-line {
            position: absolute;
            top: 26.4mm;
            left: 10mm;
            width: 47mm;
            height: 3mm;
            object-fit: fill;
        }

        .member-card__church {
            position: absolute;
            top: 29.2mm;
            left: 6mm;
            width: 55mm;
            color: #173f91;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: .7px;
            line-height: 1;
            text-align: center;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .member-card__field {
            position: absolute;
            min-height: 7.2mm;
            border: .45mm solid #173f91;
            border-radius: 1mm;
            background: rgba(255, 255, 255, .95);
            padding: .6mm 1.2mm .5mm;
        }

        .member-card__field--birth {
            top: 33.6mm;
            left: 4mm;
            width: 24mm;
        }

        .member-card__field--role {
            top: 33.6mm;
            left: 31.8mm;
            width: 26.8mm;
        }

        .member-card__field--name {
            top: 42.3mm;
            left: 4mm;
            width: 54.6mm;
        }

        .member-card__label {
            display: block;
            margin-bottom: .2mm;
            color: #1a2f65;
            font-size: 4.2px;
            font-style: italic;
            font-weight: bold;
        }

        .member-card__value {
            display: block;
            width: 100%;
            color: #3f4652;
            font-size: 6px;
            line-height: 1.15;
            text-align: left;
        }

        .member-card__value--name {
            font-size: 7px;
        }

        .member-card__footer {
            position: absolute;
            right: 3.5mm;
            bottom: 1.5mm;
            left: 3.5mm;
            color: #fff;
            font-size: 5.2px;
            font-weight: bold;
            text-align: center;
            text-shadow: 0 1px 1px rgba(0, 0, 0, .35);
            white-space: nowrap;
        }

        .member-card__footer-text {
            display: inline-block;
            padding: .35mm 1.2mm;
            border-radius: 1.2mm;
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
        @foreach($membros->chunk(8) as $pageMembers)
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
                        @if($titleLine)
                            <img src="{{ $titleLine }}" class="member-card__title-line" alt="">
                        @endif
                        <div class="member-card__church">{{ \Illuminate\Support\Str::upper($igrejaLogada ?: __('Igreja Metodista Wesleyana')) }}</div>

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
                            <span class="member-card__footer-text">{{ __('Esse documento vale pelo período de 4 anos a partir de sua impressão') }} {{ now()->format('d/m/Y') }}</span>
                        </div>
                    </section>
                @endforeach
            </div>
        @endforeach
    @endif
</body>
</html>
