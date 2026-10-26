<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SetLocale;
use App\Livewire\Admin\CategoriesIndex;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\PostForm;
use App\Livewire\Admin\PostsIndex;
use App\Livewire\Admin\TagsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => 'es']));

Route::prefix('{locale}')
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        // ES public routes
        Route::get('articulos', [PostController::class, 'index'])
            ->name('es.articles.index')->where('locale', 'es');
        Route::get('articulos/{slug}', [PostController::class, 'show'])
            ->name('es.articles.show')->where('locale', 'es');
        Route::get('categorias/{slug}', [CategoryController::class, 'show'])
            ->name('es.categories.show')->where('locale', 'es');
        Route::get('etiquetas/{slug}', [TagController::class, 'show'])
            ->name('es.tags.show')->where('locale', 'es');

        // EN public routes
        Route::get('articles', [PostController::class, 'index'])
            ->name('en.articles.index')->where('locale', 'en');
        Route::get('articles/{slug}', [PostController::class, 'show'])
            ->name('en.articles.show')->where('locale', 'en');
        Route::get('categories/{slug}', [CategoryController::class, 'show'])
            ->name('en.categories.show')->where('locale', 'en');
        Route::get('tags/{slug}', [TagController::class, 'show'])
            ->name('en.tags.show')->where('locale', 'en');
    });

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/login', Login::class)->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function (): void {
            Route::get('/posts', PostsIndex::class)->name('posts.index');
            Route::get('/posts/create', PostForm::class)->name('posts.create');
            Route::get('/posts/{post}/edit', PostForm::class)->name('posts.edit');
            Route::get('/categories', CategoriesIndex::class)->name('categories.index');
            Route::get('/tags', TagsIndex::class)->name('tags.index');
        });
    });
