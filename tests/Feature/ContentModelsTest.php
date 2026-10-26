<?php

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- User ---

test('user factory creates a regular user by default', function (): void {
    $user = User::factory()->create();

    expect($user->is_admin)->toBeFalse();
});

test('user factory admin state creates an admin user', function (): void {
    $user = User::factory()->admin()->create();

    expect($user->is_admin)->toBeTrue();
});

// --- Post scopes ---

test('draft post is not returned by publishedForLocale scope', function (): void {
    $post = Post::factory()->create(['status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);

    expect(Post::publishedForLocale('es')->count())->toBe(0);
});

test('published post with translation is returned by publishedForLocale scope', function (): void {
    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);

    expect(Post::publishedForLocale('es')->count())->toBe(1);
});

test('published post without translation for locale is not returned', function (): void {
    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('en')->create(['post_id' => $post->id]);

    expect(Post::publishedForLocale('es')->count())->toBe(0);
});

test('future scheduled post is not returned by publishedForLocale scope', function (): void {
    $post = Post::factory()->scheduledFuture()->create();
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);

    expect(Post::publishedForLocale('es')->count())->toBe(0);
});

// --- Slug uniqueness ---

test('post_translations enforces unique slug per locale', function (): void {
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();

    PostTranslation::factory()->create([
        'post_id' => $post1->id,
        'locale' => 'es',
        'slug' => 'mi-articulo',
    ]);

    expect(fn () => PostTranslation::factory()->create([
        'post_id' => $post2->id,
        'locale' => 'es',
        'slug' => 'mi-articulo',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

test('same slug is allowed in different locales', function (): void {
    $post = Post::factory()->create();

    PostTranslation::factory()->create([
        'post_id' => $post->id,
        'locale' => 'es',
        'slug' => 'accessibility',
    ]);
    PostTranslation::factory()->create([
        'post_id' => $post->id,
        'locale' => 'en',
        'slug' => 'accessibility',
    ]);

    expect(PostTranslation::where('post_id', $post->id)->count())->toBe(2);
});

test('post cannot have two translations for the same locale', function (): void {
    $post = Post::factory()->create();

    PostTranslation::factory()->create(['post_id' => $post->id, 'locale' => 'es']);

    expect(fn () => PostTranslation::factory()->create([
        'post_id' => $post->id,
        'locale' => 'es',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

// --- Relationships ---

test('post belongs to author', function (): void {
    $author = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $author->id]);

    expect($post->author->id)->toBe($author->id);
});

test('post can belong to a category', function (): void {
    $category = Category::factory()->create();
    $post = Post::factory()->create(['category_id' => $category->id]);

    expect($post->category->id)->toBe($category->id);
});

test('post has many translations', function (): void {
    $post = Post::factory()->create();
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);
    PostTranslation::factory()->forLocale('en')->create(['post_id' => $post->id]);

    expect($post->translations()->count())->toBe(2);
});

test('post belongs to many tags', function (): void {
    $post = Post::factory()->create();
    $tags = Tag::factory()->count(2)->create();
    $post->tags()->attach($tags->pluck('id'));

    expect($post->tags()->count())->toBe(2);
});

test('translationForLocale returns correct translation', function (): void {
    $post = Post::factory()->create();
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id, 'title' => 'Título en español']);
    PostTranslation::factory()->forLocale('en')->create(['post_id' => $post->id, 'title' => 'Title in English']);

    $post->load('translations');

    expect($post->translationForLocale('es')?->title)->toBe('Título en español');
    expect($post->translationForLocale('en')?->title)->toBe('Title in English');
    expect($post->translationForLocale('fr'))->toBeNull();
});

// --- Soft deletes ---

test('deleted post is excluded from normal queries', function (): void {
    $post = Post::factory()->published()->create();
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id]);

    $post->delete();

    expect(Post::publishedForLocale('es')->count())->toBe(0);
    expect(Post::withTrashed()->count())->toBe(1);
});

// --- Category slug uniqueness ---

test('category_translations enforces unique slug per locale', function (): void {
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();

    CategoryTranslation::factory()->create([
        'category_id' => $cat1->id,
        'locale' => 'es',
        'slug' => 'formularios',
    ]);

    expect(fn () => CategoryTranslation::factory()->create([
        'category_id' => $cat2->id,
        'locale' => 'es',
        'slug' => 'formularios',
    ]))->toThrow(UniqueConstraintViolationException::class);
});
