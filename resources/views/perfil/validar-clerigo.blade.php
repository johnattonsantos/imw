<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('Validação de Clérigo') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; background: linear-gradient(135deg, #eef3ff, #fff 48%, #f5f7fb); color: #263040; font-family: Arial, Helvetica, sans-serif; }
        .validation-card { width: min(560px, 100%); border: 1px solid #d8dfed; border-radius: 18px; background: #fff; box-shadow: 0 18px 50px rgba(15, 27, 67, .14); overflow: hidden; }
        header { display: flex; align-items: center; gap: 20px; padding: 28px; background: #173f91; color: #fff; }
        header img { width: 150px; height: auto; padding: 10px; background: #fff; border-radius: 10px; }
        h1 { margin: 0; font-size: 23px; }
        header p { margin: 8px 0 0; font-size: 14px; }
        section { padding: 24px; }
        dl { margin: 0; }
        dl > div { padding: 14px 0; border-bottom: 1px solid #edf1f7; }
        dl > div:last-child { border: 0; }
        dt { color: #65748a; font-size: 12px; text-transform: uppercase; margin-bottom: 6px; }
        dd { margin: 0; font-size: 17px; font-weight: 600; overflow-wrap: anywhere; }
        .status { display: inline-block; border-radius: 20px; padding: 5px 12px; background: #e8f5ed; color: #176c3f; }
        .status--inactive { background: #fff0f0; color: #a52b36; }
        .notice { padding: 12px; background: #f3f6fb; font-size: 14px; line-height: 1.5; }
        footer { padding: 0 24px 24px; font-size: 12px; color: #65748a; }
        @media (max-width: 480px) { body { padding: 12px; } header { padding: 20px; gap: 14px; } header img { width: 110px; } h1 { font-size: 20px; } }
    </style>
</head>
<body>
    <main class="validation-card">
        <header>
            <img src="{{ asset('theme/images/logo-carteirinha.png') }}" alt="{{ __('Igreja Metodista Wesleyana') }}">
            <div><h1>{{ __('Validação de Clérigo') }}</h1><p>{{ __('Carteira Digital') }}</p></div>
        </header>
        <section>
            @if ($clerigo)
                <p class="notice">{{ __('Confira os dados da carteira com o cadastro atual apresentado abaixo.') }}</p>
                <dl>
                    <div><dt>{{ __('Clérigo') }}</dt><dd>{{ $clerigo->nome }}</dd></div>
                    <div><dt>{{ __('Nº do Rol') }}</dt><dd>{{ $clerigo->rol ?: __('Não informado') }}</dd></div>
                    <div><dt>{{ __('Região') }}</dt><dd>{{ $clerigo->regiao_nome ?: __('Não informada') }}</dd></div>
                    <div><dt>{{ __('Categoria') }}</dt><dd>{{ $clerigo->categoria ? mb_convert_case($clerigo->categoria, MB_CASE_TITLE, 'UTF-8') : __('Não informada') }}</dd></div>
                    <div><dt>{{ __('Situação ministerial') }}</dt><dd>{{ $clerigo->situacao_nome ?: __('Não informada') }}</dd></div>
                    <div><dt>{{ __('Status do cadastro') }}</dt><dd><span class="status {{ $clerigo->deleted_at ? 'status--inactive' : '' }}">{{ $clerigo->deleted_at ? __('Inativo') : __('Ativo') }}</span></dd></div>
                    <div><dt>{{ __('Validade da credencial') }}</dt><dd>{{ $validade->format('d/m/Y') }} @if ($expirada)<span class="status status--inactive">{{ __('Expirada') }}</span>@endif</dd></div>
                </dl>
                @if ($clerigo->deleted_at)
                    <p class="notice">{{ __('O cadastro deste clérigo está inativo. Consulte a administração regional antes de aceitar a credencial.') }}</p>
                @endif
            @else
                <p class="notice">{{ __('Não foi possível localizar o clérigo desta credencial. Consulte a administração regional.') }}</p>
            @endif
        </section>
        <footer>{{ __('Consulta realizada em') }} {{ now()->format('d/m/Y H:i') }}.</footer>
    </main>
</body>
</html>
