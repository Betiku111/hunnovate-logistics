@extends('layouts.admin')

@section('title', 'Customers')
@section('page_title', 'Customers')
@section('page_subtitle', 'Manage customer accounts')

@section('content')

    <x-card padding="none">
        <div class="p-4 border-b border-[color:var(--color-border)]">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by name, email, phone…"
                    class="flex-1 h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none">
                <select name="status" class="h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    <option value="">All</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
                <x-button type="submit" variant="secondary" icon="search" size="md">Filter</x-button>
            </form>
        </div>

        @if($customers->isEmpty())
            <x-empty-state icon="users" title="No customers found" description="Customers will appear here after registration." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Customer</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Phone</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Orders</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Joined</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--color-border)]">
                        @foreach($customers as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-[10px] font-semibold">
                                            {{ collect(explode(' ', $c->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-ink truncate">{{ $c->name }}</p>
                                            <p class="text-xs text-ink-muted truncate">{{ $c->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-ink-muted hidden md:table-cell">{{ $c->phone ?? '—' }}</td>
                                <td class="px-5 py-3 text-ink-muted hidden lg:table-cell">{{ $c->orders_count }}</td>
                                <td class="px-5 py-3">
                                    <x-badge :color="$c->is_active ? 'success' : 'slate'" dot>{{ $c->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-ink-muted text-xs">{{ $c->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.customers.show', $c) }}" class="inline-flex items-center h-8 px-2.5 text-xs text-brand hover:bg-brand-light rounded">View</a>
                                        <form method="POST" action="{{ route('admin.customers.toggle', $c) }}">
                                            @csrf
                                            <button class="inline-flex items-center h-8 px-2.5 text-xs {{ $c->is_active ? 'text-danger hover:bg-danger-bg' : 'text-success hover:bg-success-bg' }} rounded">
                                                {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 border-t border-[color:var(--color-border)]">
                <x-pagination :paginator="$customers" />
            </div>
        @endif
    </x-card>

@endsection