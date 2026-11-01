<?php

use App\Livewire\Admin\PostForm;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use App\PostStatus;
use App\Services\WcagGuide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makePostWithCriteria(array $criteria, PostStatus $status = PostStatus::Published): PostTranslation
{
    $post = Post::factory()->create([
        'status' => $status,
        'published_at' => $status === PostStatus::Published ? now()->subDay() : null,
        'wcag_criteria' => $criteria,
    ]);

    return PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);
}

// ─── Criteria data ────────────────────────────────────────────────────────────

test('guide covers the 55 level A and AA criteria with names and summaries in both languages', function (): void {
    $criteria = app(WcagGuide::class)->all();

    expect($criteria)->toHaveCount(55);

    foreach ($criteria as $criterion) {
        foreach (['es', 'en'] as $locale) {
            expect(trans()->has("wcag.criteria.{$criterion['slug']}.name", $locale, false))->toBeTrue("{$criterion['number']} name ({$locale})")
                ->and(trans()->has("wcag.criteria.{$criterion['slug']}.summary", $locale, false))->toBeTrue("{$criterion['number']} summary ({$locale})");
        }
    }
});

test('every criterion marked with a guide has a guide file in both languages', function (): void {
    $guide = app(WcagGuide::class);

    expect($guide->guides())->toHaveCount(15);

    foreach (array_keys($guide->guides()) as $number) {
        expect($guide->guidePath($number, 'es'))->toBeFile()
            ->and($guide->guidePath($number, 'en'))->toBeFile();
    }
});

// ─── Index ────────────────────────────────────────────────────────────────────

test('guide index lists every criterion', function (string $path, string $title, string $count): void {
    $this->get($path)
        ->assertOk()
        ->assertSee("<title>{$title}</title>", false)
        ->assertSee($count)
        ->assertSee('1.1.1')
        ->assertSee('4.1.3');
})->with([
    ['/es/guia-wcag', 'Guía WCAG 2.2 para Laravel — Laravel Accesible', '55 criterios'],
    ['/en/wcag-guide', 'WCAG 2.2 Guide for Laravel — Laravel Accessible', '55 criteria'],
]);

test('guide index filters criteria', function (string $query, string $count): void {
    $this->get('/es/guia-wcag?'.$query)
        ->assertOk()
        ->assertSee($count);
})->with([
    'level A' => ['level=A', '31 criterios'],
    'level AA' => ['level=AA', '24 criterios'],
    'principle' => ['principle=robust', '2 criterios'],
    'only guides' => ['guides=1', '15 criterios'],
    'combined' => ['level=AA&principle=robust', '1 criterio'],
    'invalid values are ignored' => ['level=AAA&principle=fake', '55 criterios'],
]);

test('criteria with a guide link to it and the rest link to W3C', function (): void {
    $this->get('/es/guia-wcag')
        ->assertSee('href="'.route('es.wcag.show', ['locale' => 'es', 'criterion' => '2-4-3']).'"', false)
        ->assertSee('href="https://www.w3.org/WAI/WCAG22/Understanding/captions-prerecorded.html"', false);
});

test('guide index path is not available under the other locale', function (string $path): void {
    $this->get($path)->assertNotFound();
})->with(['/en/guia-wcag', '/es/wcag-guide']);

test('main navigation and footer link to the guide index', function (string $locale): void {
    $url = locale_route('wcag.index', [], $locale);

    $this->get("/{$locale}")
        ->assertOk()
        ->assertSeeInOrder([
            '<nav aria-label="'.__('ui.nav_main', [], $locale).'"',
            'href="'.$url.'"',
            '<footer',
            'href="'.$url.'"',
        ], false);
})->with(['es', 'en']);

test('guide link in main navigation is marked as current page only on the guide index', function (): void {
    $currentGuideLink = '/href="'.preg_quote(route('es.wcag.index', ['locale' => 'es']), '/').'"\s+aria-current="page"/';

    expect($this->get('/es/guia-wcag')->getContent())->toMatch($currentGuideLink);
    expect($this->get('/es')->getContent())->not->toMatch($currentGuideLink);
});

// ─── Guide page ───────────────────────────────────────────────────────────────

test('guide page renders the criterion and its guide', function (string $path, string $heading, string $section): void {
    $this->get($path)
        ->assertOk()
        ->assertSeeInOrder(['<h1', $heading, '</h1>'], false)
        ->assertSee($section)
        ->assertSee('href="https://www.w3.org/WAI/WCAG22/Understanding/focus-order.html"', false);
})->with([
    ['/es/guia-wcag/2-4-3', '2.4.3 Orden del foco', 'Qué exige'],
    ['/en/wcag-guide/2-4-3', '2.4.3 Focus Order', 'What it requires'],
]);

test('guide page links to the equivalent page in the other language', function (): void {
    $this->get('/es/guia-wcag/2-4-3')
        ->assertSee('<link rel="canonical" href="'.route('es.wcag.show', ['locale' => 'es', 'criterion' => '2-4-3']).'">', false)
        ->assertSee('href="'.route('en.wcag.show', ['locale' => 'en', 'criterion' => '2-4-3']).'"', false);
});

test('guide page returns 404 for unknown criteria, criteria without a guide and the other locale', function (string $path): void {
    $this->get($path)->assertNotFound();
})->with([
    'unknown criterion' => '/es/guia-wcag/1-9-9',
    'criterion without guide' => '/es/guia-wcag/1-2-1',
    'other locale' => '/en/guia-wcag/2-4-3',
]);

test('guide page lists published related articles but not drafts', function (): void {
    $published = makePostWithCriteria(['2.4.3', '2.1.1']);
    $draft = makePostWithCriteria(['2.4.3'], PostStatus::Draft);
    $unrelated = makePostWithCriteria(['1.4.3']);

    $this->get('/es/guia-wcag/2-4-3')
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title)
        ->assertDontSee($unrelated->title);
});

test('sitemap contains the guide index and guide pages in both languages', function (): void {
    $this->get('/sitemap.xml')
        ->assertSee(route('es.wcag.index', ['locale' => 'es']), false)
        ->assertSee(route('en.wcag.index', ['locale' => 'en']), false)
        ->assertSee(route('es.wcag.show', ['locale' => 'es', 'criterion' => '4-1-3']), false)
        ->assertSee(route('en.wcag.show', ['locale' => 'en', 'criterion' => '4-1-3']), false);
});

// ─── Articles ─────────────────────────────────────────────────────────────────

test('article page lists its criteria linking to the guide or to W3C', function (): void {
    $translation = makePostWithCriteria(['2.4.3', '1.2.2']);

    $this->get('/es/articulos/'.$translation->slug)
        ->assertOk()
        ->assertSee(__('wcag.post_criteria', [], 'es'))
        ->assertSee('href="'.route('es.wcag.show', ['locale' => 'es', 'criterion' => '2-4-3']).'"', false)
        ->assertSee('href="https://www.w3.org/WAI/WCAG22/Understanding/captions-prerecorded.html"', false);
});

test('article page without criteria has no criteria section', function (): void {
    $translation = makePostWithCriteria([]);

    $this->get('/es/articulos/'.$translation->slug)
        ->assertOk()
        ->assertDontSee(__('wcag.post_criteria', [], 'es'));
});

// ─── Admin ────────────────────────────────────────────────────────────────────

test('admin can save the criteria an article covers', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Foco en Livewire')
        ->set('slugEs', 'foco-en-livewire')
        ->set('markdownEs', '# Hola')
        ->set('titleEn', 'Focus in Livewire')
        ->set('slugEn', 'focus-in-livewire')
        ->set('markdownEn', '# Hello')
        ->set('wcagCriteria', ['4.1.3', '2.4.3'])
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::sole()->wcag_criteria)->toBe(['2.4.3', '4.1.3']);
});

test('admin cannot save criteria that do not exist', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('wcagCriteria', ['2.4.3', '9.9.9'])
        ->call('save')
        ->assertHasErrors('wcagCriteria.1');
});

test('admin form loads the saved criteria', function (): void {
    $admin = User::factory()->admin()->create();
    $post = makePostWithCriteria(['3.3.1'])->post;

    Livewire::actingAs($admin)
        ->test(PostForm::class, ['post' => $post])
        ->assertSet('wcagCriteria', ['3.3.1']);
});
