<?php

use App\Enums\UserRole;
use App\Models\User;

test('returns a fee breakdown for customer estimates', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)->postJson(route('customer.orders.estimate-fee'), [
        'vehicle_type' => 'motorcycle',
        'package_weight_kg' => 1,
        'is_express' => false,
        'is_fragile' => false,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('base_fee', 500)
        ->assertJsonPath('distance_km', 5)
        ->assertJsonPath('distance_fee', 500)
        ->assertJsonPath('total_fee', 1000)
        ->assertJsonPath('breakdown.base.amount', 500)
        ->assertJsonPath('breakdown.distance.amount', 500)
        ->assertJsonPath('breakdown.weight.amount', 0)
        ->assertJsonPath('breakdown.express.amount', 0)
        ->assertJsonPath('breakdown.fragile.amount', 0);
});

test('requires an authenticated customer for fee estimates', function () {
    $this->postJson(route('customer.orders.estimate-fee'))
        ->assertUnauthorized();
});

test('forbids non-customer roles from estimating fees', function (UserRole $role) {
    $user = User::factory()->create(['role' => $role->value]);

    $this->actingAs($user)
        ->postJson(route('customer.orders.estimate-fee'))
        ->assertForbidden();
})->with([
    'riders' => UserRole::Rider,
    'administrators' => UserRole::Admin,
]);
