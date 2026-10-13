@props(['paginator'])

@if($paginator->hasPages())
    <nav class="flex flex-col sm:flex-row items-center justify-between gap-3 py-4">
        <p class="text-xs text-ink-muted">
            Showing <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
            to <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
            of <span class="font-medium text-ink">{{ $paginator->total() }}</span> results
        </p>

        <div class="flex items-center gap-1">
            @if($paginator->onFirstPage())
                <span class="h-8 px-3 inline-flex items-center text-xs text-ink-subtle border border-[color:var(--color-border)] rounded-[var(--radius)] opacity-50">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="h-8 px-3 inline-flex items-center text-xs text-ink border border-[color:var(--color-border-strong)] rounded-[var(--radius)] hover:bg-slate-50">Prev</a>
            @endif

            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="h-8 w-8 inline-flex items-center justify-center text-xs font-medium bg-brand text-white rounded-[var(--radius)]">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="h-8 w-8 inline-flex items-center justify-center text-xs text-ink border border-[color:var(--color-border-strong)] rounded-[var(--radius)] hover:bg-slate-50">{{ $page }}</a>
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="h-8 px-3 inline-flex items-center text-xs text-ink border border-[color:var(--color-border-strong)] rounded-[var(--radius)] hover:bg-slate-50">Next</a>
            @else
                <span class="h-8 px-3 inline-flex items-center text-xs text-ink-subtle border border-[color:var(--color-border)] rounded-[var(--radius)] opacity-50">Next</span>
            @endif
        </div>
    </nav>
@endif