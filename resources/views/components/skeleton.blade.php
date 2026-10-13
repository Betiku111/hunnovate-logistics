@props(['variant' => 'line', 'lines' => 3])

@if($variant === 'line')
    <div {{ $attributes->merge(['class' => 'space-y-2']) }}>
        @for($i = 0; $i < $lines; $i++)
            <div class="h-3 bg-slate-200 rounded animate-pulse" style="width: {{ 100 - ($i * 10) }}%"></div>
        @endfor
    </div>
@elseif($variant === 'card')
    <div {{ $attributes->merge(['class' => 'bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5 space-y-4']) }}>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-200 animate-pulse"></div>
            <div class="flex-1 space-y-2">
                <div class="h-3 w-1/3 bg-slate-200 rounded animate-pulse"></div>
                <div class="h-3 w-1/2 bg-slate-200 rounded animate-pulse"></div>
            </div>
        </div>
        <div class="h-3 w-full bg-slate-200 rounded animate-pulse"></div>
        <div class="h-3 w-5/6 bg-slate-200 rounded animate-pulse"></div>
    </div>
@endif