<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SetLocale
{
    /**
     * The locales the application supports.
     *
     * @var string[]
     */
    protected array $availableLocales = ['ar', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $defaultLocale = Config::get('app.locale', 'ar');
        $locale = $this->localeFromQuery($request);

        if ($locale) {
            Session::put('app_locale', $locale);
        } else {
            $locale = Session::get('app_locale');
        }

        if (! $locale) {
            $preferred = $request->getPreferredLanguage($this->availableLocales);

            if ($preferred && in_array($preferred, $this->availableLocales, true)) {
                $locale = $preferred;
                Session::put('app_locale', $locale);
            }
        }

        if (! $locale) {
            $locale = $defaultLocale;
            Session::put('app_locale', $locale);
        }

        if (! in_array($locale, $this->availableLocales, true)) {
            $locale = $defaultLocale;
            Session::put('app_locale', $locale);
        }

        App::setLocale($locale);

        $isRtl = $locale === 'ar';

        View::share('currentLocale', $locale);
        View::share('isRtl', $isRtl);

        return $next($request);
    }

    /**
     * Resolve locale from query string (?locale=en or ?lang=en).
     */
    protected function localeFromQuery(Request $request): ?string
    {
        $queryLocale = $request->query('locale', $request->query('lang'));

        if (! $queryLocale) {
            return null;
        }

        $queryLocale = strtolower($queryLocale);

        return in_array($queryLocale, $this->availableLocales, true) ? $queryLocale : null;
    }
}
