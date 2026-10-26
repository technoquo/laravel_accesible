<?php

if (! function_exists('locale_route')) {
    /**
     * Generate a URL for a locale-prefixed named route.
     * Route names follow the pattern: {locale}.{name} (e.g. "es.articles.index").
     *
     * @param  array<string, mixed>  $params
     */
    function locale_route(string $name, array $params = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route($locale.'.'.$name, array_merge(['locale' => $locale], $params));
    }
}
