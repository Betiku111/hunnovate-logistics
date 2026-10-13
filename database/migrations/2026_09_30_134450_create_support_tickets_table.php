<?php

use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number', 30)->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('status', 20)->default(SupportTicketStatus::Open->value)->index();
            $table->string('priority', 20)->default(SupportTicketPriority::Normal->value)->index();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
