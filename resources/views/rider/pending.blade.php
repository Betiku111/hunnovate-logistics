@extends('layouts.rider')

@section('title', 'Pending Verification')

@section('content')

    <div class="px-4 py-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-warning-bg text-warning mb-4">
            <x-icon name="clock" :size="32" />
        </div>

        <h1 class="text-xl font-semibold text-ink">Account Pending Verification</h1>
        <p class="text-sm text-ink-muted mt-2 max-w-sm mx-auto leading-relaxed">
            Thank you for registering as a Hunnovate rider. Our team is reviewing your documents. You will be notified once your account is approved.
        </p>

        <div class="mt-8 max-w-sm mx-auto">
            <x-alert type="info" title="What happens next?">
                Verification typically takes 24–48 hours. You will receive an SMS and email once approved.
            </x-alert>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="text-sm text-ink-muted hover:text-ink">
                Sign out
            </button>
        </form>
    </div>

@endsection