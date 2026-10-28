<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Spanish about page returns 200 and shows its title', function (): void {
    $this->get('/es/acerca-de')
        ->assertOk()
        ->assertSee('<title>Acerca de — Laravel Accesible</title>', false)
        ->assertSeeInOrder(['<h1', 'Acerca de', '</h1>'], false)
        ->assertSee('Soy Leonel López, una persona sorda');
});

test('English about page returns 200 and shows its title', function (): void {
    $this->get('/en/about')
        ->assertOk()
        ->assertSee('<title>About — Laravel Accessible</title>', false)
        ->assertSeeInOrder(['<h1', 'About', '</h1>'], false)
        ->assertSee('<span lang="es">Congreso Accesible América</span>', false);
});

test('about page shows the author photo with localized alt text', function (string $locale, string $path): void {
    $this->get($path)
        ->assertOk()
        ->assertSee('alt="'.__('ui.about_photo_alt', [], $locale).'"', false);
})->with([['es', '/es/acerca-de'], ['en', '/en/about']]);

test('about page path is not available under the other locale', function (string $path): void {
    $this->get($path)->assertNotFound();
})->with(['/en/acerca-de', '/es/about']);

test('main navigation and footer link to the about page', function (string $locale): void {
    $url = locale_route('about', [], $locale);

    $this->get("/{$locale}")
        ->assertOk()
        ->assertSeeInOrder([
            '<nav aria-label="'.__('ui.nav_main', [], $locale).'"',
            'href="'.$url.'"',
            '<footer',
            'href="'.$url.'"',
        ], false);
})->with(['es', 'en']);

test('about link in main navigation is marked as current page only on the about page', function (): void {
    $currentAboutLink = '/href="'.preg_quote(route('es.about', ['locale' => 'es']), '/').'"\s+aria-current="page"/';

    expect($this->get('/es/acerca-de')->getContent())->toMatch($currentAboutLink);
    expect($this->get('/es')->getContent())->not->toMatch($currentAboutLink);
});

test('language selector links to the equivalent about page', function (): void {
    $this->get('/es/acerca-de')->assertSee('href="'.route('en.about', ['locale' => 'en']).'"', false);
    $this->get('/en/about')->assertSee('href="'.route('es.about', ['locale' => 'es']).'"', false);
});

test('sitemap contains both about page urls', function (): void {
    $this->get('/sitemap.xml')
        ->assertSee(route('es.about', ['locale' => 'es']), false)
        ->assertSee(route('en.about', ['locale' => 'en']), false);
});
