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
        $locale = Session::get('app_locale', $defaultLocale);

        if (! in_array($locale, $this->availableLocales, true)) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);

        $isRtl = $locale === 'ar';

        View::share('currentLocale', $locale);
        View::share('isRtl', $isRtl);

        return $next($request);
    }
}
