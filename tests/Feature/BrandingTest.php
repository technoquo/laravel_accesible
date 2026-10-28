<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('header logo links home with an accessible name matching the Spanish logotype', function (string $locale, string $expectedName): void {
    $this->get("/{$locale}")
        ->assertOk()
        ->assertSeeInOrder([
            '<header',
            'href="'.route('home', ['locale' => $locale]).'"',
            'data-logo-variant="light"',
            'data-logo-variant="dark"',
            $expectedName,
            '</header>',
        ], false);
})->with([
    'es' => ['es', '<span class="sr-only"><span lang="es">Laravel Accesible</span>, Inicio</span>'],
    'en' => ['en', '<span class="sr-only"><span lang="es">Laravel Accesible</span>, Home</span>'],
]);

test('inlined logo svgs are decorative and keep no duplicated title id', function (): void {
    $html = $this->get('/es')->assertOk()->getContent();

    expect(substr_count($html, '<svg aria-hidden="true" focusable="false" data-logo-variant='))->toBe(4)
        ->and($html)->not->toContain('id="t"')
        ->and($html)->not->toContain('aria-labelledby="t"');
});

test('home page keeps a single text h1 below the decorative logo', function (string $locale): void {
    $html = $this->get("/{$locale}")->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1);
    $this->get("/{$locale}")->assertSeeInOrder([
        '<main',
        'data-logo-variant="light"',
        '<h1',
        __('ui.home_heading', [], $locale),
        '</h1>',
    ], false);
})->with(['es', 'en']);

test('public and admin pages declare the site icons', function (string $path): void {
    $this->get($path)
        ->assertOk()
        ->assertSee('<link rel="icon" href="'.asset('favicon.ico').'" sizes="32x32">', false)
        ->assertSee('<link rel="icon" href="'.asset('favicon.svg').'" type="image/svg+xml">', false)
        ->assertSee('<link rel="apple-touch-icon" href="'.asset('apple-touch-icon.png').'">', false);
})->with(['/es', '/en/about', '/admin/login']);
