<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Relatório de Aniversariantes - IMW PGA') }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        
        h2 {
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        h4 {
            margin-bottom: .5rem;
        } 

        table {
            margin-top: 2rem;
            border-collapse: collapse
        }

        th, td {
            padding: 8px 0;
            border-bottom: 1px solid black;
            text-align: left;
            font-size: .9rem;
        }
    </style>
</head>
<body>
    <header>
        <div style="text-align: center; margin-bottom: 1rem;">
        <small style="text-transform: uppercase">
                IMW PGA {{ date('Y') }} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}
            </small>
        </div>
    </header>

    <h2>ANIVERSARIANTES - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h2>
    <h4>Vínculo: {{ $vinculos }}</h4>
    <h4>Mês: {{ $mes }}</h4>
    <h4>Onde Congrega: {{ $ondeCongrega }}</h4>
    <h4>Registros Encontrados: {{ $membros->count() }}</h4>

    <table>
        <thead>
            <tr>
                <th style="width: 30%">{{ __('NOME') }}</th>
                <th style="width: 15%">{{ __('ANIVERSÁRIO') }}</th>
                <th style="width: 15%">{{ __('NASCIMENTO') }}</th>
                <th style="width: 10%">{{ __('IDADE') }}</th>
                <th style="width: 15%">{{ __('TELEFONE') }}</th>
                <th style="width: 15%">{{ __('ONDE CONGREGA') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($membros as $membro)
                <tr>
                    <tr>
                        <td>{{ $membro->nome }}</td>
                        <td>{{ $membro->aniversario }}</td>
                        <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                        <td>{{ $membro->idade }}</td>
                        <td>{{ formatStr($membro->contato, '## (##) #####-####') }}</td>
                        <td>{{ optional($membro->congregacao)->nome ?? 'SEDE' }}</td>
                    </tr>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>