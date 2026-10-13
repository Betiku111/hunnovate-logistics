<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink">Become a Hunnovate Rider</h1>
        <p class="text-sm text-ink-muted mt-1">Join our network and start earning.</p>
    </div>

    <form method="POST" action="{{ route('rider.register.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <h2 class="text-xs font-semibold text-ink-muted uppercase tracking-wider pt-2">Personal Info</h2>
        <x-input name="name" label="Full Name" required />
        <x-input name="email" type="email" label="Email" required />
        <x-input name="phone" label="Phone" required />
        <x-input name="password" type="password" label="Password" required />
        <x-input name="password_confirmation" type="password" label="Confirm Password" required />

        <h2 class="text-xs font-semibold text-ink-muted uppercase tracking-wider pt-4">Address</h2>
        <x-input name="home_address" label="Home Address" required />
        <div class="grid grid-cols-2 gap-3">
            <x-input name="city" label="City" required />
            <x-input name="state" label="State" required />
        </div>

        <h2 class="text-xs font-semibold text-ink-muted uppercase tracking-wider pt-4">Emergency Contact</h2>
        <x-input name="emergency_contact_name" label="Contact Name" required />
        <x-input name="emergency_contact_phone" label="Contact Phone" required />

        <h2 class="text-xs font-semibold text-ink-muted uppercase tracking-wider pt-4">Vehicle</h2>
        <x-select name="vehicle_type" label="Vehicle Type" :options="['motorcycle' => 'Motorcycle', 'car' => 'Car', 'truck' => 'Truck']" required />
        <div class="grid grid-cols-2 gap-3">
            <x-input name="vehicle_brand" label="Brand" placeholder="Honda" required />
            <x-input name="vehicle_model" label="Model" placeholder="CG125" required />
        </div>
        <div class="grid grid-cols-2 gap-3">
            <x-input name="vehicle_color" label="Color" required />
            <x-input name="vehicle_plate" label="Plate Number" required />
        </div>
        <x-input name="vehicle_registration" label="Registration No. (optional)" />
        <x-input name="vehicle_capacity_kg" type="number" label="Capacity (kg)" placeholder="20" required />

        <h2 class="text-xs font-semibold text-ink-muted uppercase tracking-wider pt-4">Documents</h2>
        <x-input name="profile_photo" type="file" label="Profile Photo" required accept="image/*" />
        <x-input name="government_id" type="file" label="Government ID" required accept="image/*,application/pdf" />
        <x-input name="rider_license" type="file" label="Rider License" required accept="image/*,application/pdf" />

        <x-button type="submit" full size="lg" icon="check" class="mt-4">
            Submit Application
        </x-button>

        <p class="text-xs text-ink-muted text-center pt-2">
            Already a rider?
            <a href="{{ route('login') }}" class="text-brand font-medium">Sign in</a>
        </p>
    </form>
</x-guest-layout>