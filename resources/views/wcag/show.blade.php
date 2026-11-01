@php
    $number = $wcagCriterion['number'];
    $name = $wcagCriterion['name'];
@endphp

@extends('layouts.app')

@section('title', __('wcag.meta_title', ['number' => $number, 'name' => $name]).' — '.__('ui.site_name'))
@section('description', $wcagCriterion['summary'])

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <nav aria-label="{{ __('wcag.breadcrumb') }}" class="text-sm text-gray-600">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <li>
                <a href="{{ locale_route('wcag.index') }}" class="underline underline-offset-2 hover:text-gray-900">
                    {{ __('wcag.nav') }}
                </a>
            </li>
            <li aria-hidden="true">/</li>
            <li>
                <a href="{{ locale_route('wcag.index') }}?principle={{ $wcagCriterion['principle'] }}"
                   class="underline underline-offset-2 hover:text-gray-900">
                    {{ __('wcag.principles.'.$wcagCriterion['principle']) }}
                </a>
            </li>
            <li aria-hidden="true">/</li>
            <li aria-current="page">{{ $number }}</li>
        </ol>
    </nav>

    <article aria-labelledby="criterion-heading" class="mt-6">

        <header class="max-w-2xl">
            <h1 id="criterion-heading" class="text-3xl font-semibold tracking-tight leading-tight text-brand-ink">
                {{ $number }} {{ $name }}
            </h1>

            <p class="mt-4 text-lg leading-relaxed text-gray-700">
                {{ $wcagCriterion['summary'] }}
            </p>

            <dl class="mt-6 grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                <dt class="font-semibold text-gray-900">{{ __('wcag.level') }}</dt>
                <dd class="text-gray-700">{{ $wcagCriterion['level'] }}</dd>

                <dt class="font-semibold text-gray-900">{{ __('wcag.principle') }}</dt>
                <dd class="text-gray-700">{{ __('wcag.principles.'.$wcagCriterion['principle']) }}</dd>

                <dt class="font-semibold text-gray-900">{{ __('wcag.guideline') }}</dt>
                <dd class="text-gray-700">{{ $wcagCriterion['guideline_name'] }}</dd>

                <dt class="font-semibold text-gray-900">W3C</dt>
                <dd>
                    <a href="{{ $wcagCriterion['understanding_url'] }}" hreflang="en"
                       class="text-brand-teal underline underline-offset-2 hover:text-brand-ink">
                        {{ __('wcag.understanding', ['number' => $number]) }}
                    </a>
                </dd>
            </dl>
        </header>

        <div class="prose mt-10 max-w-2xl">
            {!! $html !!}
        </div>

        <section aria-labelledby="axe-rules-heading" class="mt-10 max-w-2xl">
            <h2 id="axe-rules-heading" class="text-xl font-semibold text-brand-ink">{{ __('wcag.axe_rules') }}</h2>
            @if ($wcagCriterion['axe_rules'] === [])
                <p class="mt-3 text-gray-700">{{ __('wcag.axe_rules_none') }}</p>
            @else
                <ul class="mt-3 flex flex-wrap gap-2" role="list">
                    @foreach ($wcagCriterion['axe_rules'] as $rule)
                        <li><code lang="en" class="rounded bg-gray-100 px-2 py-0.5 text-sm text-gray-800">{{ $rule }}</code></li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section aria-labelledby="related-heading" class="mt-10 max-w-2xl">
            <h2 id="related-heading" class="text-xl font-semibold text-brand-ink">{{ __('wcag.related_articles') }}</h2>
            @if ($relatedPosts->isEmpty())
                <p class="mt-3 text-gray-700">{{ __('wcag.related_articles_none') }}</p>
            @else
                <ul class="mt-3 space-y-3" role="list">
                    @foreach ($relatedPosts as $post)
                        @php $postTranslation = $post->translationForLocale($locale) @endphp
                        @if ($postTranslation)
                            <li>
                                <a href="{{ locale_route('articles.show', ['slug' => $postTranslation->slug]) }}"
                                   class="font-medium text-brand-ink underline underline-offset-2 hover:text-brand-teal">
                                    {{ $postTranslation->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </section>

    </article>

    @if ($adjacentGuides['previous'] || $adjacentGuides['next'])
        <nav aria-label="{{ __('wcag.guide_nav') }}" class="mt-12 flex flex-wrap justify-between gap-4 border-t border-gray-200 pt-6 text-sm">
            @if ($adjacentGuides['previous'])
                <a href="{{ locale_route('wcag.show', ['criterion' => $adjacentGuides['previous']['slug']]) }}"
                   class="underline underline-offset-2 hover:text-brand-teal">
                    <span class="block text-gray-600">{{ __('wcag.previous') }}</span>
                    <span class="font-medium text-brand-ink">{{ $adjacentGuides['previous']['number'] }} {{ $adjacentGuides['previous']['name'] }}</span>
                </a>
            @endif
            @if ($adjacentGuides['next'])
                <a href="{{ locale_route('wcag.show', ['criterion' => $adjacentGuides['next']['slug']]) }}"
                   class="ml-auto text-right underline underline-offset-2 hover:text-brand-teal">
                    <span class="block text-gray-600">{{ __('wcag.next') }}</span>
                    <span class="font-medium text-brand-ink">{{ $adjacentGuides['next']['number'] }} {{ $adjacentGuides['next']['name'] }}</span>
                </a>
            @endif
        </nav>
    @endif

</div>
@endsection
