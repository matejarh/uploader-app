<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = $request->cookie('language') ?: app()->getLocale();
        //\Log::info("Cookie set to: " . $request->cookie('language'));

        app()->setLocale($language);
        //\Log::info("Locale set to: " . $language . " (Selected language: " . $language . ")");


        return $next($request);
    }
}
