@extends('layouts.app')

@section('title', __('wcag.index_title').' — '.__('ui.site_name'))
@section('description', __('wcag.index_meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-semibold tracking-tight text-brand-ink">
        {{ __('wcag.index_title') }}
    </h1>

    <p class="mt-4 max-w-prose text-lg leading-relaxed text-gray-700">
        {{ __('wcag.index_intro') }}
    </p>

    {{-- Filters --}}
    <form action="{{ locale_route('wcag.index') }}" method="GET" class="mt-8">
        <fieldset class="rounded-lg border border-gray-200 p-4">
            <legend class="px-1 text-sm font-semibold text-gray-900">{{ __('wcag.filters') }}</legend>

            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="filter-level" class="block text-sm font-medium text-gray-700">{{ __('wcag.filter_level') }}</label>
                    <select id="filter-level" name="level"
                            class="mt-1 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">{{ __('wcag.filter_all') }}</option>
                        @foreach (\App\Services\WcagGuide::LEVELS as $levelOption)
                            <option value="{{ $levelOption }}" @selected($level === $levelOption)>{{ $levelOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter-principle" class="block text-sm font-medium text-gray-700">{{ __('wcag.filter_principle') }}</label>
                    <select id="filter-principle" name="principle"
                            class="mt-1 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="">{{ __('wcag.filter_all') }}</option>
                        @foreach ($principles as $principleOption)
                            <option value="{{ $principleOption }}" @selected($principle === $principleOption)>
                                {{ __('wcag.principles.'.$principleOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label class="flex min-h-10 items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="guides" value="1" @checked($onlyGuides)
                           class="size-5 rounded border-gray-300 focus:ring-2 focus:ring-gray-900">
                    {{ __('wcag.filter_only_guides') }}
                </label>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                            class="rounded border border-brand-ink bg-brand-ink px-4 py-2 text-sm font-medium text-white hover:border-brand-teal hover:bg-brand-teal focus:outline-none focus:ring-2 focus:ring-gray-900">
                        {{ __('wcag.filter_submit') }}
                    </button>
                    @if ($isFiltered)
                        <a href="{{ locale_route('wcag.index') }}"
                           class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            {{ __('wcag.filter_reset') }}
                        </a>
                    @endif
                </div>
            </div>
        </fieldset>
    </form>

    <p role="status" class="mt-6 text-sm text-gray-600">
        {{ trans_choice('wcag.results_count', count($criteria)) }}
    </p>

    {{-- Criteria grouped by principle and guideline --}}
    @if ($criteria === [])
        <p class="mt-6 text-gray-600">{{ __('wcag.no_results') }}</p>
    @else
        @foreach ($groupedCriteria as $principleKey => $guidelines)
            <section class="mt-10" aria-labelledby="principle-{{ $principleKey }}">
                <h2 id="principle-{{ $principleKey }}" class="text-2xl font-semibold text-brand-ink">
                    {{ __('wcag.principles.'.$principleKey) }}
                </h2>

                @foreach ($guidelines as $guidelineNumber => $guidelineCriteria)
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">
                        {{ reset($guidelineCriteria)['guideline_name'] }}
                    </h3>

                    <ul class="mt-3 space-y-4" role="list">
                        @foreach ($guidelineCriteria as $number => $criterion)
                            <li class="border-b border-gray-100 pb-4 last:border-0">
                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                    @if ($criterion['has_guide'])
                                        <a href="{{ locale_route('wcag.show', ['criterion' => $criterion['slug']]) }}"
                                           class="font-semibold text-brand-ink underline underline-offset-2 hover:text-brand-teal">
                                            {{ $number }} {{ $criterion['name'] }}
                                        </a>
                                    @else
                                        <a href="{{ $criterion['understanding_url'] }}"
                                           hreflang="en"
                                           class="font-semibold text-brand-ink underline underline-offset-2 hover:text-brand-teal">
                                            {{ $number }} {{ $criterion['name'] }}
                                            <span class="text-sm font-normal">{{ __('wcag.w3c_link_suffix') }}</span>
                                        </a>
                                    @endif

                                    <span class="rounded border border-gray-300 px-2 py-0.5 text-xs font-medium text-gray-700">
                                        {{ __('wcag.level_label', ['level' => $criterion['level']]) }}
                                    </span>

                                    @if ($criterion['has_guide'])
                                        <span class="rounded border border-brand-teal px-2 py-0.5 text-xs font-medium text-brand-teal">
                                            {{ __('wcag.has_guide') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 max-w-prose text-sm text-gray-600">
                                    {{ $criterion['summary'] }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </section>
        @endforeach
    @endif

</div>
@endsection
