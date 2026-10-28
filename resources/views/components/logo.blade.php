{{--
    Brand logo, inlined from resources/svg so it scales with text zoom and needs no extra request.
    Both colour variants are rendered and swapped by theme (dark class) and by the high-contrast colour modes
    (see resources/css/app.css). The SVGs are decorative: the surrounding element provides the accessible name,
    so their own role, title and duplicated id="t" are stripped.
--}}
@props(['variant' => 'horizontal'])

@php
    $fileName = $variant === 'symbol' ? 'la-simbolo' : 'la-logo-horizontal';

    $inlineSvg = function (string $theme, string $classes) use ($fileName): string {
        $svg = file_get_contents(resource_path("svg/{$fileName}-{$theme}.svg"));
        $svg = preg_replace('/<title\b[^>]*>.*?<\/title>/s', '', $svg);

        return preg_replace_callback('/<svg\b[^>]*>/', function (array $match) use ($theme, $classes): string {
            $openingTag = preg_replace('/\s(?:role|aria-labelledby|width|height)="[^"]*"/', '', $match[0]);

            return str_replace('<svg ', '<svg aria-hidden="true" focusable="false" data-logo-variant="'.$theme.'" class="'.$classes.'" ', $openingTag);
        }, $svg, 1);
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0']) }}>
    {!! $inlineSvg('light', 'h-full w-auto dark:hidden') !!}
    {!! $inlineSvg('dark', 'hidden h-full w-auto dark:block') !!}
</span>
