<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carteiras de Clérigos</title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; font-size: 5.7pt; color: #222; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .grid { border-collapse: collapse; table-layout: fixed; width: 194mm; }
        .slot { width: 97mm; height: 112mm; vertical-align: top; padding: 0 4mm 5mm 0; }
        .card { position: relative; width: 85.6mm; height: 105.6mm; border: .15mm solid #d5dbe8; overflow: hidden; }
        .background { position: absolute; top: 0; left: 0; width: 85.6mm; height: 105.6mm; }
        .field { position: absolute; z-index: 1; }
        .region-top { top: 20.25mm; left: 31.38mm; font-size: 4pt; color: #4361ee; }
        .photo { top: 14.27mm; left: 60.2mm; width: 19.97mm; height: 25.49mm; }
        .qr { top: 22.64mm; left: 6.66mm; width: 17.12mm; height: 17.12mm; background: #fff; }
        .name { top: 51.65mm; left: 6.66mm; width: 49mm; white-space: nowrap; }
        .rol { top: 51.65mm; left: 58.49mm; }
        .cpf { top: 60.87mm; left: 6.66mm; }
        .rg { top: 60.87mm; left: 32.34mm; }
        .birth { top: 60.87mm; left: 58.97mm; }
        .category { top: 70.10mm; left: 6.66mm; }
        .consecration { top: 70.10mm; left: 32.91mm; }
        .ordination { top: 70.10mm; left: 58.97mm; }
        .validity { top: 80.84mm; left: 12.36mm; }
        .contact { top: 80.84mm; left: 42.8mm; font-size: 4.8pt; width: 38mm; }
        .superintendent { top: 93.02mm; left: 39.94mm; width: 36.62mm; height: 3.24mm; line-height: 3.24mm; text-align: center; font-weight: bold; background: #fff; font-size: 5pt; }
        .superintendent-region { top: 96.4mm; left: 39.94mm; width: 38.52mm; height: 3.5mm; line-height: 2.8mm; text-align: center; background: #fff; font-size: 4.5pt; font-style: italic; }
        .region-bottom { top: 97.96mm; left: 22.83mm; font-size: 4pt; color: #4361ee; }
    </style>
</head>
<body>
@foreach ($carteiras->chunk(4) as $pagina)
    <div class="page">
        <table class="grid">
            @foreach ($pagina->values()->chunk(2) as $linha)
                <tr>
                    @foreach ($linha as $carteira)
                        @php
                            $regiao = trim($carteira['nome_regiao_formatado'].' Eclesiástica');
                            $cpf = preg_replace('/\D/', '', (string) ($carteira['cpf'] ?? ''));
                            $cpf = strlen($cpf) === 11 ? substr($cpf, 0, 3).'.'.substr($cpf, 3, 3).'.'.substr($cpf, 6, 3).'-'.substr($cpf, 9, 2) : $cpf;
                        @endphp
                        <td class="slot">
                            <div class="card">
                                <img class="background" src="{{ $background }}" alt="">
                                <div class="field region-top">{{ $regiao }}</div>
                                @if ($carteira['foto_pdf'])<img class="field photo" src="{{ $carteira['foto_pdf'] }}" alt="">@endif
                                <img class="field qr" src="{{ $carteira['qr_code'] }}" alt="QR Code de validação">
                                <div class="field name" style="font-size: {{ mb_strlen($carteira['nome']) > 42 ? '4.5' : '5.7' }}pt">{{ $carteira['nome'] }}</div>
                                <div class="field rol">{{ $carteira['rol'] ?? '' }}</div>
                                <div class="field cpf">{{ $cpf }}</div>
                                <div class="field rg">{{ $carteira['identidade'] ?? '' }}</div>
                                <div class="field birth">{{ !empty($carteira['data_nascimento']) ? \Carbon\Carbon::parse($carteira['data_nascimento'])->format('d/m/Y') : '' }}</div>
                                <div class="field category">{{ !empty($carteira['categoria']) ? mb_convert_case($carteira['categoria'], MB_CASE_TITLE, 'UTF-8') : '' }}</div>
                                <div class="field consecration">{{ !empty($carteira['data_consagracao']) ? \Carbon\Carbon::parse($carteira['data_consagracao'])->format('d/m/Y') : '' }}</div>
                                <div class="field ordination">{{ !empty($carteira['data_ordenacao']) ? \Carbon\Carbon::parse($carteira['data_ordenacao'])->format('d/m/Y') : '' }}</div>
                                <div class="field validity">Validade: {{ \Carbon\Carbon::parse($validade)->format('d/m/Y') }}</div>
                                <div class="field contact">Sede Administrativa: {{ $carteira['telefone_sede_administrativa'] }}</div>
                                <div class="field superintendent">{{ $carteira['superintendente_regional_nome'] }}</div>
                                <div class="field superintendent-region">Superintendente da {{ $regiao }}</div>
                                <div class="field region-bottom">{{ $regiao }}</div>
                            </div>
                        </td>
                    @endforeach
                    @if ($linha->count() === 1)<td class="slot"></td>@endif
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
</body>
</html>
