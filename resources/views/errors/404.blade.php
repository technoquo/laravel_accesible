@extends('layouts.app')

@section('title', __('ui.page_404_title'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-24 text-center">
    <p class="text-5xl font-semibold text-gray-300" aria-hidden="true">404</p>
    <h1 class="mt-4 text-2xl font-semibold tracking-tight">
        {{ __('ui.page_404_heading') }}
    </h1>
    <p class="mt-3 text-gray-600">
        {{ __('ui.page_404_text') }}
    </p>
    <div class="mt-8">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
           class="inline-block rounded border border-gray-900 px-5 py-2 text-sm font-medium hover:bg-gray-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-900">
            {{ __('ui.page_404_back_home') }}
        </a>
    </div>
</div>
@endsection
