<?php

use App\Services\MarkdownService;

test('converts markdown headings to html', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('# Hello');

    expect($html)->toContain('<h1>Hello</h1>');
});

test('converts markdown bold to html', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('**bold**');

    expect($html)->toContain('<strong>bold</strong>');
});

test('converts markdown link to html anchor', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('[example](https://example.com)');

    expect($html)->toContain('href="https://example.com"');
});

test('strips raw html script tags to prevent xss', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('<script>alert("xss")</script>');

    expect($html)->not->toContain('<script>');
    expect($html)->not->toContain('alert("xss")');
});

test('strips raw html event handlers to prevent xss', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('<img src="x" onerror="alert(1)">');

    expect($html)->not->toContain('onerror');
});

test('blocks javascript scheme in links', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('[click](javascript:alert(1))');

    expect($html)->not->toContain('javascript:');
});

test('renders github flavored markdown fenced code blocks', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml("```php\necho 'hi';\n```");

    expect($html)->toContain('<code');
    expect($html)->toContain("echo 'hi';");
});

// ─── Images ──────────────────────────────────────────────────────────────────

test('renders markdown image with loading lazy and alt text', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Captura de pantalla del formulario](https://example.com/img.png)');

    expect($html)->toContain('<img')
        ->toContain('src="https://example.com/img.png"')
        ->toContain('alt="Captura de pantalla del formulario"')
        ->toContain('loading="lazy"');
});

test('renders markdown image with optional title attribute', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Alt text](https://example.com/img.png "Título de la imagen")');

    expect($html)->toContain('title="Título de la imagen"');
});

// ─── YouTube embeds ───────────────────────────────────────────────────────────

test('renders youtube watch url as accessible iframe', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Demostración de accesibilidad](https://www.youtube.com/watch?v=dQw4w9WgXcQ)');

    expect($html)
        ->toContain('<iframe')
        ->toContain('src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"')
        ->toContain('title="Demostración de accesibilidad"')
        ->toContain('loading="lazy"')
        ->toContain('allowfullscreen')
        ->not->toContain('<img');
});

test('renders youtu.be short url as iframe', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Video corto](https://youtu.be/dQw4w9WgXcQ)');

    expect($html)
        ->toContain('src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"')
        ->toContain('title="Video corto"');
});

test('youtube iframe uses nocookie domain for privacy', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Test](https://www.youtube.com/watch?v=dQw4w9WgXcQ)');

    expect($html)
        ->toContain('youtube-nocookie.com')
        ->not->toContain('youtube.com/embed');
});

test('youtube iframe has accessible role and aria-label', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Tutorial de WCAG 2.2](https://youtu.be/dQw4w9WgXcQ)');

    expect($html)
        ->toContain('role="figure"')
        ->toContain('aria-label="Tutorial de WCAG 2.2"');
});

test('non-youtube url renders as regular image not iframe', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml('![Foto](https://vimeo.com/123456)');

    expect($html)
        ->toContain('<img')
        ->not->toContain('<iframe');
});

test('youtube video id is sanitized in iframe src', function (): void {
    $service = new MarkdownService;

    // A URL with extra query params after the video ID — ID still extracted correctly
    $html = $service->toHtml('![Video](https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=30s)');

    expect($html)->toContain('embed/dQw4w9WgXcQ');
});

test('fenced and indented code blocks are keyboard focusable', function (): void {
    $service = new MarkdownService;

    $html = $service->toHtml("```php\necho 1;\n```\n\n    indented();\n");

    expect(substr_count($html, '<pre tabindex="0">'))->toBe(2);
});
