<?php

use App\Enums\VehicleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default(VehicleType::Motorcycle->value)->index();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('plate_number')->unique();
            $table->string('registration_number')->nullable();
            $table->unsignedInteger('capacity_kg')->default(20);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
