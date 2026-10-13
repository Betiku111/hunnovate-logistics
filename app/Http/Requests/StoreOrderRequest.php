<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    public function rules(): array
    {
        return [
            // Pickup
            'pickup_contact_name' => ['required', 'string', 'max:120'],
            'pickup_contact_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'pickup_address' => ['required', 'string', 'max:255'],
            'pickup_city' => ['required', 'string', 'max:80'],
            'pickup_state' => ['required', 'string', 'max:80'],
            'pickup_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['nullable', 'numeric', 'between:-180,180'],

            // Dropoff
            'dropoff_contact_name' => ['required', 'string', 'max:120'],
            'dropoff_contact_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'dropoff_address' => ['required', 'string', 'max:255'],
            'dropoff_city' => ['required', 'string', 'max:80'],
            'dropoff_state' => ['required', 'string', 'max:80'],
            'dropoff_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['nullable', 'numeric', 'between:-180,180'],

            // Package
            'package_description' => ['nullable', 'string', 'max:500'],
            'package_weight_kg' => ['required', 'numeric', 'min:0.1', 'max:5000'],
            'package_length_cm' => ['nullable', 'numeric', 'min:1', 'max:2000'],
            'package_width_cm' => ['nullable', 'numeric', 'min:1', 'max:2000'],
            'package_height_cm' => ['nullable', 'numeric', 'min:1', 'max:2000'],
            'is_fragile' => ['boolean'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],

            // Vehicle + options
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'is_express' => ['boolean'],
            'scheduled_at' => ['nullable', 'date', 'after_or_equal:now'],

            // Payment method (optional at this stage)
            'payment_method' => ['nullable', 'string', Rule::in(['paystack', 'flutterwave', 'wallet', 'cash'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_fragile' => $this->boolean('is_fragile'),
            'is_express' => $this->boolean('is_express'),
        ]);
    }

    public function messages(): array
    {
        return [
            'pickup_contact_phone.regex' => 'Please enter a valid pickup phone number.',
            'dropoff_contact_phone.regex' => 'Please enter a valid recipient phone number.',
        ];
    }
}
