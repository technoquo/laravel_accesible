@extends('layouts.app')

@section('title', ($translation->meta_title ?: $translation->title) . ' — ' . __('ui.site_name'))
@section('description', $translation->meta_description ?: $translation->excerpt)

@push('styles')
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github.min.css"
      media="print"
      onload="this.media='all'"
      crossorigin="anonymous"
      referrerpolicy="no-referrer">
<noscript>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github.min.css"
          crossorigin="anonymous">
</noscript>
@endpush

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <article aria-labelledby="article-heading">

        {{-- Header --}}
        <header class="max-w-2xl">
            <h1 id="article-heading" class="text-3xl font-semibold tracking-tight leading-tight">
                {{ $translation->title }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                <time datetime="{{ $post->published_at->toDateString() }}">
                    {{ __('ui.article_published', ['date' => $post->published_at->isoFormat('LL')]) }}
                </time>

                @if ($post->author)
                    <span aria-hidden="true">·</span>
                    <span>{{ __('ui.article_by', ['author' => $post->author->name]) }}</span>
                @endif

                @php $cat = $post->category?->translationForLocale($locale) @endphp
                @if ($cat)
                    <span aria-hidden="true">·</span>
                    <a href="{{ locale_route('categories.show', ['slug' => $cat->slug]) }}"
                       class="hover:underline">
                        {{ $cat->name }}
                    </a>
                @endif
            </div>

            @if ($post->tags->isNotEmpty())
                <ul class="mt-3 flex flex-wrap gap-2" aria-label="{{ __('ui.article_tags') }}">
                    @foreach ($post->tags as $tag)
                        @php $tagT = $tag->translationForLocale($locale) @endphp
                        @if ($tagT)
                            <li>
                                <a href="{{ locale_route('tags.show', ['slug' => $tagT->slug]) }}"
                                   class="inline-block rounded-full bg-gray-100 px-3 py-0.5 text-xs text-gray-700 hover:bg-gray-200">
                                    {{ $tagT->name }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </header>

        {{-- Body --}}
        <div id="article-body" class="prose mt-10 max-w-2xl">
            {!! $html !!}
        </div>

    </article>

    {{-- Accessible live region for copy feedback --}}
    <div id="copy-feedback" aria-live="polite" aria-atomic="true" class="sr-only"></div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<script>
(function () {
    // Syntax highlighting
    document.querySelectorAll('#article-body pre code').forEach(function (block) {
        hljs.highlightElement(block);
    });

    // Copy code buttons
    var copyLabel = {{ Js::from(__('ui.copy_code')) }};
    var copiedLabel = {{ Js::from(__('ui.code_copied')) }};
    var failedLabel = {{ Js::from(__('ui.code_copy_failed')) }};
    var feedback = document.getElementById('copy-feedback');

    document.querySelectorAll('#article-body pre').forEach(function (pre) {
        var wrapper = document.createElement('div');
        wrapper.className = 'code-block';
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(pre);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'copy-btn';
        btn.setAttribute('aria-label', copyLabel);
        btn.textContent = copyLabel;
        wrapper.appendChild(btn);

        btn.addEventListener('click', function () {
            var code = pre.querySelector('code');
            var text = code ? code.textContent : pre.textContent;

            navigator.clipboard.writeText(text).then(function () {
                btn.textContent = copiedLabel;
                btn.setAttribute('aria-label', copiedLabel);
                if (feedback) { feedback.textContent = copiedLabel; }
                setTimeout(function () {
                    btn.textContent = copyLabel;
                    btn.setAttribute('aria-label', copyLabel);
                    if (feedback) { feedback.textContent = ''; }
                }, 2000);
            }).catch(function () {
                btn.textContent = failedLabel;
                btn.setAttribute('aria-label', failedLabel);
                if (feedback) { feedback.textContent = failedLabel; }
            });
        });
    });
}());
</script>
@endpush
