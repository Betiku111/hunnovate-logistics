<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RiderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Rider::query()
            ->with(['user'])
            ->with('vehicles')
            ->latest();

        if ($status = $request->input('status')) {
            if (RiderStatus::tryFrom($status)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return view('admin.riders.index', [
            'riders' => $query->paginate(20)->withQueryString(),
            'search' => $search ?? '',
            'status' => $status,
        ]);
    }

    public function show(Rider $rider): View
    {
        $rider->load(['user', 'vehicles', 'verifier']);

        $activeDeliveries = Order::where('rider_id', $rider->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $totalDelivered = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->count();

        $recentOrders = Order::where('rider_id', $rider->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.riders.show', [
            'rider'            => $rider,
            'activeDeliveries' => $activeDeliveries,
            'totalDelivered'   => $totalDelivered,
            'recentOrders'     => $recentOrders,
        ]);
    }

    public function updateStatus(Request $request, Rider $rider): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(RiderStatus::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $new = RiderStatus::from($data['status']);

        $updates = ['status' => $new->value];

        if ($new === RiderStatus::Verified) {
            $updates['verified_at'] = now();
            $updates['verified_by'] = $request->user()->id;
            $updates['rejection_reason'] = null;
        }

        if ($new === RiderStatus::Rejected) {
            $updates['rejection_reason'] = $data['reason'] ?? 'Rejected by admin.';
        }

        if (in_array($new, [RiderStatus::Suspended, RiderStatus::Rejected], true)) {
            $updates['is_online']    = false;
            $updates['is_available'] = false;
        }

        $rider->update($updates);

        return back()->with('success', "Rider status updated to {$new->label()}.");
    }
}