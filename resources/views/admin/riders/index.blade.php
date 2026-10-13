@extends('layouts.admin')

@section('title', 'Riders')
@section('page_title', 'Riders')
@section('page_subtitle', 'Manage rider accounts and verification')

@section('content')

    <x-card padding="none">
        <div class="p-4 border-b border-[color:var(--color-border)]">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search riders…"
                    class="flex-1 h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                <select name="status" class="h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\RiderStatus::cases() as $s)
                        <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary" icon="search" size="md">Filter</x-button>
            </form>
        </div>

        @if($riders->isEmpty())
            <x-empty-state icon="bike" title="No riders found" description="Riders will appear here after registration." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Rider</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Vehicle</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Deliveries</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Availability</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--color-border)]">
                        @foreach($riders as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-[10px] font-semibold">
                                            {{ collect(explode(' ', $r->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-ink truncate">{{ $r->user->name }}</p>
                                            <p class="text-xs text-ink-muted truncate">{{ $r->user->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 hidden md:table-cell">
                                    @php $v = $r->vehicles->first(); @endphp
                                    @if($v)
                                        <span class="text-xs text-ink-muted">{{ $v->type->label() }} · {{ $v->plate_number }}</span>
                                    @else
                                        <span class="text-xs text-ink-subtle">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 hidden lg:table-cell text-ink-muted text-xs">{{ $r->total_deliveries }}</td>
                                <td class="px-5 py-3"><x-status-badge :status="$r->status" /></td>
                                <td class="px-5 py-3 hidden lg:table-cell">
                                    <x-badge :color="$r->is_online ? 'success' : 'slate'" dot>{{ $r->is_online ? 'Online' : 'Offline' }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.riders.show', $r) }}" class="inline-flex items-center h-8 px-2.5 text-xs text-brand hover:bg-brand-light rounded">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 border-t border-[color:var(--color-border)]">
                <x-pagination :paginator="$riders" />
            </div>
        @endif
    </x-card>

@endsection