
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RELATÓRIO {{ isset($vinculos) ? $vinculos : '' }} REGIÃO - {{ $regiao_nome }}</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .header .info {
            text-align: left;
            margin-top: 10px;
        }

        .header .info .title {
            font-size: 16px;
            font-weight: bold;
        }

        .header .info .period {
            font-size: 14px;
        }

        .header .date {
            font-size: 10px;
            color: #555;
            text-align: right;
        }

        h4,
        h5 {
            color: #333;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div style="text-align: center; margin-bottom: 1rem;">
        <small style="text-transform: uppercase">
                RELATÓRIO {{ isset($vinculos) ? $vinculos : '' }} REGIÃO - {{ $regiao_nome }}
            </small>
        </div>
    </header>

    <h2>RELATÓRIO {{ isset($vinculos) ? $vinculos : '' }} REGIÃO - {{ $regiao_nome }}</h2>


    <table class="table table-bordered table-striped table-hover mb-4">
      <thead>
          <tr>
              <th width=100px>
                {{ __('DISTRITO') }}
                <span id="distrito-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="distrito-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('IGREJA') }}
                <span id="igreja-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="igreja-down"></i>
                </span>
              </th>
              <th width=55px>
                {{ __('ROL') }}
                <span id="rol-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="rol-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('NOME') }}
                <span id="nome-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="nome-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('TELEFONE') }}
                <span id="telefone-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="telefone-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('SITUAÇÃO') }}
                <span id="situacao-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="situacao-down"></i>
                </span>
              </th>
              <th width=90px>
                {{ __('VÍNCULO') }}
                <span id="vinculo-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="vinculo-down"></i>
                </span>
              </th>
              <th width=120px>
                {{ __('NASCIMENTO') }}
                <span id="nascimento-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="nascimento-down"></i>
                </span>
              </th>
              <th width=110px>{{ __('RECEPÇÃO') }}
                <span id="recepcao-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="recepcao-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('MODO') }}
                <span id="modo-recepcao-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="modo-recepcao-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('EXCLUSÃO') }}
                <span id="exclusao-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="exclusao-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('MODO') }}
                <span id="modo-exclusao-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="modo-exclusao-down"></i>
                </span>
              </th>
              <th width=100px>
                {{ __('LOCAL') }}
                <span id="local-order">
                    <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="local-down"></i>
                </span>
              </th>
          </tr>
      </thead>
      <tbody>
        @forelse ($membros as $membro)
          <tr>
            <td>{{ $membro->distrito_nome }}</td>
            <td>{{ $membro->igreja_nome }}</td>
            <td>{{ $membro->rol_atual ?? 0 }}</td>
            <td>{{ $membro->nome }}</td>
            <td>{{ formatStr($membro->telefone, '## (##) #####-####') }}</td>
            <td>
              @if($membro->vinculo == 'M')
                {{ $membro->status == 'A' ? 'ATIVO' : 'INATIVO' }}
              @else
                {{ $membro->statusText }}
              @endif
            </td>
            <td>{{ $membro->vinculoText }}</td>
            <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
            <td>
              @if($membro->vinculo == 'M')
                  {{ $membro->dt_recepcao ? formatDate($membro->dt_recepcao) : '-' }}
              @else
                {{ optional($membro->created_at)->format('d/m/Y') }}
              @endif
            </td>
            <td>{{ $membro->recepcao_modo ? $membro->recepcao_modo : '-' }}</td>
            <td>
              @if($membro->vinculo == 'M')
                {{ $membro->dt_exclusao ? formatDate($membro->dt_exclusao) : '-'}}
              @else
                {{ optional($membro->deleted_at)->format('d/m/Y') }}
              @endif
            </td>
            <td>{{ $membro->exclusao_modo ? $membro->exclusao_modo : '-' }}</td>
            <td>{{ $membro->congregacao_nome ?? 'SEDE' }}</td>
          </tr>
        @empty
        <tr>
            <td colspan="13">{{ __('Nenhum dado encontrado.') }}</td>
        </tr>
        @endforelse
      </tbody>
  </table>

  <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 10;
            $pageText = "Page " . '{PAGE_NUM}' . " of " . '{PAGE_COUNT}';
            $y = 750; // Y-position (from top)
            $x = 520; // X-position (from left, adjust as needed)
            $pdf->text($x, $y, $pageText, $font, $size);
        }
    </script>
</body>
</html>

