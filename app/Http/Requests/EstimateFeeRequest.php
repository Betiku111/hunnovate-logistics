<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'package_weight_kg' => ['required', 'numeric', 'min:0.1', 'max:5000'],
            'pickup_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'dropoff_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_express' => ['boolean'],
            'is_fragile' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_fragile' => $this->boolean('is_fragile'),
            'is_express' => $this->boolean('is_express'),
        ]);
    }
}
