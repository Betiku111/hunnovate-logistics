<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('city')->index();
            $table->string('state')->index();
            $table->string('country', 2)->default('NG');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
