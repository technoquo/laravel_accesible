<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $locale = app()->getLocale();
        $alternateLocale = $locale === 'es' ? 'en' : 'es';

        $canonicalUrl = locale_route('contact');
        $hreflangUrls = [
            'es' => locale_route('contact', [], 'es'),
            'en' => locale_route('contact', [], 'en'),
        ];
        $alternateUrls = [$alternateLocale => $hreflangUrls[$alternateLocale]];

        return view('contact', compact('canonicalUrl', 'hreflangUrls', 'alternateUrls'));
    }
}
