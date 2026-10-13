@extends('layouts.rider')

@section('title', 'Profile')

@section('content')

    <div class="px-4 py-4">

        <div class="mb-4">
            <h1 class="text-xl font-semibold text-ink">Profile</h1>
        </div>

        {{-- Header card --}}
        <x-card padding="none" class="mb-4">
            <div class="p-5 flex items-center gap-4">
                @if($rider->profile_photo_path)
                    <img src="{{ Storage::url($rider->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 rounded-full object-cover">
                @else
                    <span class="w-16 h-16 rounded-full bg-brand text-white flex items-center justify-center text-lg font-bold">
                        {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                    </span>
                @endif

                <div class="min-w-0">
                    <p class="text-base font-semibold text-ink">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-ink-muted">{{ auth()->user()->email }}</p>
                    <div class="mt-2">
                        <x-status-badge :status="$rider->status" />
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Vehicle --}}
        @foreach($vehicles as $vehicle)
            <x-card padding="none" class="mb-4">
                <div class="p-5">
                    <p class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-3">Vehicle</p>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-ink-muted">Type</p>
                            <p class="font-medium text-ink mt-0.5">{{ $vehicle->type->label() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Plate</p>
                            <p class="font-medium text-ink mt-0.5">{{ $vehicle->plate_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Brand</p>
                            <p class="font-medium text-ink mt-0.5">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Color</p>
                            <p class="font-medium text-ink mt-0.5">{{ $vehicle->color }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-ink-muted">Capacity</p>
                            <p class="font-medium text-ink mt-0.5">{{ number_format($vehicle->capacity_kg) }} kg</p>
                        </div>
                    </div>
                </div>
            </x-card>
        @endforeach

        {{-- Update form --}}
        <form method="POST" action="{{ route('rider.profile.update') }}">
            @csrf

            <x-card padding="none" class="mb-4">
                <div class="p-5 space-y-4">
                    <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Contact</p>

                    <x-input name="home_address" label="Home Address" :value="$rider->home_address" required />
                    <x-input name="city" label="City" :value="$rider->city" required />
                    <x-input name="state" label="State" :value="$rider->state" required />
                </div>
            </x-card>

            <x-card padding="none" class="mb-4">
                <div class="p-5 space-y-4">
                    <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Emergency Contact</p>

                    <x-input name="emergency_contact_name" label="Contact Name" :value="$rider->emergency_contact_name" required />
                    <x-input name="emergency_contact_phone" label="Contact Phone" :value="$rider->emergency_contact_phone" required />
                </div>
            </x-card>

            <x-button type="submit" full size="lg" icon="check">
                Save Changes
            </x-button>
        </form>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full h-10 text-sm font-medium text-danger hover:bg-danger-bg rounded-[var(--radius)]">
                Sign Out
            </button>
        </form>
    </div>

@endsection