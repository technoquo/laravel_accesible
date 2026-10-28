@extends('layouts.app')

@section('title', __('ui.home_title'))
@section('description', __('ui.meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <div class="mx-auto max-w-2xl text-center">
        {{-- Decorative: the heading below already gives the site name --}}
        <x-logo variant="symbol" class="mb-6 size-20 sm:size-24" />
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight">
            {{ __('ui.home_heading') }}
        </h1>
        <p class="mt-4 text-lg text-gray-700">
            {{ __('ui.home_intro') }}
        </p>
    </div>

    <section aria-labelledby="recent-articles-heading" class="mt-12">
        <h2 id="recent-articles-heading" class="text-xl font-semibold tracking-tight mb-6">
            {{ __('ui.home_recent_articles') }}
        </h2>

        @if ($recentPosts->isEmpty())
            <p class="text-gray-600">{{ __('ui.home_no_articles') }}</p>
        @else
            <ul class="space-y-6" role="list">
                @foreach ($recentPosts as $post)
                    @php $t = $post->translationForLocale($locale) @endphp
                    @if ($t)
                        <li class="border-b border-gray-100 pb-6 last:border-0">
                            <article>
                                <h3 class="text-lg font-medium">
                                    <a href="{{ locale_route('articles.show', ['slug' => $t->slug]) }}"
                                       class="hover:underline underline-offset-2">
                                        {{ $t->title }}
                                    </a>
                                </h3>
                                @if ($t->excerpt)
                                    <p class="mt-1 text-gray-600 text-sm line-clamp-2">
                                        {{ $t->excerpt }}
                                    </p>
                                @endif
                                <p class="mt-2 text-xs text-gray-600">
                                    {{ $post->published_at->isoFormat('LL') }}
                                </p>
                            </article>
                        </li>
                    @endif
                @endforeach
            </ul>

            <div class="mt-8">
                <a href="{{ locale_route('articles.index') }}"
                   class="text-sm font-medium hover:underline underline-offset-2">
                    {{ __('ui.home_all_articles') }} →
                </a>
            </div>
        @endif
    </section>

</div>
@endsection
