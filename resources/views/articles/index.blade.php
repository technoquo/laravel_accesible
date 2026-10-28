@extends('layouts.app')

@section('title', __('ui.articles_title'))
@section('description', __('ui.meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-semibold tracking-tight text-brand-ink">
        {{ __('ui.articles_heading') }}
    </h1>

    {{-- Search form --}}
    <form action="{{ locale_route('articles.index') }}"
          method="GET"
          role="search"
          class="mt-6 flex flex-wrap gap-2">
        <label for="search-input" class="sr-only">
            {{ __('ui.articles_search_label') }}
        </label>
        <input
            id="search-input"
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="{{ __('ui.articles_search_placeholder') }}"
            class="flex-1 min-w-48 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
            aria-label="{{ __('ui.articles_search_label') }}"
        >
        <button type="submit"
                class="rounded border border-brand-ink bg-brand-ink px-4 py-2 text-sm font-medium text-white hover:border-brand-teal hover:bg-brand-teal focus:outline-none focus:ring-2 focus:ring-gray-900">
            {{ __('ui.articles_search_submit') }}
        </button>
        @if ($search !== '')
            <a href="{{ locale_route('articles.index') }}"
               class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                {{ __('ui.articles_search_clear') }}
            </a>
        @endif
    </form>

    @if ($search !== '')
        <p class="mt-4 text-sm text-gray-600" aria-live="polite" aria-atomic="true">
            {{ __('ui.articles_search_results', ['query' => $search]) }}
        </p>
    @endif

    {{-- Article list --}}
    <div class="mt-8">
        @if ($posts->isEmpty())
            <p class="text-gray-600">{{ __('ui.articles_no_results') }}</p>
        @else
            <ul class="space-y-8" role="list">
                @foreach ($posts as $post)
                    @php $t = $post->translationForLocale($locale) @endphp
                    @if ($t)
                        <li class="border-b border-gray-100 pb-8 last:border-0">
                            <article>
                                <h2 class="text-xl font-semibold text-brand-ink">
                                    <a href="{{ locale_route('articles.show', ['slug' => $t->slug]) }}"
                                       class="hover:text-brand-teal hover:underline underline-offset-2">
                                        {{ $t->title }}
                                    </a>
                                </h2>

                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500">
                                    <time datetime="{{ $post->published_at->toDateString() }}">
                                        {{ $post->published_at->isoFormat('LL') }}
                                    </time>
                                    @php $cat = $post->category?->translationForLocale($locale) @endphp
                                    @if ($cat)
                                        <span aria-hidden="true">·</span>
                                        <a href="{{ locale_route('categories.show', ['slug' => $cat->slug]) }}"
                                           class="font-medium text-brand-teal hover:underline">
                                            {{ $cat->name }}
                                        </a>
                                    @endif
                                </div>

                                @if ($t->excerpt)
                                    <p class="mt-2 text-gray-700 text-sm leading-relaxed">
                                        {{ $t->excerpt }}
                                    </p>
                                @endif

                                @if ($post->tags->isNotEmpty())
                                    <ul class="mt-3 flex flex-wrap gap-2" aria-label="{{ __('ui.article_tags') }}">
                                        @foreach ($post->tags as $tag)
                                            @php $tagT = $tag->translationForLocale($locale) @endphp
                                            @if ($tagT)
                                                <li>
                                                    <a href="{{ locale_route('tags.show', ['slug' => $tagT->slug]) }}"
                                                       class="inline-block rounded-full bg-brand-teal-soft px-3 py-0.5 text-xs font-medium text-brand-teal hover:bg-brand-teal hover:text-white">
                                                        {{ $tagT->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif

                                <p class="mt-3">
                                    <a href="{{ locale_route('articles.show', ['slug' => $t->slug]) }}"
                                       class="text-sm font-medium text-brand-teal hover:underline underline-offset-2"
                                       aria-label="{{ __('ui.read_more_label', ['title' => $t->title]) }}">
                                        {{ __('ui.read_more') }} →
                                    </a>
                                </p>
                            </article>
                        </li>
                    @endif
                @endforeach
            </ul>

            {{-- Pagination --}}
            @if ($posts->hasPages())
                <nav aria-label="{{ __('ui.pagination_nav') }}" class="mt-10">
                    {{ $posts->links() }}
                </nav>
            @endif
        @endif
    </div>

</div>
@endsection
