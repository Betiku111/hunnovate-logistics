@extends('layouts.admin')

@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', $subtitle ?? '')

@section('content')
    <x-card>
        <x-empty-state
            icon="construction"
            title="{{ $title }}"
            description="{{ $subtitle ?? 'This section is coming soon.' }}"
        />
    </x-card>
@endsection