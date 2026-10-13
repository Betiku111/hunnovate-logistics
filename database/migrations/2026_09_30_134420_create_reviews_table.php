<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('rider_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['rider_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
