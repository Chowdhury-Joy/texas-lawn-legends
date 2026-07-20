@extends('layouts.app')

@php
    $seoTitle = $page->seo_title ?: ($page->is_home ? null : $page->title);
    $seoDescription = $page->seo_description;
    $seoImage = filled($page->seo_image) ? public_url($page->seo_image) : null;
@endphp

@section('content')
    @foreach ((array) $page->blocks as $block)
        <div data-reveal>
            @include('blocks.'.$block['type'], ['data' => $block['data'] ?? []])
        </div>
    @endforeach
@endsection
