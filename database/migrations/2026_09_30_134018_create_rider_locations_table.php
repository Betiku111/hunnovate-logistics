<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('accuracy_m', 8, 2)->nullable();
            $table->decimal('speed_kmh', 6, 2)->nullable();
            $table->decimal('heading_deg', 5, 2)->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['rider_id', 'recorded_at']);
            $table->index(['order_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_locations');
    }
};
