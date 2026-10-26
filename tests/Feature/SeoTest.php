<?php

use App\Models\Post;
use App\Models\PostTranslation;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
});

// ─── Helpers ─────────────────────────────────────────────────────────────────

function seoPost(string $locale, string $slug, ?string $altLocale = null, ?string $altSlug = null): Post
{
    $post = Post::factory()->published()->create();

    PostTranslation::factory()->forLocale($locale)->create([
        'post_id' => $post->id,
        'title' => 'SEO Test Article',
        'slug' => $slug,
        'excerpt' => 'An excerpt.',
        'markdown_content' => '# SEO Test',
    ]);

    if ($altLocale && $altSlug) {
        PostTranslation::factory()->forLocale($altLocale)->create([
            'post_id' => $post->id,
            'title' => 'SEO Test Article Alt',
            'slug' => $altSlug,
            'excerpt' => 'An excerpt.',
            'markdown_content' => '# SEO Test',
        ]);
    }

    return $post->load('translations');
}

// ─── Canonical ────────────────────────────────────────────────────────────────

test('home page has canonical url', function (): void {
    $this->get('/es')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('home', ['locale' => 'es']).'">', false);
});

test('articles index has canonical url', function (): void {
    $this->get('/es/articulos')
        ->assertOk()
        ->assertSee('<link rel="canonical"', false);
});

test('articles index page 1 canonical has no page param', function (): void {
    $response = $this->get('/es/articulos');
    $response->assertSee(locale_route('articles.index', [], 'es'), false);
});

test('article show has canonical url with slug', function (): void {
    seoPost('es', 'articulo-canonical');
    $this->get('/es/articulos/articulo-canonical')
        ->assertOk()
        ->assertSee('articulo-canonical', false);
});

// ─── hreflang ────────────────────────────────────────────────────────────────

test('home page has hreflang link tags for both locales', function (): void {
    $response = $this->get('/es');
    $response->assertSee('<link rel="alternate" hreflang="es"', false);
    $response->assertSee('<link rel="alternate" hreflang="en"', false);
    $response->assertSee('<link rel="alternate" hreflang="x-default"', false);
});

test('articles index has hreflang link tags for both locales', function (): void {
    $response = $this->get('/en/articles');
    $response->assertSee('<link rel="alternate" hreflang="es"', false);
    $response->assertSee('<link rel="alternate" hreflang="en"', false);
});

test('article with single locale has no hreflang link tag for alternate', function (): void {
    seoPost('es', 'solo-espanol');
    $this->get('/es/articulos/solo-espanol')
        ->assertOk()
        ->assertDontSee('<link rel="alternate" hreflang="en"', false);
});

test('article with both translations has hreflang link tags for each locale', function (): void {
    seoPost('es', 'en-espanol', 'en', 'in-english');
    $this->get('/es/articulos/en-espanol')
        ->assertOk()
        ->assertSee('<link rel="alternate" hreflang="es"', false)
        ->assertSee('<link rel="alternate" hreflang="en"', false)
        ->assertSee('<link rel="alternate" hreflang="x-default"', false);
});

test('article hreflang alternate points to correct en slug', function (): void {
    seoPost('es', 'articulo-bilingue', 'en', 'bilingual-article');
    $this->get('/es/articulos/articulo-bilingue')
        ->assertOk()
        ->assertSee('bilingual-article', false);
});

// ─── Open Graph ───────────────────────────────────────────────────────────────

test('home page has open graph meta tags', function (): void {
    $response = $this->get('/es');
    $response->assertSee('property="og:title"', false);
    $response->assertSee('property="og:description"', false);
    $response->assertSee('property="og:site_name"', false);
    $response->assertSee('property="og:type"', false);
    $response->assertSee('content="website"', false);
});

test('article page has og type article', function (): void {
    seoPost('es', 'articulo-og');
    $this->get('/es/articulos/articulo-og')
        ->assertOk()
        ->assertSee('content="article"', false);
});

test('article page has article published_time meta', function (): void {
    seoPost('es', 'articulo-time');
    $this->get('/es/articulos/articulo-time')
        ->assertOk()
        ->assertSee('property="article:published_time"', false);
});

test('article page has og url matching canonical', function (): void {
    seoPost('es', 'articulo-url');
    $this->get('/es/articulos/articulo-url')
        ->assertOk()
        ->assertSee('property="og:url"', false)
        ->assertSee('articulo-url', false);
});

// ─── Sitemap ─────────────────────────────────────────────────────────────────

test('sitemap returns xml content type', function (): void {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');
});

test('sitemap contains home and articles index urls', function (): void {
    $response = $this->get('/sitemap.xml');
    $response->assertSee(route('home', ['locale' => 'es']), false);
    $response->assertSee(route('home', ['locale' => 'en']), false);
    $response->assertSee(locale_route('articles.index', [], 'es'), false);
    $response->assertSee(locale_route('articles.index', [], 'en'), false);
});

test('sitemap contains published article urls', function (): void {
    seoPost('es', 'articulo-en-sitemap');
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('articulo-en-sitemap', false);
});

test('sitemap does not contain draft article urls', function (): void {
    $draft = Post::factory()->create(['status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $draft->id,
        'title' => 'Draft',
        'slug' => 'articulo-borrador-seo',
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertDontSee('articulo-borrador-seo', false);
});

test('sitemap includes both locale urls for bilingual articles', function (): void {
    seoPost('es', 'bilingue-sitemap', 'en', 'bilingual-sitemap');
    $response = $this->get('/sitemap.xml');
    $response->assertSee('bilingue-sitemap', false);
    $response->assertSee('bilingual-sitemap', false);
});

test('sitemap has valid xml structure', function (): void {
    $response = $this->get('/sitemap.xml');
    $response->assertOk();
    $response->assertSee('<urlset', false);
    $response->assertSee('<url>', false);
    $response->assertSee('<loc>', false);
});

// ─── robots.txt ───────────────────────────────────────────────────────────────

test('robots txt returns 200', function (): void {
    $this->get('/robots.txt')->assertOk();
});

test('robots txt disallows admin', function (): void {
    $this->get('/robots.txt')->assertSee('Disallow: /admin');
});

test('robots txt contains sitemap url', function (): void {
    $this->get('/robots.txt')->assertSee('Sitemap:');
});

// ─── Cache invalidation ───────────────────────────────────────────────────────

test('sitemap cache is invalidated when post is saved', function (): void {
    $this->get('/sitemap.xml')->assertOk();
    expect(Cache::has('sitemap.xml'))->toBeTrue();

    Post::factory()->published()->create();

    expect(Cache::has('sitemap.xml'))->toBeFalse();
});
