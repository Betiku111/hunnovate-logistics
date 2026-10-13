@props(['status'])

@php
    $label = method_exists($status, 'label') ? $status->label() : (string) $status;
    $color = method_exists($status, 'color') ? $status->color() : 'slate';
@endphp

<x-badge :color="$color" dot>{{ $label }}</x-badge>