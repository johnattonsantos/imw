<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'locale' => ['required', Rule::in(array_keys(config('locales.supported')))],
        ]);

        $locale = $request->input('locale');

        session([config('locales.session_key') => $locale]);
        App::setLocale($locale);

        return back()->with('success', __('app.messages.language_changed'));
    }
}
