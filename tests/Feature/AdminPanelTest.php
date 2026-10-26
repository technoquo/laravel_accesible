<?php

use App\Livewire\Admin\CategoriesIndex;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\PostForm;
use App\Livewire\Admin\PostsIndex;
use App\Livewire\Admin\TagsIndex;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Access control ───────────────────────────────────────────────────────────

test('unauthenticated user is redirected from admin posts to login', function (): void {
    $this->get(route('admin.posts.index'))
        ->assertRedirect(route('admin.login'));
});

test('unauthenticated user is redirected from admin categories to login', function (): void {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('admin.login'));
});

test('unauthenticated user is redirected from admin tags to login', function (): void {
    $this->get(route('admin.tags.index'))
        ->assertRedirect(route('admin.login'));
});

test('non-admin user cannot access admin posts', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.posts.index'))
        ->assertForbidden();
});

test('admin user can access admin posts', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.posts.index'))
        ->assertOk();
});

test('admin login page is publicly accessible', function (): void {
    $this->get(route('admin.login'))
        ->assertOk();
});

// ─── Login component ──────────────────────────────────────────────────────────

test('login with invalid credentials shows validation error', function (): void {
    User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => bcrypt('secret')]);

    Livewire::test(Login::class)
        ->set('email', 'admin@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('login with non-admin account is rejected', function (): void {
    $user = User::factory()->create(['email' => 'regular@example.com', 'password' => bcrypt('secret')]);

    Livewire::test(Login::class)
        ->set('email', 'regular@example.com')
        ->set('password', 'secret')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('login with valid admin credentials redirects to posts', function (): void {
    User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => bcrypt('secret')]);

    Livewire::test(Login::class)
        ->set('email', 'admin@example.com')
        ->set('password', 'secret')
        ->call('login')
        ->assertRedirect(route('admin.posts.index'));
});

test('login requires email and password', function (): void {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

test('logout invalidates session and redirects to login', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});

// ─── Posts index ──────────────────────────────────────────────────────────────

test('posts index renders for admin', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostsIndex::class)
        ->assertOk();
});

test('posts index lists posts with translations', function (): void {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['author_id' => $admin->id]);
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $post->id, 'title' => 'Mi primer post']);

    Livewire::actingAs($admin)
        ->test(PostsIndex::class)
        ->assertSee('Mi primer post');
});

test('posts index search filters by translation title', function (): void {
    $admin = User::factory()->admin()->create();

    $visible = Post::factory()->create(['author_id' => $admin->id]);
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $visible->id, 'title' => 'Laravel Accesible']);

    $hidden = Post::factory()->create(['author_id' => $admin->id]);
    PostTranslation::factory()->forLocale('es')->create(['post_id' => $hidden->id, 'title' => 'Otro post']);

    Livewire::actingAs($admin)
        ->test(PostsIndex::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Accesible')
        ->assertDontSee('Otro post');
});

test('admin can soft-delete a post', function (): void {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['author_id' => $admin->id]);

    Livewire::actingAs($admin)
        ->test(PostsIndex::class)
        ->call('delete', $post->id);

    expect(Post::withTrashed()->find($post->id)->deleted_at)->not->toBeNull();
    expect(Post::find($post->id))->toBeNull();
});

// ─── Post form ────────────────────────────────────────────────────────────────

test('post form renders for create', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->assertOk()
        ->assertSet('postId', null);
});

test('post form loads existing post data for edit', function (): void {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['author_id' => $admin->id, 'status' => PostStatus::Draft]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $post->id,
        'title' => 'Título en español',
        'slug' => 'titulo-en-espanol',
    ]);

    Livewire::actingAs($admin)
        ->test(PostForm::class, ['post' => $post])
        ->assertSet('postId', $post->id)
        ->assertSet('titleEs', 'Título en español')
        ->assertSet('slugEs', 'titulo-en-espanol');
});

test('creating a post with valid data persists it to database', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Accesibilidad en Laravel')
        ->set('slugEs', 'accesibilidad-en-laravel')
        ->set('markdownEs', '# Hola')
        ->set('titleEn', 'Accessibility in Laravel')
        ->set('slugEn', 'accessibility-in-laravel')
        ->set('markdownEn', '# Hello')
        ->call('save')
        ->assertRedirect(route('admin.posts.index'));

    expect(Post::count())->toBe(1);
    expect(PostTranslation::where('locale', 'es')->where('slug', 'accesibilidad-en-laravel')->exists())->toBeTrue();
    expect(PostTranslation::where('locale', 'en')->where('slug', 'accessibility-in-laravel')->exists())->toBeTrue();
});

test('creating a post with status published sets published_at', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Post publicado')
        ->set('slugEs', 'post-publicado')
        ->set('markdownEs', 'Contenido')
        ->set('titleEn', 'Published post')
        ->set('slugEn', 'published-post')
        ->set('markdownEn', 'Content')
        ->set('status', 'published')
        ->call('save');

    $post = Post::first();
    expect($post->status)->toBe(PostStatus::Published);
    expect($post->published_at)->not->toBeNull();
});

test('post form fails validation when required fields are missing', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->call('save')
        ->assertHasErrors(['titleEs', 'titleEn', 'markdownEs', 'markdownEn', 'slugEs', 'slugEn']);
});

test('post form rejects duplicate slug for same locale', function (): void {
    $admin = User::factory()->admin()->create();
    $existing = Post::factory()->create(['author_id' => $admin->id]);
    PostTranslation::factory()->forLocale('es')->create([
        'post_id' => $existing->id,
        'slug' => 'slug-duplicado',
    ]);

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Otro post')
        ->set('slugEs', 'slug-duplicado')
        ->set('markdownEs', 'Contenido')
        ->set('titleEn', 'Another post')
        ->set('slugEn', 'another-post')
        ->set('markdownEn', 'Content')
        ->call('save')
        ->assertHasErrors(['slugEs']);
});

test('slug auto-generates from title when empty', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Mi Primer Post Accesible')
        ->assertSet('slugEs', 'mi-primer-post-accesible');
});

test('existing slug is preserved when title changes', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Primer título')
        ->set('slugEs', 'slug-personalizado')
        ->set('titleEs', 'Título cambiado')
        ->assertSet('slugEs', 'slug-personalizado');
});

test('post form syncs tags on save', function (): void {
    $admin = User::factory()->admin()->create();
    $tag = Tag::factory()->create();
    TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'es', 'slug' => 'mi-tag']);
    TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'en', 'slug' => 'my-tag']);

    Livewire::actingAs($admin)
        ->test(PostForm::class)
        ->set('titleEs', 'Post con etiqueta')
        ->set('slugEs', 'post-con-etiqueta')
        ->set('markdownEs', 'Contenido')
        ->set('titleEn', 'Post with tag')
        ->set('slugEn', 'post-with-tag')
        ->set('markdownEn', 'Content')
        ->set('tagIds', [$tag->id])
        ->call('save');

    $post = Post::first();
    expect($post->tags->pluck('id'))->toContain($tag->id);
});

// ─── Categories ───────────────────────────────────────────────────────────────

test('categories index renders for admin', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(CategoriesIndex::class)
        ->assertOk();
});

test('admin can create a category with translations', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(CategoriesIndex::class)
        ->call('create')
        ->set('stableKey', 'php')
        ->set('sortOrder', '1')
        ->set('nameEs', 'PHP')
        ->set('slugEs', 'php')
        ->set('nameEn', 'PHP')
        ->set('slugEn', 'php-en')
        ->call('save');

    expect(Category::where('stable_key', 'php')->exists())->toBeTrue();
    expect(CategoryTranslation::where('locale', 'es')->where('slug', 'php')->exists())->toBeTrue();
});

test('admin can delete a category', function (): void {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(CategoriesIndex::class)
        ->call('delete', $category->id);

    expect(Category::find($category->id))->toBeNull();
});

// ─── Tags ─────────────────────────────────────────────────────────────────────

test('tags index renders for admin', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(TagsIndex::class)
        ->assertOk();
});

test('admin can create a tag with translations', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(TagsIndex::class)
        ->call('create')
        ->set('stableKey', 'laravel')
        ->set('nameEs', 'Laravel')
        ->set('slugEs', 'laravel')
        ->set('nameEn', 'Laravel')
        ->set('slugEn', 'laravel-en')
        ->call('save');

    expect(Tag::where('stable_key', 'laravel')->exists())->toBeTrue();
    expect(TagTranslation::where('locale', 'es')->where('slug', 'laravel')->exists())->toBeTrue();
});

test('admin can delete a tag', function (): void {
    $admin = User::factory()->admin()->create();
    $tag = Tag::factory()->create();
    TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'es', 'slug' => 'test-tag']);
    TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'en', 'slug' => 'test-tag-en']);

    Livewire::actingAs($admin)
        ->test(TagsIndex::class)
        ->call('delete', $tag->id);

    expect(Tag::find($tag->id))->toBeNull();
});
