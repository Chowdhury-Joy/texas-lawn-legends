@props(['name' => 'heroicon-o-squares-plus', 'class' => 'h-8 w-8'])
@php
    try {
        $html = svg($name, $class)->toHtml();
    } catch (\Throwable $e) {
        $html = svg('heroicon-o-squares-plus', $class)->toHtml();
    }
@endphp
{!! $html !!}
