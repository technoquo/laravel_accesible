<?php

test('root redirects to Spanish home', function (): void {
    $this->get('/')->assertRedirect(route('home', ['locale' => 'es']));
});

test('Spanish home returns 200', function (): void {
    $this->get(route('home', ['locale' => 'es']))->assertOk();
});

test('English home returns 200', function (): void {
    $this->get(route('home', ['locale' => 'en']))->assertOk();
});

test('Spanish home has html lang attribute set to es', function (): void {
    $this->get(route('home', ['locale' => 'es']))
        ->assertSee('<html lang="es"', false);
});

test('English home has html lang attribute set to en', function (): void {
    $this->get(route('home', ['locale' => 'en']))
        ->assertSee('<html lang="en"', false);
});

test('unknown locale returns 404', function (): void {
    $this->get('/fr')->assertNotFound();
    $this->get('/de')->assertNotFound();
});

test('home has skip link targeting main content', function (): void {
    $this->get(route('home', ['locale' => 'es']))
        ->assertSee('href="#main-content"', false)
        ->assertSee('id="main-content"', false);
});

test('home language selector links to both locales', function (): void {
    $this->get(route('home', ['locale' => 'es']))
        ->assertSee(route('home', ['locale' => 'en']), false)
        ->assertSee('hreflang="en"', false);
});

test('active locale in language selector is marked with aria-current', function (): void {
    // The active locale is rendered as <span aria-current="page">, not as an <a>
    $this->get(route('home', ['locale' => 'es']))
        ->assertSee('aria-current="page"', false)
        ->assertSee('<span lang="es"', false)
        ->assertDontSee('<a lang="es"', false);
});
