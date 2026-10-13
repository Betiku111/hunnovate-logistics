<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->string('default_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->default('NG');
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
