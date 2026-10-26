<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => 'es']));

Route::prefix('{locale}')
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', fn () => view('home'))->name('home');
    });
