<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('platform_commission', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->string('currency', 3)->default('NGN');
            $table->timestamp('earned_at')->useCurrent();
            $table->boolean('is_paid_out')->default(false)->index();
            $table->timestamp('paid_out_at')->nullable();
            $table->timestamps();

            $table->index(['rider_id', 'earned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
