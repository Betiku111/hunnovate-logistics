@extends('layouts.admin')

@section('title', $rider->user->name)
@section('page_title', $rider->user->name)
@section('page_subtitle', 'Rider details and verification')

@section('content')

    <div class="grid lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">

            {{-- Status banner --}}
            <x-card padding="none">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="w-14 h-14 rounded-full bg-brand text-white flex items-center justify-center text-lg font-semibold">
                            {{ collect(explode(' ', $rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                        </span>
                        <div>
                            <p class="font-mono text-sm text-ink-muted">{{ $rider->user->email }}</p>
                            <p class="text-xs text-ink-muted mt-0.5">{{ $rider->user->phone }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <x-status-badge :status="$rider->status" />
                                @if($rider->is_online)
                                    <x-badge color="success" dot>Online</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-ink">★ {{ number_format((float) $rider->rating, 1) }}</p>
                        <p class="text-xs text-ink-muted">{{ $rider->total_deliveries }} deliveries</p>
                    </div>
                </div>
            </x-card>

            {{-- Documents --}}
            @if($rider->status === \App\Enums\RiderStatus::Pending)
                <x-alert type="warning" title="Pending verification">
                    Review the rider's documents below and approve or reject.
                </x-alert>
            @endif

            <x-card padding="none">
                <x-card-header title="Documents" subtitle="Uploaded at registration" />
                <div class="p-5 grid sm:grid-cols-3 gap-4">
                    @foreach([
                        'Profile Photo'   => $rider->profile_photo_path,
                        'Government ID'   => $rider->government_id_path,
                        'Rider License'   => $rider->rider_license_path,
                    ] as $label => $path)
                        <div class="border border-[color:var(--color-border)] rounded-[var(--radius)] overflow-hidden">
                            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider px-3 py-2 bg-slate-50 border-b border-[color:var(--color-border)]">{{ $label }}</p>
                            @if($path)
                                @php $ext = pathinfo($path, PATHINFO_EXTENSION); @endphp
                                @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                                    <a href="{{ Storage::url($path) }}" target="_blank" class="block aspect-square bg-slate-100">
                                        <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <a href="{{ Storage::url($path) }}" target="_blank" class="block aspect-square flex items-center justify-center bg-slate-100 hover:bg-slate-200 transition-colors">
                                        <x-icon name="file-text" :size="32" class="text-ink-subtle" />
                                    </a>
                                @endif
                            @else
                                <div class="aspect-square flex items-center justify-center bg-slate-50">
                                    <p class="text-xs text-ink-subtle">Not uploaded</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>

            {{-- Vehicles --}}
            <x-card padding="none">
                <x-card-header title="Vehicles" />
                @foreach($rider->vehicles as $vehicle)
                    <div class="p-5 {{ ! $loop->last ? 'border-b border-[color:var(--color-border)]' : '' }}">
                        <div class="grid sm:grid-cols-3 gap-3 text-sm">
                            <div><p class="text-xs text-ink-muted">Type</p><p class="font-medium text-ink mt-0.5">{{ $vehicle->type->label() }}</p></div>
                            <div><p class="text-xs text-ink-muted">Plate</p><p class="font-medium text-ink mt-0.5">{{ $vehicle->plate_number }}</p></div>
                            <div><p class="text-xs text-ink-muted">Brand/Model</p><p class="font-medium text-ink mt-0.5">{{ $vehicle->brand }} {{ $vehicle->model }}</p></div>
                        </div>
                    </div>
                @endforeach
            </x-card>

            {{-- Recent orders --}}
            <x-card padding="none">
                <x-card-header title="Recent Deliveries" />
                @if($recentOrders->isEmpty())
                    <x-empty-state icon="package" title="No deliveries" description="This rider has not completed any deliveries yet." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                                <tr>
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Tracking</th>
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                                    <th class="text-right px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Fee</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[color:var(--color-border)]">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-2.5"><a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs text-brand">{{ $order->tracking_number }}</a></td>
                                        <td class="px-5 py-2.5"><x-status-badge :status="$order->status" /></td>
                                        <td class="px-5 py-2.5 text-right font-medium text-xs">₦{{ number_format($order->total_fee, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Actions sidebar --}}
        <div class="space-y-5">
            <x-card padding="none">
                <x-card-header title="Verification" />
                <div class="p-5 space-y-4">
                    @if($rider->status === \App\Enums\RiderStatus::Pending)
                        <p class="text-sm text-ink-muted">Review the documents and confirm this rider's identity.</p>

                        <form method="POST" action="{{ route('admin.riders.status', $rider) }}">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <x-button type="submit" variant="success" full icon="check">
                                Approve Rider
                            </x-button>
                        </form>

                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="text-xs text-danger hover:underline">
                                Reject application
                            </button>
                            <div x-show="open" x-cloak class="mt-3 space-y-3">
                                <form method="POST" action="{{ route('admin.riders.status', $rider) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <x-textarea name="reason" label="Rejection reason" rows="3" required />
                                    <x-button type="submit" variant="danger" full class="mt-3">Confirm Rejection</x-button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($rider->status === \App\Enums\RiderStatus::Verified)
                        <form method="POST" action="{{ route('admin.riders.status', $rider) }}">
                            @csrf
                            <input type="hidden" name="status" value="suspended">
                            <x-button type="submit" variant="danger" full icon="ban">
                                Suspend Rider
                            </x-button>
                        </form>
                    @endif

                    @if(in_array($rider->status, [\App\Enums\RiderStatus::Rejected, \App\Enums\RiderStatus::Suspended], true))
                        <form method="POST" action="{{ route('admin.riders.status', $rider) }}">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <x-button type="submit" variant="success" full icon="check">
                                Re-activate Rider
                            </x-button>
                        </form>
                    @endif

                    @if($rider->rejection_reason)
                        <x-alert type="danger" title="Rejection reason">
                            {{ $rider->rejection_reason }}
                        </x-alert>
                    @endif
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Stats" />
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-ink-muted">Active Deliveries</span><span class="font-semibold">{{ $activeDeliveries }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Completed</span><span class="font-semibold text-success">{{ $totalDelivered }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Rating</span><span class="font-semibold">★ {{ number_format((float) $rider->rating, 1) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Registered</span><span class="font-medium">{{ $rider->created_at->format('M d, Y') }}</span></div>
                    @if($rider->verified_at)
                        <div class="flex justify-between"><span class="text-ink-muted">Verified</span><span class="font-medium text-success">{{ $rider->verified_at->format('M d, Y') }}</span></div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

@endsection