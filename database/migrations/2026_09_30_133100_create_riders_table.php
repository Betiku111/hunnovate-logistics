<?php

use App\Enums\RiderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default(RiderStatus::Pending->value)->index();

            // Contact
            $table->string('home_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            // Documents
            $table->string('government_id_path')->nullable();
            $table->string('rider_license_path')->nullable();
            $table->string('profile_photo_path')->nullable();

            // Verification
            $table->timestamp('verified_at')->nullable();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();

            // Performance
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->unsignedInteger('cancelled_deliveries')->default(0);

            // Availability (live tracking, computed at runtime too)
            $table->boolean('is_online')->default(false)->index();
            $table->boolean('is_available')->default(false)->index();
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_online', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
