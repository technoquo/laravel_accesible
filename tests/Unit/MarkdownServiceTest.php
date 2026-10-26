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
