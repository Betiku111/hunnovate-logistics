<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $rider = $request->user()->rider()->with('vehicles')->firstOrFail();

        return view('rider.profile', [
            'rider'    => $rider,
            'vehicles' => $rider->vehicles,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rider = $request->user()->rider;

        $data = $request->validate([
            'home_address'            => ['required', 'string', 'max:255'],
            'city'                    => ['required', 'string', 'max:80'],
            'state'                   => ['required', 'string', 'max:80'],
            'emergency_contact_name'  => ['required', 'string', 'max:120'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
        ]);

        $rider->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function toggleAvailability(Request $request): JsonResponse
    {
        $rider = $request->user()->rider;

        $rider->update([
            'is_online'    => ! $rider->is_online,
            'is_available' => ! $rider->is_online ? true : $rider->is_available,
        ]);

        return response()->json([
            'is_online'    => (bool) $rider->fresh()->is_online,
            'is_available' => (bool) $rider->fresh()->is_available,
        ]);
    }
}