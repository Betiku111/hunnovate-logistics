@extends('layouts.customer')

@section('title', 'Create Order')
@section('page_title', 'Create a New Order')
@section('page_subtitle', 'Complete the steps below to schedule your delivery.')

@section('content')

<div
    x-data="orderWizard({
        estimateUrl: '{{ route('customer.orders.estimate-fee') }}',
        csrfToken: '{{ csrf_token() }}',
        old: {{ json_encode(old()) }},
    })"
    x-init="init()"
    class="max-w-3xl mx-auto"
>

    {{-- Step indicator --}}
    <div class="mb-6 bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 overflow-x-auto scrollbar-thin">
        <ol class="flex items-center gap-2 min-w-max">
            <template x-for="(s, idx) in steps" :key="s.key">
                <li class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="goTo(idx)"
                        :disabled="idx > maxReachedStep"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-[var(--radius)] text-xs font-medium whitespace-nowrap transition-colors disabled:cursor-not-allowed"
                        :class="{
                            'bg-brand text-white': step === idx,
                            'bg-brand-light text-brand': step > idx,
                            'text-ink-muted hover:bg-slate-100': step < idx && idx <= maxReachedStep,
                            'text-ink-subtle opacity-60': idx > maxReachedStep,
                        }"
                    >
                        <span
                            class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                            :class="{
                                'bg-white text-brand': step >= idx,
                                'bg-slate-200 text-ink-muted': step < idx,
                            }"
                            x-text="idx + 1"
                        ></span>
                        <span x-text="s.label"></span>
                    </button>

                    <template x-if="idx < steps.length - 1">
                        <span class="w-4 h-px bg-slate-300"></span>
                    </template>
                </li>
            </template>
        </ol>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('customer.orders.store') }}" @submit.prevent="submit($el)">
        @csrf

        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-card)]">

            {{-- ============================================================ --}}
            {{-- STEP 0 — DELIVERY TYPE --}}
            {{-- ============================================================ --}}
            <div x-show="step === 0" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">What type of delivery?</h2>
                    <p class="text-sm text-ink-muted mt-1">Choose the vehicle based on your package size and urgency.</p>
                </header>

                <div class="grid sm:grid-cols-3 gap-4">
                    <template x-for="v in vehicleOptions" :key="v.value">
                        <label
                            class="cursor-pointer border-2 rounded-[var(--radius-lg)] p-4 transition-all"
                            :class="form.vehicle_type === v.value
                                ? 'border-brand bg-brand-light'
                                : 'border-[color:var(--color-border)] hover:border-brand/40'"
                        >
                            <input type="radio" name="vehicle_type" :value="v.value" x-model="form.vehicle_type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-[10px] flex items-center justify-center"
                                    :class="form.vehicle_type === v.value ? 'bg-brand text-white' : 'bg-slate-100 text-ink-muted'">
                                    <svg x-html="iconSvg(v.icon)" width="20" height="20"></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-ink" x-text="v.label"></p>
                                    <p class="text-xs text-ink-muted" x-text="v.capacity"></p>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-ink-muted leading-relaxed" x-text="v.desc"></p>
                        </label>
                    </template>
                </div>

                {{-- Express toggle --}}
                <label class="mt-6 flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_express" x-model="form.is_express" value="1" class="mt-0.5 h-4 w-4 rounded border-[color:var(--color-border-strong)] text-brand focus:ring-brand">
                    <span>
                        <span class="block text-sm font-medium text-ink">Express delivery (+50% base fee)</span>
                        <span class="block text-xs text-ink-muted">Priority pickup and delivery — rider assigned immediately.</span>
                    </span>
                </label>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 1 — PICKUP --}}
            {{-- ============================================================ --}}
            <div x-show="step === 1" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">Pickup details</h2>
                    <p class="text-sm text-ink-muted mt-1">Where should the rider collect the package?</p>
                </header>

                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="pickup_contact_name" label="Contact Name" placeholder="Full name" required x-model="form.pickup_contact_name" />
                    <x-input name="pickup_contact_phone" label="Contact Phone" placeholder="08012345678" required x-model="form.pickup_contact_phone" />
                    <x-input name="pickup_address" label="Street Address" placeholder="12 Marina Road" required class="sm:col-span-2" x-model="form.pickup_address" />
                    <x-input name="pickup_city" label="City" placeholder="Lagos" required x-model="form.pickup_city" />
                    <x-input name="pickup_state" label="State" placeholder="Lagos" required x-model="form.pickup_state" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button type="button" @click="useMyLocation('pickup')"
                        class="text-xs font-medium text-brand hover:text-brand-hover inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/><circle cx="12" cy="12" r="3"/></svg>
                        Use my current location
                    </button>
                    <p class="text-xs text-ink-muted" x-show="coords.pickup.lat" x-text="coords.pickup.lat ? 'Coords captured ✓' : ''"></p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 2 — RECIPIENT --}}
            {{-- ============================================================ --}}
            <div x-show="step === 2" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">Delivery recipient</h2>
                    <p class="text-sm text-ink-muted mt-1">Who is receiving the package?</p>
                </header>

                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="dropoff_contact_name" label="Recipient Name" placeholder="Full name" required x-model="form.dropoff_contact_name" />
                    <x-input name="dropoff_contact_phone" label="Recipient Phone" placeholder="08012345678" required x-model="form.dropoff_contact_phone" />
                    <x-input name="dropoff_address" label="Street Address" placeholder="45 Broad Street" required class="sm:col-span-2" x-model="form.dropoff_address" />
                    <x-input name="dropoff_city" label="City" placeholder="Lagos" required x-model="form.dropoff_city" />
                    <x-input name="dropoff_state" label="State" placeholder="Lagos" required x-model="form.dropoff_state" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button type="button" @click="useMyLocation('dropoff')"
                        class="text-xs font-medium text-brand hover:text-brand-hover inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/><circle cx="12" cy="12" r="3"/></svg>
                        Use this location
                    </button>
                    <p class="text-xs text-ink-muted" x-show="coords.dropoff.lat" x-text="coords.dropoff.lat ? 'Coords captured ✓' : ''"></p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 3 — PACKAGE --}}
            {{-- ============================================================ --}}
            <div x-show="step === 3" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">Package details</h2>
                    <p class="text-sm text-ink-muted mt-1">Help us route your package correctly.</p>
                </header>

                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="package_description" label="Description" placeholder="Documents, small box…" class="sm:col-span-2" x-model="form.package_description" />
                    <x-input name="package_weight_kg" type="number" step="0.1" label="Weight (kg)" placeholder="1.5" required x-model.number="form.package_weight_kg" hint="Motorcycle: ≤20kg · Car: ≤200kg · Truck: ≤5,000kg" />
                    <div></div>
                    <x-input name="package_length_cm" type="number" step="1" label="Length (cm)" placeholder="Optional" x-model.number="form.package_length_cm" />
                    <x-input name="package_width_cm" type="number" step="1" label="Width (cm)" placeholder="Optional" x-model.number="form.package_width_cm" />
                    <x-input name="package_height_cm" type="number" step="1" label="Height (cm)" placeholder="Optional" x-model.number="form.package_height_cm" />
                    <div></div>
                </div>

                <label class="mt-6 flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_fragile" x-model="form.is_fragile" value="1" class="mt-0.5 h-4 w-4 rounded border-[color:var(--color-border-strong)] text-brand focus:ring-brand">
                    <span>
                        <span class="block text-sm font-medium text-ink">Fragile item (+₦500)</span>
                        <span class="block text-xs text-ink-muted">Rider will handle with extra care. Comes with priority handling.</span>
                    </span>
                </label>

                <div class="mt-6">
                    <x-textarea name="special_instructions" label="Special instructions (optional)" rows="3" placeholder="Leave at the front desk, call on arrival…" x-model="form.special_instructions" />
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 4 — REVIEW & FEE --}}
            {{-- ============================================================ --}}
            <div x-show="step === 4" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">Review & fee</h2>
                    <p class="text-sm text-ink-muted mt-1">Verify the details before confirming.</p>
                </header>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Summary --}}
                    <div class="space-y-5">
                        <div>
                            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-2">Route</p>
                            <div class="space-y-3">
                                <div class="flex gap-3">
                                    <span class="w-2 h-2 rounded-full bg-brand mt-1.5 shrink-0"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-ink" x-text="form.pickup_contact_name || '—'"></p>
                                        <p class="text-xs text-ink-muted" x-text="form.pickup_address"></p>
                                        <p class="text-xs text-ink-subtle" x-text="`${form.pickup_city}, ${form.pickup_state}`"></p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <span class="w-2 h-2 rounded-full bg-success mt-1.5 shrink-0"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-ink" x-text="form.dropoff_contact_name || '—'"></p>
                                        <p class="text-xs text-ink-muted" x-text="form.dropoff_address"></p>
                                        <p class="text-xs text-ink-subtle" x-text="`${form.dropoff_city}, ${form.dropoff_state}`"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-2">Package</p>
                            <div class="text-sm space-y-1">
                                <p><span class="text-ink-muted">Vehicle:</span> <span class="font-medium text-ink" x-text="vehicleLabel"></span></p>
                                <p><span class="text-ink-muted">Weight:</span> <span class="font-medium text-ink" x-text="`${form.package_weight_kg} kg`"></span></p>
                                <p x-show="form.is_fragile"><span class="text-ink-muted">Fragile handling</span></p>
                                <p x-show="form.is_express"><span class="text-ink-muted">Express delivery</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Fee breakdown --}}
                    <div class="bg-slate-50 border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5">
                        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-3">Fee Breakdown</p>

                        <div x-show="estimating" class="py-8 text-center">
                            <svg class="animate-spin inline-block text-brand" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <p class="text-xs text-ink-muted mt-2">Calculating fee…</p>
                        </div>

                        <template x-if="fee && !estimating">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-ink-muted">Base fee</span>
                                    <span class="font-medium" x-text="formatNaira(fee.base_fee)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-muted">Distance (<span x-text="fee.distance_km"></span> km)</span>
                                    <span class="font-medium" x-text="formatNaira(fee.distance_fee)"></span>
                                </div>
                                <div class="flex justify-between" x-show="fee.weight_fee > 0">
                                    <span class="text-ink-muted">Weight surcharge</span>
                                    <span class="font-medium" x-text="formatNaira(fee.weight_fee)"></span>
                                </div>
                                <div class="flex justify-between" x-show="fee.express_fee > 0">
                                    <span class="text-ink-muted">Express (+50%)</span>
                                    <span class="font-medium" x-text="formatNaira(fee.express_fee)"></span>
                                </div>
                                <div class="flex justify-between" x-show="fee.fragile_fee > 0">
                                    <span class="text-ink-muted">Fragile handling</span>
                                    <span class="font-medium" x-text="formatNaira(fee.fragile_fee)"></span>
                                </div>
                                <div class="flex justify-between pt-3 mt-3 border-t border-[color:var(--color-border)]">
                                    <span class="font-semibold text-ink">Total</span>
                                    <span class="font-bold text-brand text-lg" x-text="formatNaira(fee.total_fee)"></span>
                                </div>
                                <p class="text-xs text-ink-muted pt-2" x-show="fee.eta_minutes">
                                    Estimated delivery time: <span class="font-medium text-ink" x-text="`${fee.eta_minutes} min`"></span>
                                </p>
                            </div>
                        </template>

                        <div x-show="!estimating && feeError" class="text-center py-6 text-sm text-red-600" x-text="feeError"></div>

                        <div x-show="!estimating && !fee && !feeError" class="text-center py-6 text-sm text-ink-muted">
                            Enter package details to see the fee.
                        </div>

                        <button type="button" @click="estimateFee()"
                            class="mt-4 w-full text-xs font-medium text-brand hover:text-brand-hover py-2">
                            <span x-show="!estimating">↻ Recalculate</span>
                            <span x-show="estimating">Calculating…</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 5 — CONFIRM --}}
            {{-- ============================================================ --}}
            <div x-show="step === 5" x-cloak class="p-6 sm:p-8">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-ink">Payment method</h2>
                    <p class="text-sm text-ink-muted mt-1">Choose how you would like to pay.</p>
                </header>

                <div class="grid sm:grid-cols-2 gap-3">
                    <template x-for="m in paymentMethods" :key="m.value">
                        <label
                            class="cursor-pointer border-2 rounded-[var(--radius-lg)] p-4 transition-all flex items-center gap-3"
                            :class="form.payment_method === m.value
                                ? 'border-brand bg-brand-light'
                                : 'border-[color:var(--color-border)] hover:border-brand/40'"
                        >
                            <input type="radio" name="payment_method" :value="m.value" x-model="form.payment_method" class="sr-only">
                            <span class="w-9 h-9 rounded-[10px] flex items-center justify-center shrink-0"
                                :class="form.payment_method === m.value ? 'bg-brand text-white' : 'bg-slate-100 text-ink-muted'">
                                <svg x-html="iconSvg(m.icon)" width="18" height="18"></svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-ink" x-text="m.label"></p>
                                <p class="text-xs text-ink-muted" x-text="m.desc"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <p class="mt-6 text-xs text-ink-muted leading-relaxed">
                    <span class="font-medium text-ink">Note:</span> Payment processing happens after order confirmation. You will receive a payment link or be able to pay via your wallet.
                </p>
            </div>

            {{-- ============================================================ --}}
            {{-- FOOTER CONTROLS --}}
            {{-- ============================================================ --}}
            <div class="border-t border-[color:var(--color-border)] px-6 sm:px-8 py-5 flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="prev()"
                    x-show="step > 0"
                    class="inline-flex items-center gap-2 h-10 px-4 text-sm font-medium text-ink-muted hover:text-ink rounded-[var(--radius)] hover:bg-slate-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Back
                </button>

                <div class="ml-auto flex items-center gap-3">
                    <template x-if="step < steps.length - 1">
                        <button
                            type="button"
                            @click="next()"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-[var(--radius)] bg-brand text-white text-sm font-medium hover:bg-brand-hover transition-colors"
                        >
                            Continue
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </template>

                    <template x-if="step === steps.length - 1">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-[var(--radius)] bg-brand text-white text-sm font-medium hover:bg-brand-hover transition-colors disabled:opacity-50"
                        >
                            <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <svg x-show="submitting" class="animate-spin" width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="submitting ? 'Creating order…' : 'Confirm & Create Order'"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Hidden inputs mirror Alpine state --}}
        <input type="hidden" name="pickup_lat"  :value="coords.pickup.lat">
        <input type="hidden" name="pickup_lng"  :value="coords.pickup.lng">
        <input type="hidden" name="dropoff_lat" :value="coords.dropoff.lat">
        <input type="hidden" name="dropoff_lng" :value="coords.dropoff.lng">
    </form>

    {{-- Server-side validation errors --}}
    @if ($errors->any())
        <div class="mt-5">
            <x-alert type="danger" title="Please fix the following errors:">
                <ul class="list-disc list-inside space-y-0.5 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif
</div>

@push('scripts')
<script>
function orderWizard(config) {
    return {
        step: 0,
        maxReachedStep: 0,
        submitting: false,
        estimating: false,
        fee: null,
        feeError: null,

        steps: [
            { key: 'type',    label: 'Type' },
            { key: 'pickup',  label: 'Pickup' },
            { key: 'dropoff', label: 'Recipient' },
            { key: 'package', label: 'Package' },
            { key: 'review',  label: 'Review' },
            { key: 'payment', label: 'Payment' },
        ],

        vehicleOptions: [
            { value: 'motorcycle', label: 'Motorcycle', icon: 'bike',       capacity: 'Up to 20 kg',    desc: 'Fast local delivery. Best for urgent documents and small packages.' },
            { value: 'car',        label: 'Car',        icon: 'car',        capacity: 'Up to 200 kg',   desc: 'Reliable vehicle delivery for medium packages and business items.' },
            { value: 'truck',      label: 'Truck',      icon: 'truck',      capacity: 'Up to 5,000 kg', desc: 'Heavy cargo and long-distance shipments.' },
        ],

        paymentMethods: [
            { value: 'paystack',    label: 'Paystack',        desc: 'Card, bank, USSD',       icon: 'credit-card' },
            { value: 'flutterwave', label: 'Flutterwave',     desc: 'Card, bank transfer',    icon: 'credit-card' },
            { value: 'wallet',      label: 'Hunnovate Wallet', desc: 'Pay from your balance',  icon: 'wallet' },
            { value: 'cash',        label: 'Cash on Delivery', desc: 'Pay the rider directly', icon: 'banknote' },
        ],

        form: {
            vehicle_type:         'motorcycle',
            is_express:           false,
            pickup_contact_name:  '',
            pickup_contact_phone: '',
            pickup_address:       '',
            pickup_city:          '',
            pickup_state:         '',
            dropoff_contact_name:  '',
            dropoff_contact_phone: '',
            dropoff_address:       '',
            dropoff_city:          '',
            dropoff_state:         '',
            package_description:   '',
            package_weight_kg:     1,
            package_length_cm:     null,
            package_width_cm:      null,
            package_height_cm:     null,
            is_fragile:            false,
            special_instructions:  '',
            payment_method:        'paystack',
        },

        coords: {
            pickup:  { lat: null, lng: null },
            dropoff: { lat: null, lng: null },
        },

        icons: {
            bike: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></svg>',
            car:  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
            truck: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
            'credit-card': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
            wallet: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>',
            banknote: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>',
        },

        init() {
            // Hydrate from old() if redirected back with validation errors
            if (config.old && Object.keys(config.old).length > 0) {
                const old = config.old;
                Object.keys(this.form).forEach((k) => {
                    if (old[k] !== undefined) {
                        this.form[k] = ['is_express', 'is_fragile'].includes(k)
                            ? (old[k] === '1' || old[k] === 1 || old[k] === true)
                            : old[k];
                    }
                });
                // Jump to review step so user can fix
                this.step = 4;
                this.maxReachedStep = 5;
                this.estimateFee();
            }
        },

        get vehicleLabel() {
            return this.vehicleOptions.find((v) => v.value === this.form.vehicle_type)?.label ?? '—';
        },

        iconSvg(name) {
            return this.icons[name] ?? '';
        },

        goTo(idx) {
            if (idx <= this.maxReachedStep) this.step = idx;
        },

        next() {
            if (! this.validateStep(this.step)) return;
            this.step = Math.min(this.step + 1, this.steps.length - 1);
            this.maxReachedStep = Math.max(this.maxReachedStep, this.step);

            if (this.step === 4) this.estimateFee();
        },

        prev() {
            this.step = Math.max(this.step - 1, 0);
        },

        validateStep(step) {
            const rules = [
                // 0: type
                () => !!this.form.vehicle_type,
                // 1: pickup
                () => this.form.pickup_contact_name
                    && this.form.pickup_contact_phone
                    && this.form.pickup_address
                    && this.form.pickup_city
                    && this.form.pickup_state,
                // 2: dropoff
                () => this.form.dropoff_contact_name
                    && this.form.dropoff_contact_phone
                    && this.form.dropoff_address
                    && this.form.dropoff_city
                    && this.form.dropoff_state,
                // 3: package
                () => this.form.package_weight_kg > 0,
                // 4: review — always valid
                () => true,
                // 5: payment
                () => !!this.form.payment_method,
            ];

            const ok = rules[step]?.() ?? true;
            if (! ok) {
                alert('Please complete the required fields before continuing.');
            }
            return ok;
        },

        async estimateFee() {
            if (! this.form.vehicle_type || ! (this.form.package_weight_kg > 0)) return;

            this.estimating = true;
            this.fee = null;
            this.feeError = null;

            try {
                const res = await fetch(config.estimateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        vehicle_type:      this.form.vehicle_type,
                        package_weight_kg: this.form.package_weight_kg,
                        pickup_lat:        this.coords.pickup.lat,
                        pickup_lng:        this.coords.pickup.lng,
                        dropoff_lat:       this.coords.dropoff.lat,
                        dropoff_lng:       this.coords.dropoff.lng,
                        is_express:        this.form.is_express,
                        is_fragile:        this.form.is_fragile,
                    }),
                });

                if (! res.ok) throw new Error(`Fee estimation failed with status ${res.status}`);
                this.fee = await res.json();
            } catch (error) {
                console.error('Fee estimation failed:', error);
                this.fee = null;
                this.feeError = 'Could not calculate the delivery fee. Please try again.';
            } finally {
                this.estimating = false;
            }
        },

        useMyLocation(which) {
            if (! navigator.geolocation) {
                alert('Geolocation is not supported in this browser.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.coords[which].lat = pos.coords.latitude.toFixed(7);
                    this.coords[which].lng = pos.coords.longitude.toFixed(7);
                },
                (err) => {
                    alert('Could not get your location: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        formatNaira(v) {
            return '₦' + Number(v).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        submit($form) {
            if (! this.validateStep(this.step)) return;
            this.submitting = true;
            $form.submit();
        },
    };
}
</script>
@endpush