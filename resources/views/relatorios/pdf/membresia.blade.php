<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Relatório de Membros - IMW PGA') }}</title>

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

    <h2>RELATÓRIO SECRETARIA {{ isset($vinculos) ? $vinculos : ''}} - {{ session()->get('session_perfil')->instituicoes->igrejaLocal->nome }}</h2>
    <h4>Registros Encontrados: {{ $membros->count() }}</h4>
    <h4>Vínculo: {{ isset($vinculos) ? $vinculos : '' }}</h4>
    <h4>Situação: {{ $situacao }}</h4>
    <h4>Onde Congrega: {{ $ondeCongrega }}</h4>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">{{ __('ROL') }}</th>
                <th style="width: 20%">{{ __('NOME') }}</th>
                <th style="width: 15%">{{ __('TELEFONE') }}</th>
                <th style="width: 9%">{{ __('SITUAÇÃO') }}</th>
                <th style="width: 12%">{{ __('VÍNCULO') }}</th>
                <th style="width: 12%">{{ __('NASCIMENTO') }}</th>
                <th style="width: 10%">{{ __('RECEPÇÃO') }}</th>
                <th style="width: 10%">{{ __('EXCLUSÃO') }}</th>
                <th style="width: 15%">{{ __('LOCAL') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($membros as $membro)
                <tr>
                    <td>{{ $membro->rol_atual ?? 0 }}</td>
                    <td>{{ $membro->nome }}</td>
                    <td>{{ formatStr($membro->telefone, '## (##) #####-####') }}</td>
                    <td>
                        {{ $membro->status == 'A' ? 'ATIVO' : 'INATIVO' }}
                        @if($membro->vinculo == App\Models\MembresiaMembro::VINCULO_MEMBRO)
                            {{-- $membro->rolAtualSessionIgreja->statusText --}}
                            {{ $membro->status == 'A' ? 'ATIVO' : 'INATIVO' }}
                        @else
                            {{ $membro->statusText }}
                        @endif
                    </td>
                    <td>{{ $membro->vinculoText }}</td>
                    <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                    <td>
                        @if($membro->vinculo == App\Models\MembresiaMembro::VINCULO_MEMBRO)
                            {{ optional($membro->rolAtualSessionIgreja->dt_recepcao)->format('d/m/Y') }}
                        @else
                            {{ $membro->created_at->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>
                        @if($membro->vinculo == App\Models\MembresiaMembro::VINCULO_MEMBRO)
                            {{ optional($membro->rolAtualSessionIgreja->dt_exclusao)->format('d/m/Y') }}
                        @else
                            {{ optional($membro->deleted_at)->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>{{ optional($membro->congregacao)->nome ?? 'SEDE' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>