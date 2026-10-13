<?php

use App\Enums\OrderStatus;
use App\Enums\RiderStatus;
use App\Events\RiderAssignmentAccepted;
use App\Models\Order;
use App\Models\Rider;
use App\Models\RiderAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Event;

it('records and dispatches rider acceptance when a rider accepts a job', function () {
    $riderUser = User::factory()->rider()->create();
    $rider = Rider::create([
        'user_id' => $riderUser->id,
        'status' => RiderStatus::Verified->value,
        'is_available' => true,
    ]);
    Vehicle::create([
        'rider_id' => $rider->id,
        'plate_number' => fake()->unique()->bothify('???-####'),
    ]);
    $customer = User::factory()->customer()->create();
    $order = Order::create([
        'tracking_number' => fake()->unique()->numerify('ORD########'),
        'customer_id' => $customer->id,
        'status' => OrderStatus::WaitingAdmin->value,
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
    ]);
    Event::fake([RiderAssignmentAccepted::class]);

    $response = $this->actingAs($riderUser)
        ->post(route('rider.jobs.accept', $order));

    $response->assertRedirect(route('rider.deliveries.show', $order))
        ->assertSessionHas('success', 'Job accepted. Head to the pickup location.');
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'rider_id' => $rider->id,
        'assigned_by' => null,
        'status' => OrderStatus::Accepted->value,
    ]);
    $this->assertDatabaseHas('rider_assignments', [
        'order_id' => $order->id,
        'rider_id' => $rider->id,
        'assigned_by' => null,
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'from_status' => OrderStatus::Assigned->value,
        'to_status' => OrderStatus::Accepted->value,
        'changed_by' => $riderUser->id,
    ]);
    expect(RiderAssignment::where('order_id', $order->id)->sole()->accepted_at)->not->toBeNull();
    expect($rider->fresh()->is_available)->toBeFalse();
    Event::assertDispatched(RiderAssignmentAccepted::class, fn (RiderAssignmentAccepted $event) => $event->order->id === $order->id
        && $event->order->status === OrderStatus::Accepted
        && $event->rider->is($rider));
});
