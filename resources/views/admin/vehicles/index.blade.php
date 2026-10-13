@extends('layouts.admin')

@section('title', 'Vehicles')
@section('page_title', 'Vehicles')
@section('page_subtitle', 'All registered vehicles')

@section('content')

    <x-card padding="none">
        <div class="p-4 border-b border-[color:var(--color-border)]">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search plate, brand…"
                    class="flex-1 h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                <select name="type" class="h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    <option value="">All types</option>
                    @foreach(\App\Enums\VehicleType::cases() as $t)
                        <option value="{{ $t->value }}" @selected($type === $t->value)>{{ $t->label() }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary" icon="search" size="md">Filter</x-button>
            </form>
        </div>

        @if($vehicles->isEmpty())
            <x-empty-state icon="truck" title="No vehicles" description="Vehicles will appear here after riders register." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Plate</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Type</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Brand / Model</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Rider</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Capacity</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--color-border)]">
                        @foreach($vehicles as $v)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono text-xs font-medium">{{ $v->plate_number }}</td>
                                <td class="px-5 py-3 text-xs">{{ $v->type->label() }}</td>
                                <td class="px-5 py-3 hidden md:table-cell text-ink-muted text-xs">{{ $v->brand }} {{ $v->model }}</td>
                                <td class="px-5 py-3 hidden lg:table-cell text-xs">
                                    @if($v->rider)
                                        <a href="{{ route('admin.riders.show', $v->rider) }}" class="text-brand">{{ $v->rider->user->name }}</a>
                                    @else
                                        <span class="text-ink-subtle">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 hidden lg:table-cell text-ink-muted text-xs">{{ number_format($v->capacity_kg) }} kg</td>
                                <td class="px-5 py-3">
                                    <x-badge :color="$v->is_active ? 'success' : 'slate'" dot>{{ $v->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.vehicles.toggle', $v) }}" class="inline">
                                        @csrf
                                        <button class="text-xs text-brand hover:bg-brand-light rounded px-2.5 py-1.5">
                                            {{ $v->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 border-t border-[color:var(--color-border)]">
                <x-pagination :paginator="$vehicles" />
            </div>
        @endif
    </x-card>

@endsection