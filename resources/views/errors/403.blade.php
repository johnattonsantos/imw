<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso negado</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/assets/img/favicon.ico') }}" />
    <link href="{{ asset('theme/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: "Nunito", sans-serif;
        }

        .error-card {
            width: 100%;
            max-width: 680px;
            margin: 24px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .error-code {
            font-size: 72px;
            font-weight: 800;
            line-height: 1;
            color: #7f1d1d;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .error-description {
            color: #475569;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="card error-card">
        <div class="card-body p-5 text-center">
            <div class="error-code mb-3">403</div>
            <h1 class="error-title mb-3">Acesso negado</h1>
            <p class="error-description mb-4">
                Você não tem permissão para acessar este recurso.
            </p>

            <div class="d-flex justify-content-center flex-wrap" style="gap: 12px;">
                <a href="{{ url('/') }}" class="btn btn-primary">Ir para início</a>
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Voltar</button>
            </div>
        </div>
    </div>
</body>
</html>
