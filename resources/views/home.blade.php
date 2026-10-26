@extends('layouts.app')

@section('title', __('ui.home_title'))
@section('description', __('ui.meta_description'))

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-semibold tracking-tight">
        {{ __('ui.home_heading') }}
    </h1>
    <p class="mt-4 text-lg text-gray-700 max-w-2xl">
        {{ __('ui.home_intro') }}
    </p>
</div>
@endsection
