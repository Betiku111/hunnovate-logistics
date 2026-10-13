<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['order_id', 'is_active']);
            $table->index(['rider_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_assignments');
    }
};
