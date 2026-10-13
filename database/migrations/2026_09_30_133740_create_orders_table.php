<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Public tracking number
            $table->string('tracking_number', 30)->unique();

            // Parties
            $table->foreignUuid('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('rider_id')->nullable()->constrained('riders')->nullOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();

            // Status
            $table->string('status', 30)->default(OrderStatus::Created->value)->index();
            $table->string('vehicle_type', 20)->default(VehicleType::Motorcycle->value);

            // Pickup
            $table->string('pickup_contact_name');
            $table->string('pickup_contact_phone', 20);
            $table->string('pickup_address');
            $table->string('pickup_city')->index();
            $table->string('pickup_state')->index();
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();

            // Destination
            $table->string('dropoff_contact_name');
            $table->string('dropoff_contact_phone', 20);
            $table->string('dropoff_address');
            $table->string('dropoff_city')->index();
            $table->string('dropoff_state')->index();
            $table->decimal('dropoff_lat', 10, 7)->nullable();
            $table->decimal('dropoff_lng', 10, 7)->nullable();

            // Package
            $table->string('package_description')->nullable();
            $table->decimal('package_weight_kg', 8, 2)->default(1);
            $table->decimal('package_length_cm', 8, 2)->nullable();
            $table->decimal('package_width_cm', 8, 2)->nullable();
            $table->decimal('package_height_cm', 8, 2)->nullable();
            $table->boolean('is_fragile')->default(false);
            $table->text('special_instructions')->nullable();

            // Delivery options
            $table->boolean('is_express')->default(false)->index();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Pricing (locked at order time)
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('base_fee', 12, 2)->default(0);
            $table->decimal('distance_fee', 12, 2)->default(0);
            $table->decimal('weight_fee', 12, 2)->default(0);
            $table->decimal('express_fee', 12, 2)->default(0);
            $table->decimal('fragile_fee', 12, 2)->default(0);
            $table->decimal('total_fee', 12, 2)->default(0);
            $table->string('currency', 3)->default('NGN');

            // Payment snapshot
            $table->string('payment_status', 20)->default(PaymentStatus::Pending->value)->index();
            $table->string('payment_method', 20)->nullable();

            // ETA
            $table->unsignedInteger('eta_minutes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for admin filters
            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'status']);
            $table->index(['rider_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
