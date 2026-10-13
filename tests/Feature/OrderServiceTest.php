<?php

use App\Enums\OrderStatus;
use App\Enums\RiderStatus;
use App\Events\RiderAssigned;
use App\Models\Order;
use App\Models\Rider;
use App\Models\RiderAssignment;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

function makeOrderForOrderServiceTest(
    User $customer,
    OrderStatus $status = OrderStatus::WaitingAdmin,
    ?Rider $rider = null,
): Order {
    return Order::create([
        'tracking_number' => fake()->unique()->numerify('ORD########'),
        'customer_id' => $customer->id,
        'rider_id' => $rider?->id,
        'status' => $status->value,
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
}

function makeRiderForOrderServiceTest(RiderStatus $status = RiderStatus::Verified): Rider
{
    $user = User::factory()->rider()->create();

    return Rider::create([
        'user_id' => $user->id,
        'status' => $status->value,
        'is_available' => true,
    ]);
}

it('assigns a verified rider and records the assignment and status transition', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $rider = makeRiderForOrderServiceTest();
    $order = makeOrderForOrderServiceTest($customer);
    Event::fake([RiderAssigned::class]);

    $assignedOrder = app(OrderService::class)->assignRider($order, $rider, $admin);

    expect($assignedOrder->status)->toBe(OrderStatus::Assigned)
        ->and($assignedOrder->rider_id)->toBe($rider->id)
        ->and($assignedOrder->assigned_by)->toBe($admin->id);
    $this->assertDatabaseHas('rider_assignments', [
        'order_id' => $order->id,
        'rider_id' => $rider->id,
        'assigned_by' => $admin->id,
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'from_status' => OrderStatus::WaitingAdmin->value,
        'to_status' => OrderStatus::Assigned->value,
        'changed_by' => $admin->id,
    ]);
    expect($rider->fresh()->is_available)->toBeFalse();
    Event::assertDispatched(RiderAssigned::class, fn (RiderAssigned $event) => $event->order->is($assignedOrder)
        && $event->rider->is($rider)
        && $event->assignedBy->is($admin)
        && ! $event->isReassignment);
});

it('rejects rider assignment when the order is not waiting for admin', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $rider = makeRiderForOrderServiceTest();
    $order = makeOrderForOrderServiceTest($customer, OrderStatus::Assigned);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->assignRider($order, $rider, $admin))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseMissing('rider_assignments', ['order_id' => $order->id]);
    Event::assertNotDispatched(RiderAssigned::class);
});

it('rejects rider assignment when the order already has a rider', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $currentRider = makeRiderForOrderServiceTest();
    $newRider = makeRiderForOrderServiceTest();
    $order = makeOrderForOrderServiceTest($customer, OrderStatus::WaitingAdmin, $currentRider);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->assignRider($order, $newRider, $admin))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseMissing('rider_assignments', ['order_id' => $order->id]);
    Event::assertNotDispatched(RiderAssigned::class);
});

it('rejects assignment to a rider who is not verified', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $rider = makeRiderForOrderServiceTest(RiderStatus::Pending);
    $order = makeOrderForOrderServiceTest($customer);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->assignRider($order, $rider, $admin))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseMissing('rider_assignments', ['order_id' => $order->id]);
    Event::assertNotDispatched(RiderAssigned::class);
});

it('reassigns an unaccepted order and releases the previous rider', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $oldRider = makeRiderForOrderServiceTest();
    $newRider = makeRiderForOrderServiceTest();
    $oldRider->update(['is_available' => false]);
    $order = makeOrderForOrderServiceTest($customer, OrderStatus::Assigned, $oldRider);
    $oldAssignment = RiderAssignment::create([
        'order_id' => $order->id,
        'rider_id' => $oldRider->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
        'is_active' => true,
    ]);
    Event::fake([RiderAssigned::class]);

    $reassignedOrder = app(OrderService::class)->reassignRider($order, $newRider, $admin);

    expect($reassignedOrder->status)->toBe(OrderStatus::Assigned)
        ->and($reassignedOrder->rider_id)->toBe($newRider->id)
        ->and($oldRider->fresh()->is_available)->toBeTrue()
        ->and($newRider->fresh()->is_available)->toBeFalse();
    $this->assertDatabaseHas('rider_assignments', [
        'id' => $oldAssignment->id,
        'is_active' => false,
    ]);
    $this->assertDatabaseHas('rider_assignments', [
        'order_id' => $order->id,
        'rider_id' => $newRider->id,
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'from_status' => OrderStatus::Assigned->value,
        'to_status' => OrderStatus::Assigned->value,
        'changed_by' => $admin->id,
        'notes' => "Reassigned to {$newRider->user->name} (previously {$oldRider->user->name}).",
    ]);
    Event::assertDispatched(RiderAssigned::class, fn (RiderAssigned $event) => $event->order->is($reassignedOrder)
        && $event->rider->is($newRider)
        && $event->assignedBy->is($admin)
        && $event->isReassignment);
});

it('rejects reassignment when the order is not in the assigned state', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $rider = makeRiderForOrderServiceTest();
    $order = makeOrderForOrderServiceTest($customer);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->reassignRider($order, $rider, $admin))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseMissing('rider_assignments', ['order_id' => $order->id]);
    Event::assertNotDispatched(RiderAssigned::class);
});

it('rejects reassigning an order to its current rider', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $rider = makeRiderForOrderServiceTest();
    $order = makeOrderForOrderServiceTest($customer, OrderStatus::Assigned, $rider);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->reassignRider($order, $rider, $admin))
        ->toThrow(ValidationException::class);

    Event::assertNotDispatched(RiderAssigned::class);
});

it('rejects reassignment to a rider who is not verified', function () {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();
    $currentRider = makeRiderForOrderServiceTest();
    $newRider = makeRiderForOrderServiceTest(RiderStatus::Pending);
    $order = makeOrderForOrderServiceTest($customer, OrderStatus::Assigned, $currentRider);
    Event::fake([RiderAssigned::class]);

    expect(fn () => app(OrderService::class)->reassignRider($order, $newRider, $admin))
        ->toThrow(ValidationException::class);

    Event::assertNotDispatched(RiderAssigned::class);
});
