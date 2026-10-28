@extends('layouts.app')

@section('title', __('ui.home_title'))
@section('description', __('ui.meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <div class="mx-auto max-w-2xl text-center">
        {{-- Decorative: the heading below already gives the site name --}}
        <x-logo variant="symbol" class="mb-6 size-20 sm:size-24" />
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-brand-ink">
            {{ __('ui.home_heading') }}
        </h1>
        <p class="mt-4 text-lg text-gray-700">
            {{ __('ui.home_intro') }}
        </p>
    </div>

    <section aria-labelledby="recent-articles-heading" class="mt-12">
        <h2 id="recent-articles-heading" class="mb-6 border-l-4 border-brand-orange pl-3 text-xl font-semibold tracking-tight text-brand-ink">
            {{ __('ui.home_recent_articles') }}
        </h2>

        @if ($recentPosts->isEmpty())
            <p class="text-gray-600">{{ __('ui.home_no_articles') }}</p>
        @else
            <ul class="grid gap-4 sm:grid-cols-2" role="list">
                @foreach ($recentPosts as $post)
                    @php $t = $post->translationForLocale($locale) @endphp
                    @if ($t)
                        <li class="min-w-0 wrap-break-word rounded-xl border border-gray-200 border-t-4 border-t-brand-teal p-4 sm:p-5 transition-colors hover:border-brand-teal motion-reduce:transition-none">
                            <article>
                                <h3 class="text-lg font-semibold text-brand-ink">
                                    <a href="{{ locale_route('articles.show', ['slug' => $t->slug]) }}"
                                       class="hover:text-brand-teal hover:underline underline-offset-2">
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
                   class="text-sm font-medium text-brand-teal hover:underline underline-offset-2">
                    {{ __('ui.home_all_articles') }} →
                </a>
            </div>
        @endif
    </section>

</div>
@endsection
