@extends('layouts.app')

@section('title', __('ui.about_title'))
@section('description', __('ui.about_meta_description'))

@section('content')
<article class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-semibold tracking-tight">
        {{ __('ui.about_heading') }}
    </h1>

    <div class="mt-6 flex flex-col gap-8 md:flex-row md:items-start">
        <img src="{{ Vite::asset('resources/img/leonel.png') }}"
             alt="{{ __('ui.about_photo_alt') }}"
             width="344" height="280"
             class="h-auto w-full max-w-xs shrink-0 rounded-lg md:w-64">

        <div class="max-w-prose space-y-6 text-lg leading-relaxed text-gray-700">
            <p>{{ __('ui.about_intro') }}</p>
            <p>{!! __('ui.about_bio') !!}</p>
            <p>{{ __('ui.about_goal') }}</p>
        </div>
    </div>

</article>
@endsection
