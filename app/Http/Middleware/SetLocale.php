<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to get locale from cookie first, then session, then default
        $locale = $request->cookie('locale') ?? $request->session()?->get('locale') ?? config('app.locale');
        
        // Validate locale is allowed
        $allowed = ['en', 'vi'];
        if (!in_array($locale, $allowed)) {
            $locale = config('app.locale');
        }
        
        App::setLocale($locale);
        return $next($request);
    }
}
