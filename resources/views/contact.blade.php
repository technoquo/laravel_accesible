@extends('layouts.app')

@section('title', __('ui.contact_title'))
@section('description', __('ui.contact_meta_description'))

@section('content')
<article class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-semibold tracking-tight">
        {{ __('ui.contact_heading') }}
    </h1>

    <div class="mt-6 max-w-prose space-y-6 text-lg leading-relaxed text-gray-700">
        <p>{{ __('ui.contact_intro') }}</p>
        <p>
            <a href="mailto:{{ config('mail.contact_address') }}"
               class="font-semibold underline underline-offset-2 decoration-brand-orange decoration-2 hover:text-gray-900 break-all">
                {{ config('mail.contact_address') }}
            </a>
        </p>
    </div>

</article>
@endsection
