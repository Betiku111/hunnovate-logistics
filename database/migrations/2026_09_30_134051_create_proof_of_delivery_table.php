<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proof_of_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUuid('rider_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('note')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamp('delivered_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proof_of_delivery');
    }
};
