<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    private const SUPPORTED = ['es', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, strict: true)) {
            abort(404);
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
