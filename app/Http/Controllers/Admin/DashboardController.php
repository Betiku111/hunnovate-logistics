<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AdminDashboardService $service): View
    {
        return view('admin.dashboard', [
            'kpis'          => $service->kpis(),
            'distribution'  => $service->statusDistribution(),
            'revenueTrend'  => $service->revenueTrend(14),
            'recentOrders'  => $service->recentOrders(8),
            'activeRiders'  => $service->activeRiders(5),
        ]);
    }
}