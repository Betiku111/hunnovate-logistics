@extends('layouts.admin')

@section('title', 'Outlets')
@section('page_title', 'Outlets')
@section('page_subtitle', 'Physical outlet locations')

@section('content')

    <div
        x-data="{ open: false }"
        class="grid lg:grid-cols-3 gap-5"
    >
        <div class="lg:col-span-2">
            <x-card padding="none">
                <div class="p-4 border-b border-[color:var(--color-border)]">
                    <form method="GET">
                        <input type="search" name="q" value="{{ $search }}" placeholder="Search outlets…"
                            class="w-full h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    </form>
                </div>

                @if($outlets->isEmpty())
                    <x-empty-state icon="building-2" title="No outlets" description="Add your first outlet." />
                @else
                    <div class="divide-y divide-[color:var(--color-border)]">
                        @foreach($outlets as $o)
                            <div class="p-5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-ink">{{ $o->name }}</p>
                                        <x-badge :color="$o->is_active ? 'success' : 'slate'" size="sm">{{ $o->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                    </div>
                                    <p class="text-xs text-ink-muted mt-0.5">{{ $o->code }} · {{ $o->city }}, {{ $o->state }}</p>
                                    <p class="text-xs text-ink-subtle mt-0.5">{{ $o->address }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.outlets.toggle', $o) }}">
                                    @csrf
                                    <button class="text-xs text-brand">{{ $o->is_active ? 'Disable' : 'Enable' }}</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 border-t border-[color:var(--color-border)]">
                        <x-pagination :paginator="$outlets" />
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-button @click="open = !open" full icon="plus">Add Outlet</x-button>

            <div x-show="open" x-cloak x-transition class="mt-4">
                <x-card padding="none">
                    <x-card-header title="New Outlet" />
                    <form method="POST" action="{{ route('admin.outlets.store') }}" class="p-5 space-y-3">
                        @csrf
                        <x-input name="name" label="Name" required />
                        <x-input name="code" label="Code" placeholder="HQ-001" required />
                        <x-input name="address" label="Address" required />
                        <div class="grid grid-cols-2 gap-3">
                            <x-input name="city" label="City" required />
                            <x-input name="state" label="State" required />
                        </div>
                        <x-input name="phone" label="Phone" />
                        <x-input name="email" type="email" label="Email" />
                        <x-button type="submit" full icon="check">Create</x-button>
                    </form>
                </x-card>
            </div>
        </div>
    </div>

@endsection