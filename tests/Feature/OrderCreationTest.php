<?php

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('submits a customer order to the admin queue', function () {
    $customer = User::factory()->customer()->create();

    Event::fake([OrderStatusChanged::class]);

    $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
        'pickup_contact_name' => 'Pickup Contact',
        'pickup_contact_phone' => '08012345678',
        'pickup_address' => '1 Pickup Road',
        'pickup_city' => 'Lagos',
        'pickup_state' => 'Lagos',
        'dropoff_contact_name' => 'Dropoff Contact',
        'dropoff_contact_phone' => '08087654321',
        'dropoff_address' => '2 Dropoff Road',
        'dropoff_city' => 'Lagos',
        'dropoff_state' => 'Lagos',
        'package_weight_kg' => 1,
        'vehicle_type' => 'motorcycle',
    ]);

    $order = Order::query()->sole();

    $response
        ->assertRedirect(route('customer.orders.confirmation', $order))
        ->assertSessionHas('success', 'Your order has been created successfully.');
    expect($order->status)->toBe(OrderStatus::WaitingAdmin);
    $this->assertModelExists($order);
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'from_status' => OrderStatus::Created->value,
        'to_status' => OrderStatus::WaitingAdmin->value,
        'changed_by' => $customer->id,
        'notes' => 'Order submitted for processing.',
    ]);
    Event::assertDispatched(
        OrderStatusChanged::class,
        fn (OrderStatusChanged $event) => $event->order->is($order)
            && $event->from === OrderStatus::Created
            && $event->to === OrderStatus::WaitingAdmin
            && $event->actor->is($customer),
    );
});
