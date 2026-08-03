<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $sessionKey = config('locales.session_key', 'app_locale');
        $locale = session($sessionKey, config('locales.default', config('app.locale')));
        $supportedLocales = array_keys(config('locales.supported', []));

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = config('locales.default', config('app.locale'));
            session([$sessionKey => $locale]);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
