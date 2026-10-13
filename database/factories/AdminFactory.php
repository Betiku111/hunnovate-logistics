<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Admin->value]),
            'title' => fake()->jobTitle(),
            'permission_level' => 'standard',
            'last_login_at' => null,
        ];
    }
}
