<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Redefinir Senha') }}</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('auth/css/login.css') }}" rel="stylesheet">
</head>

<body>

    <div class="form-signin text-center">
        <img src="{{ asset('auth/images/login.png') }}" alt="{{ __('Logotipo') }}" class="logo">
        <h4 class="mb-4">{{ __('Redefinir Senha') }}</h4>
        <form method="POST" action="{{ route('password.reset-post') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group row">
                <label for="email" class="col-md-12 col-form-label">{{ __('Endereço de E-mail') }}</label>
                <div class="col-md-12">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-12 col-form-label">{{ __('Nova Senha') }}</label>
                <div class="col-md-12">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm" class="col-md-12 col-form-label">{{ __('Confirme a Nova Senha') }}</label>
                <div class="col-md-12">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-group row mb-0 mt-4">
                <div class="col-md-12 text-center">
                    <button type="submit" class="w-100 btn btn-lg btn-primary">{{ __('Redefinir Senha') }}</button>
                </div>
            </div>


            @if (session('status'))
                <div class="alert alert-success mt-4">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mt-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </form>

    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{ asset('auth/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>

</html>
