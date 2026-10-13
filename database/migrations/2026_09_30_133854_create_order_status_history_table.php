<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('changed_by_role', 20)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index(['order_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};
