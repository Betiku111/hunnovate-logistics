<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', UserRole::Customer->value)
            ->with(['customer'])
            ->withCount('orders')
            ->latest();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        return view('admin.customers.index', [
            'customers' => $query->paginate(20)->withQueryString(),
            'search'    => $search ?? '',
            'status'    => $status,
        ]);
    }

    public function show(User $customer): View
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->load(['customer']);

        $orders = Order::where('customer_id', $customer->id)
            ->latest()
            ->paginate(15);

        $spent = Order::where('customer_id', $customer->id)
            ->where('status', 'delivered')
            ->sum('total_fee');

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders'   => $orders,
            'spent'    => (float) $spent,
        ]);
    }

    public function toggleStatus(User $customer): RedirectResponse
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('success', $customer->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }
}