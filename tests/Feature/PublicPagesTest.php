<?php

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makePublishedPost(string $locale = 'es', array $translationAttributes = []): Post
{
    $title = $translationAttributes['title'] ?? fake()->unique()->sentence(5);
    unset($translationAttributes['title']);

    $post = Post::factory()->published()->create();

    PostTranslation::factory()->forLocale($locale)->create(array_merge([
        'post_id' => $post->id,
        'title' => $title,
        'slug' => Str::slug($title),
        'excerpt' => fake()->paragraph(),
        'markdown_content' => "# $title\n\nHello world.",
    ], $translationAttributes));

    return $post->load('translations');
}

// ─── Home page ────────────────────────────────────────────────────────────────

test('home page loads for es locale', function (): void {
    $this->get('/es')->assertOk()->assertSee(__('ui.home_heading'));
});

test('home page loads for en locale', function (): void {
    $this->get('/en')->assertOk()->assertSee(__('ui.home_heading'));
});

test('home page shows recent published articles for current locale', function (): void {
    $post = makePublishedPost('es', ['title' => 'Artículo de prueba visible']);
    makePublishedPost('en', ['title' => 'Article only in English']);

    $response = $this->get('/es');

    $response->assertSee('Artículo de prueba visible');
    $response->assertDontSee('Article only in English');
});

test('home page does not show draft articles', function (): void {
    $draft = Post::factory()->create(['status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $draft->id,
        'title' => 'Borrador secreto',
    ]);

    $this->get('/es')->assertDontSee('Borrador secreto');
});

// ─── Article index ─────────────────────────────────────────────────────────

test('es article index returns 200', function (): void {
    $this->get('/es/articulos')->assertOk();
});

test('en article index returns 200', function (): void {
    $this->get('/en/articles')->assertOk();
});

test('article index lists published articles in correct locale', function (): void {
    $post = makePublishedPost('es', ['title' => 'Introducción a la accesibilidad']);
    makePublishedPost('en', ['title' => 'Intro to accessibility']);

    $this->get('/es/articulos')
        ->assertSee('Introducción a la accesibilidad')
        ->assertDontSee('Intro to accessibility');
});

test('article index does not show drafts', function (): void {
    $draft = Post::factory()->create(['status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $draft->id,
        'title' => 'Draft should not appear',
    ]);

    $this->get('/es/articulos')->assertDontSee('Draft should not appear');
});

test('article index does not show future-scheduled articles', function (): void {
    $post = Post::factory()->scheduledFuture()->create();
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'title' => 'Future article hidden',
    ]);

    $this->get('/es/articulos')->assertDontSee('Future article hidden');
});

// ─── Search ────────────────────────────────────────────────────────────────

test('search returns matching article by title', function (): void {
    makePublishedPost('es', ['title' => 'Formularios accesibles con Laravel']);
    makePublishedPost('es', ['title' => 'Teclado en Livewire']);

    $this->get('/es/articulos?search=Formularios')
        ->assertSee('Formularios accesibles con Laravel')
        ->assertDontSee('Teclado en Livewire');
});

test('search returns matching article by excerpt', function (): void {
    makePublishedPost('es', [
        'title' => 'Otro artículo cualquiera',
        'excerpt' => 'Este artículo habla sobre aria-label en profundidad.',
    ]);
    makePublishedPost('es', ['title' => 'Sin coincidencia']);

    $this->get('/es/articulos?search=aria-label')
        ->assertSee('Otro artículo cualquiera')
        ->assertDontSee('Sin coincidencia');
});

test('search with no results shows empty state', function (): void {
    makePublishedPost('es');

    $this->get('/es/articulos?search=xkcd-improbable-string-99')
        ->assertOk()
        ->assertSee(__('ui.articles_no_results'));
});

// ─── Article detail ─────────────────────────────────────────────────────────

test('published article is accessible by its slug', function (): void {
    $post = makePublishedPost('es', ['title' => 'Artículo accesible publicado']);
    $slug = $post->translationForLocale('es')->slug;

    $this->get("/es/articulos/{$slug}")->assertOk()->assertSee('Artículo accesible publicado');
});

test('draft article returns 404 on public url', function (): void {
    $draft = Post::factory()->create(['status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $draft->id,
        'slug' => 'borrador-privado',
    ]);

    $this->get('/es/articulos/borrador-privado')->assertNotFound();
});

test('future-scheduled article returns 404 on public url', function (): void {
    $post = Post::factory()->scheduledFuture()->create();
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'slug' => 'articulo-futuro',
    ]);

    $this->get('/es/articulos/articulo-futuro')->assertNotFound();
});

test('non-existent article slug returns 404', function (): void {
    $this->get('/es/articulos/no-existe')->assertNotFound();
});

test('article in wrong locale returns 404', function (): void {
    // ES article not available in EN
    $post = makePublishedPost('es', ['slug' => 'solo-espanol']);

    $this->get('/en/articles/solo-espanol')->assertNotFound();
});

test('rendered article body does not contain raw script tags', function (): void {
    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'slug' => 'xss-test-article',
        'title' => 'XSS Test',
        'markdown_content' => "Hello\n\n<script>alert('xss')</script>\n\nWorld",
    ]);

    $this->get('/es/articulos/xss-test-article')
        ->assertOk()
        ->assertDontSee("<script>alert('xss')</script>", false);
});

// ─── Category pages ──────────────────────────────────────────────────────────

test('category page shows articles for that category', function (): void {
    $category = Category::factory()->create();
    CategoryTranslation::factory()->create([
        'category_id' => $category->id,
        'locale' => 'es',
        'name' => 'Accesibilidad',
        'slug' => 'accesibilidad',
    ]);

    $post = Post::factory()->published()->create(['category_id' => $category->id]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'title' => 'Post en categoría accesibilidad',
    ]);

    $this->get('/es/categorias/accesibilidad')
        ->assertOk()
        ->assertSee('Post en categoría accesibilidad');
});

test('non-existent category slug returns 404', function (): void {
    $this->get('/es/categorias/no-existe')->assertNotFound();
});

// ─── Tag pages ───────────────────────────────────────────────────────────────

test('tag page shows articles with that tag', function (): void {
    $tag = Tag::factory()->create();
    TagTranslation::factory()->create([
        'tag_id' => $tag->id,
        'locale' => 'es',
        'name' => 'Livewire',
        'slug' => 'livewire',
    ]);

    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'title' => 'Post etiquetado con Livewire',
    ]);
    $post->tags()->attach($tag);

    $this->get('/es/etiquetas/livewire')
        ->assertOk()
        ->assertSee('Post etiquetado con Livewire');
});

test('non-existent tag slug returns 404', function (): void {
    $this->get('/es/etiquetas/no-existe')->assertNotFound();
});

// ─── 404 page ────────────────────────────────────────────────────────────────

test('404 page renders accessible markup', function (): void {
    $this->get('/es/articulos/ruta-que-no-existe')
        ->assertNotFound()
        ->assertSee(__('ui.page_404_heading'));
});

// ─── Language switcher ───────────────────────────────────────────────────────

test('article show links to equivalent EN translation in language switcher', function (): void {
    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'slug' => 'accesibilidad-web',
    ]);
    PostTranslation::factory()->forLocale('en')->create([
        'post_id' => $post->id,
        'slug' => 'web-accessibility',
    ]);

    $this->get('/es/articulos/accesibilidad-web')
        ->assertOk()
        ->assertSee('/en/articles/web-accessibility');
});

test('article show falls back to en articles index when no EN translation exists', function (): void {
    $post = makePublishedPost('es', ['slug' => 'solo-en-espanol']);

    $this->get('/es/articulos/solo-en-espanol')
        ->assertOk()
        ->assertSee('/en/articles');
});
