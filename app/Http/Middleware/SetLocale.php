<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales.
     */
    public const LOCALES = ['en', 'pl'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1);

        if (in_array($locale, self::LOCALES)) {
            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);
        } else {
            // Default to English if no locale in URL
            App::setLocale('en');
            URL::defaults(['locale' => 'en']);
        }

        return $next($request);
    }
}
