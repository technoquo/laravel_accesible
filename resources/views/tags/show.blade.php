@extends('layouts.app')

@section('title', __('ui.tag_title', ['name' => $tagTranslation->name]))
@section('description', __('ui.meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-semibold tracking-tight">
        {{ __('ui.tag_heading', ['name' => $tagTranslation->name]) }}
    </h1>

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
                                <h2 class="text-xl font-medium">
                                    <a href="{{ locale_route('articles.show', ['slug' => $t->slug]) }}"
                                       class="hover:underline underline-offset-2">
                                        {{ $t->title }}
                                    </a>
                                </h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    <time datetime="{{ $post->published_at->toDateString() }}">
                                        {{ $post->published_at->isoFormat('LL') }}
                                    </time>
                                </p>
                                @if ($t->excerpt)
                                    <p class="mt-2 text-gray-700 text-sm leading-relaxed">
                                        {{ $t->excerpt }}
                                    </p>
                                @endif
                                @php $cat = $post->category?->translationForLocale($locale) @endphp
                                @if ($cat)
                                    <p class="mt-2 text-xs text-gray-500">
                                        <a href="{{ locale_route('categories.show', ['slug' => $cat->slug]) }}"
                                           class="hover:underline">
                                            {{ $cat->name }}
                                        </a>
                                    </p>
                                @endif
                            </article>
                        </li>
                    @endif
                @endforeach
            </ul>

            @if ($posts->hasPages())
                <nav aria-label="{{ __('ui.pagination_nav') }}" class="mt-10">
                    {{ $posts->links() }}
                </nav>
            @endif
        @endif
    </div>

</div>
@endsection
