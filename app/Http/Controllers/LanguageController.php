<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class LanguageController extends Controller
{
    /**
     * Update the current locale for the authenticated session.
     */
    public function switch(Request $request): RedirectResponse
    {
        $availableLocales = ['ar', 'en'];
        $default = Config::get('app.locale', 'ar');

        $locale = $request->string('locale')->lower()->value();
        $locale = in_array($locale, $availableLocales, true) ? $locale : $default;

        $request->session()->put('app_locale', $locale);
        App::setLocale($locale);

        return back()->with('language-switched', $locale);
    }
}
