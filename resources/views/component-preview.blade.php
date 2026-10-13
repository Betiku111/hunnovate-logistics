<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Component Preview</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 p-8 font-sans">
    <div class="max-w-5xl mx-auto space-y-10">

        <h1 class="text-3xl font-bold">Hunnovate Component Library</h1>

        {{-- Buttons --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Buttons</h2>
            <div class="flex flex-wrap gap-3">
                <x-button>Primary</x-button>
                <x-button variant="secondary">Secondary</x-button>
                <x-button variant="outline">Outline</x-button>
                <x-button variant="ghost">Ghost</x-button>
                <x-button variant="danger" icon="trash-2">Delete</x-button>
                <x-button variant="success" icon="check-circle">Approve</x-button>
                <x-button loading>Loading…</x-button>
                <x-button href="#" icon-after="arrow-right">Go Home</x-button>
            </div>
        </section>

        {{-- Badges --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Badges</h2>
            <div class="flex flex-wrap gap-3">
                <x-badge color="slate">Slate</x-badge>
                <x-badge color="success" dot>Delivered</x-badge>
                <x-badge color="warning" dot>In Transit</x-badge>
                <x-badge color="danger" dot>Cancelled</x-badge>
                <x-badge color="info" dot>Assigned</x-badge>
                <x-badge color="brand">New</x-badge>
            </div>
        </section>

        {{-- Alerts --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Alerts</h2>
            <div class="space-y-3">
                <x-alert type="success" title="Payment received">Your order has been confirmed.</x-alert>
                <x-alert type="warning" title="Rider arriving soon">Estimated arrival: 5 minutes.</x-alert>
                <x-alert type="danger" title="Delivery failed">Please contact support.</x-alert>
                <x-alert type="info" dismissible>Location tracking will begin once the rider is assigned.</x-alert>
            </div>
        </section>

        {{-- Form controls --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Form Controls</h2>
            <div class="grid sm:grid-cols-2 gap-5 max-w-2xl">
                <x-input name="full_name" label="Full name" placeholder="John Doe" required />
                <x-input name="email" type="email" label="Email" icon="mail" placeholder="you@example.com" />
                <x-input name="phone" label="Phone" icon="phone" placeholder="08012345678" />
                <x-input name="weight" type="number" label="Weight (kg)" hint="Max 20kg for motorcycle" />
                <x-select name="vehicle" label="Vehicle type" :options="['motorcycle' => 'Motorcycle', 'car' => 'Car', 'truck' => 'Truck']" placeholder="Choose…" />
                <x-select name="city" label="City" :options="['lagos' => 'Lagos', 'abuja' => 'Abuja']" />
            </div>
            <div class="max-w-2xl mt-5">
                <x-textarea name="notes" label="Delivery notes" placeholder="Anything the rider should know…" />
            </div>
            <div class="mt-5 max-w-2xl">
                <x-checkbox name="agree" label="I agree to the terms" hint="You must accept before proceeding" />
            </div>
        </section>

        {{-- Cards --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Cards</h2>
            <div class="grid sm:grid-cols-3 gap-5">
                <x-stat-card label="Total Orders" value="1,245" icon="package" delta="+12%" trend="up" hint="vs last week" />
                <x-stat-card label="Active Riders" value="87" icon="truck" color="info" />
                <x-stat-card label="Revenue" value="₦4.2M" icon="wallet" color="success" delta="+8%" />
            </div>
        </section>

        {{-- Empty / Error --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Empty & Error States</h2>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-card>
                    <x-empty-state icon="inbox" title="No orders yet" description="Create your first order to get started.">
                        <x-slot:action>
                            <x-button icon="plus">New Order</x-button>
                        </x-slot:action>
                    </x-empty-state>
                </x-card>
                <x-card>
                    <x-error-state />
                </x-card>
            </div>
        </section>

        {{-- Modal demo --}}
        <section x-data>
            <h2 class="text-xl font-semibold mb-3">Modal</h2>
            <x-button @click="$dispatch('open-modal', 'demo-modal')">Open Modal</x-button>
            <x-modal name="demo-modal" title="Confirm action">
                <p class="text-sm text-ink-muted">This is a demo modal. It closes on ESC or backdrop click.</p>
                <x-slot:footer>
                    <x-button variant="outline" @click="$dispatch('close-modal', 'demo-modal')">Cancel</x-button>
                    <x-button variant="danger">Confirm</x-button>
                </x-slot:footer>
            </x-modal>
        </section>

        {{-- Loading --}}
        <section>
            <h2 class="text-xl font-semibold mb-3">Loading</h2>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-card><x-skeleton :lines="3" /></x-card>
                <x-card><x-skeleton variant="card" /></x-card>
            </div>
        </section>

    </div>
</body>
</html>