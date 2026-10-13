<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // credit|debit
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('reference')->nullable()->index();
            $table->string('description')->nullable();
            $table->nullableMorphs('source'); // link back to order/payment/etc.
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
