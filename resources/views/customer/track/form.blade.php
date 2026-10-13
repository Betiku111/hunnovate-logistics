@extends('layouts.public')

@section('title', 'Track Your Order — Hunnovate Logistics')
@section('meta_description', 'Enter your Hunnovate tracking number to see your delivery status in real time.')

@section('content')
    <section class="py-20 sm:py-24 bg-slate-50 min-h-[60vh]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-light text-brand mb-5">
                    <x-icon name="map-pin" :size="26" />
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-ink tracking-tight">Track your delivery</h1>
                <p class="mt-3 text-base text-ink-muted">Enter your tracking number to see live status.</p>
            </div>

            <x-card padding="lg">
                <form method="GET" action="" onsubmit="event.preventDefault(); const v = this.tracking.value.trim(); if (v) window.location.href = '/track/' + encodeURIComponent(v);" class="space-y-4">
                    <x-input
                        name="tracking"
                        label="Tracking Number"
                        placeholder="HNL-2026-000245"
                        icon="hash"
                        required
                    />

                    <x-button type="submit" icon="search" full size="lg">
                        Track Order
                    </x-button>
                </form>

                <p class="mt-5 text-xs text-ink-muted text-center">
                    Your tracking number starts with <span class="font-mono font-medium text-ink">HNL-</span> and was sent to you when the order was created.
                </p>
            </x-card>

            <div class="mt-8 text-center">
                <a href="{{ route('home') }}" class="text-sm text-ink-muted hover:text-ink inline-flex items-center gap-1.5">
                    <x-icon name="arrow-left" :size="14" />
                    Back to home
                </a>
            </div>
        </div>
    </section>
@endsection