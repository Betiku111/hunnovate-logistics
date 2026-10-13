<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->restrictOnDelete();
            $table->string('method', 20);
            $table->string('status', 20)->default(PaymentStatus::Pending->value)->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('reference')->unique();
            $table->string('provider_reference')->nullable()->index();
            $table->json('provider_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
