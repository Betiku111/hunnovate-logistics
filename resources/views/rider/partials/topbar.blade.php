@php
    $rider = auth()->user()->rider;
    $isOnline = $rider?->is_online ?? false;
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-[color:var(--color-border)]">
    <div class="px-4 h-14 flex items-center justify-between gap-3">

        {{-- Left: greeting --}}
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-[10px] font-semibold shrink-0">
                {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
            </span>
            <div class="min-w-0">
                <p class="text-xs text-ink-muted leading-none">Hello,</p>
                <p class="text-sm font-semibold text-ink truncate leading-tight mt-0.5">
                    {{ explode(' ', auth()->user()->name)[0] }}
                </p>
            </div>
        </div>

        {{-- Right: online toggle + logout --}}
        <div class="flex items-center gap-2">
            <button
                x-data="{ online: {{ $isOnline ? 'true' : 'false' }}, saving: false }"
                @click="
                    saving = true;
                    fetch('{{ route('rider.availability.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    })
                    .then(r => r.json())
                    .then(d => { online = d.is_online; })
                    .finally(() => saving = false);
                "
                :class="online ? 'bg-success/10 text-success' : 'bg-slate-100 text-ink-muted'"
                class="inline-flex items-center gap-1.5 h-8 px-2.5 rounded-full text-xs font-medium transition-colors"
                :disabled="saving"
            >
                <span :class="online ? 'bg-success' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full"></span>
                <span x-text="online ? 'Online' : 'Offline'"></span>
            </button>

            <a href="{{ route('rider.profile') }}" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-ink-muted" aria-label="Profile">
                <x-icon name="user" :size="16" />
            </a>
        </div>
    </div>
</header>