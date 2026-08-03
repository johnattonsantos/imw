<!DOCTYPE html>
<html lang="{{ config('locales.supported.' . app()->getLocale() . '.html_lang', str_replace('_', '-', app()->getLocale())) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('auth/css/login.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- ALERT TOASTR -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @php
        $browserTranslationsPath = base_path('lang/' . app()->getLocale() . '.json');
        $browserTranslations = file_exists($browserTranslationsPath)
            ? json_decode(file_get_contents($browserTranslationsPath), true)
            : [];
    @endphp
    <script>
        window.IMW_TRANSLATIONS = @json($browserTranslations ?: []);
        window.__ = function(key) {
            return (window.IMW_TRANSLATIONS && window.IMW_TRANSLATIONS[key]) ? window.IMW_TRANSLATIONS[key] : key;
        };
    </script>
    <title>{{ __('app.auth.login') }}</title>
    <style>
        .login-language-form {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .login-language-select {
            max-width: 235px;
            border-radius: 999px;
            font-size: 0.875rem;
            padding-left: 0.85rem;
            background-color: #f8fbff;
        }
    </style>
</head>

<body>
    @include('extras.alerts')
    <div class="form-signin text-center">
        @php
            $locales = config('locales.supported', []);
            $currentLocale = app()->getLocale();
        @endphp
        <form action="{{ route('locale.update') }}" method="POST" class="login-language-form">
            @csrf
            <label class="sr-only" for="login-locale-select">{{ __('app.language.select') }}</label>
            <select id="login-locale-select" name="locale" class="form-select login-language-select" onchange="this.form.submit()" aria-label="{{ __('app.language.select') }}">
                @foreach($locales as $locale => $settings)
                    <option value="{{ $locale }}" {{ $currentLocale === $locale ? 'selected' : '' }}>
                        {{ $settings['flag'] ?? '' }} {{ $settings['name'] }}
                    </option>
                @endforeach
            </select>
        </form>

        <img src="{{ asset('auth/images/login.png') }}" alt="{{ __('Logotipo') }}" class="logo">
        <h4 class="mb-4">{{ __('app.auth.title') }}</h4>
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="nome@exemplo.com">
                <label for="floatingInput">{{ __('app.auth.email') }}</label>
            </div>
            <div class="form-floating mb-3 position-relative">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="{{ __('app.auth.password') }}">
                <label for="floatingPassword">{{ __('app.auth.password') }}</label>
                <i class="fas fa-eye password-icon" onclick="togglePasswordVisibility()" id="togglePasswordIcon"></i>
            </div>

            <div class="mb-3">
                <a href="/esqueci-senha">{{ __('app.auth.forgot_password') }}</a>
            </div>
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> {{ __('app.auth.remember_me') }}
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit" id="loginButton">
                {{ __('app.auth.login') }}
            </button>

            <p class="mt-5 mb-3 text-muted">&copy; 2024 - <?= date('Y') ?></p>
        </form>
    </div>
    <!-- Bootstrap Bundle with Popper -->
    <link href="{{ asset('auth/js/bootstrap.bundle.min.js') }}" rel="stylesheet">
    <link href="{{ asset('auth/js/bootstrap.bundle.min.js') }}" rel="stylesheet">
    <script>
        function togglePasswordVisibility() {
            let passwordInput = document.getElementById('floatingPassword');
            let passwordIcon = document.getElementById('togglePasswordIcon');
            let isPasswordVisible = passwordInput.getAttribute('type') === 'password';

            if (isPasswordVisible) {
                passwordInput.setAttribute('type', 'text');
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.setAttribute('type', 'password');
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        document.getElementById('login-form').addEventListener('submit', function() {
            const loginButton = document.getElementById('loginButton');
            loginButton.innerHTML = @json(__('app.auth.loading')) + ' <i class="fas fa-spinner fa-spin"></i>';
            loginButton.disabled = true;
        });

        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        });
    </script>

</body>

</html>
