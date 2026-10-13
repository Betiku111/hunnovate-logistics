<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CustomerDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CustomerDashboardService $service): View
    {
        $user = $request->user();

        return view('customer.dashboard', [
            'metrics' => $service->metrics($user),
            'recentOrders' => $service->recentOrders($user, 5),
            'activeOrders' => $service->activeOrders($user, 3),
        ]);
    }
}
