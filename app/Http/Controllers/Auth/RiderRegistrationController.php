<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RiderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RiderRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.rider-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'                   => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'                => ['required', 'confirmed', Rules\Password::defaults()],

            'home_address'            => ['required', 'string', 'max:255'],
            'city'                    => ['required', 'string', 'max:80'],
            'state'                   => ['required', 'string', 'max:80'],
            'emergency_contact_name'  => ['required', 'string', 'max:120'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],

            'profile_photo'           => ['required', 'image', 'max:4096'],
            'government_id'           => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'rider_license'           => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],

            // Vehicle
            'vehicle_type'            => ['required', 'in:motorcycle,car,truck'],
            'vehicle_brand'           => ['required', 'string', 'max:80'],
            'vehicle_model'           => ['required', 'string', 'max:80'],
            'vehicle_color'           => ['required', 'string', 'max:40'],
            'vehicle_plate'           => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'vehicle_registration'    => ['nullable', 'string', 'max:60'],
            'vehicle_capacity_kg'     => ['required', 'integer', 'min:1', 'max:20000'],
        ]);

        $user = DB::transaction(function () use ($request, $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
                'role'     => UserRole::Rider->value,
            ]);

            $rider = Rider::create([
                'user_id'                 => $user->id,
                'status'                  => RiderStatus::Pending->value,
                'home_address'            => $data['home_address'],
                'city'                    => $data['city'],
                'state'                   => $data['state'],
                'emergency_contact_name'  => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],

                'profile_photo_path'      => $request->file('profile_photo')
                    ->store('riders/photos', 'public'),
                'government_id_path'      => $request->file('government_id')
                    ->store('riders/documents', 'public'),
                'rider_license_path'      => $request->file('rider_license')
                    ->store('riders/documents', 'public'),
            ]);

            Vehicle::create([
                'rider_id'             => $rider->id,
                'type'                 => $data['vehicle_type'],
                'brand'                => $data['vehicle_brand'],
                'model'                => $data['vehicle_model'],
                'color'                => $data['vehicle_color'],
                'plate_number'         => $data['vehicle_plate'],
                'registration_number'  => $data['vehicle_registration'] ?? null,
                'capacity_kg'          => $data['vehicle_capacity_kg'],
                'is_active'            => true,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('rider.pending')
            ->with('success', 'Registration submitted. Your account is pending verification.');
    }
}