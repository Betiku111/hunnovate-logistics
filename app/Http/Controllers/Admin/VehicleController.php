<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::query()->with(['rider.user'])->latest();

        if ($type = $request->input('type')) {
            if (VehicleType::tryFrom($type)) {
                $query->where('type', $type);
            }
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        return view('admin.vehicles.index', [
            'vehicles' => $query->paginate(20)->withQueryString(),
            'search'   => $search ?? '',
            'type'     => $type,
        ]);
    }

    public function toggleActive(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update(['is_active' => ! $vehicle->is_active]);

        return back()->with('success', 'Vehicle status updated.');
    }
}