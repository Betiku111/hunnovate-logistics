@props([
    'name',
    'size' => 20,
    'stroke' => 2,
])

@php
    $svg = app(\App\Services\IconService::class)->svg($name, [
        'width'        => $size,
        'height'       => $size,
        'stroke-width' => $stroke,
        'class'        => $attributes->get('class', ''),
    ]);
@endphp

{!! $svg !!}