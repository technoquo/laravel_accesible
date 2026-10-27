<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $locale = app()->getLocale();
        $alternateLocale = $locale === 'es' ? 'en' : 'es';

        $canonicalUrl = locale_route('about');
        $hreflangUrls = [
            'es' => locale_route('about', [], 'es'),
            'en' => locale_route('about', [], 'en'),
        ];
        $alternateUrls = [$alternateLocale => $hreflangUrls[$alternateLocale]];

        return view('about', compact('canonicalUrl', 'hreflangUrls', 'alternateUrls'));
    }
}
