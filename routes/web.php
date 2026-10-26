<?php

use App\Http\Controllers\Admin\AuthController;
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
        Route::get('/', fn () => view('home'))->name('home');
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
