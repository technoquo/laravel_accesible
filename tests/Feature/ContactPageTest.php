<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('contact page shows its title and the contact email as a mailto link', function (string $path, string $title, string $heading): void {
    $this->get($path)
        ->assertOk()
        ->assertSee("<title>{$title}</title>", false)
        ->assertSeeInOrder(['<h1', $heading, '</h1>'], false)
        ->assertSee('href="mailto:hola@laravel-accesible.org"', false);
})->with([
    ['/es/contacto', 'Contacto — Laravel Accesible', 'Contacto'],
    ['/en/contact', 'Contact Us — Laravel Accessible', 'Contact Us'],
]);

test('contact page path is not available under the other locale', function (string $path): void {
    $this->get($path)->assertNotFound();
})->with(['/en/contacto', '/es/contact']);

test('main navigation and footer link to the contact page', function (string $locale): void {
    $url = locale_route('contact', [], $locale);

    $this->get("/{$locale}")
        ->assertOk()
        ->assertSeeInOrder([
            '<nav aria-label="'.__('ui.nav_main', [], $locale).'"',
            'href="'.$url.'"',
            '<footer',
            'href="'.$url.'"',
        ], false);
})->with(['es', 'en']);

test('contact link in main navigation is marked as current page only on the contact page', function (): void {
    $currentContactLink = '/href="'.preg_quote(route('es.contact', ['locale' => 'es']), '/').'"\s+aria-current="page"/';

    expect($this->get('/es/contacto')->getContent())->toMatch($currentContactLink);
    expect($this->get('/es')->getContent())->not->toMatch($currentContactLink);
});

test('language selector links to the equivalent contact page', function (): void {
    $this->get('/es/contacto')->assertSee('href="'.route('en.contact', ['locale' => 'en']).'"', false);
    $this->get('/en/contact')->assertSee('href="'.route('es.contact', ['locale' => 'es']).'"', false);
});

test('sitemap contains both contact page urls', function (): void {
    $this->get('/sitemap.xml')
        ->assertSee(route('es.contact', ['locale' => 'es']), false)
        ->assertSee(route('en.contact', ['locale' => 'en']), false);
});
