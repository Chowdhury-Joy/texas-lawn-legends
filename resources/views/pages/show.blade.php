@extends('layouts.app')

@php
    $seoTitle = $page->seo_title ?: ($page->is_home ? null : $page->title);
    $seoDescription = $page->seo_description;
    $seoImage = filled($page->seo_image) ? public_url($page->seo_image) : null;
@endphp

@section('content')
    @foreach ((array) $page->blocks as $block)
        @php
            $blockType = $block['type'] ?? null;
            $isValid = $blockType && in_array($blockType, \App\Support\PageBlocks::all(), true) && view()->exists('blocks.'.$blockType);
        @endphp
        @if ($isValid)
            @include('blocks.'.$blockType, ['data' => array_merge($block['data'] ?? [], ['is_first_block' => $loop->first])])
        @endif
    @endforeach
@endsection
