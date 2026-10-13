<?php

namespace Database\Seeders;

use App\Enums\RiderStatus;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $adminUser = User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@hunnovate.test',
        ]);
        Admin::create([
            'user_id' => $adminUser->id,
            'title' => 'Operations Director',
            'permission_level' => 'super',
        ]);

        // Customers
        User::factory()->customer()->count(5)->create()->each(function (User $user) {
            Customer::create([
                'user_id' => $user->id,
                'city' => fake()->city(),
                'state' => fake()->state(),
            ]);
        });

        // Riders
        User::factory()->rider()->count(3)->create()->each(function (User $user) {
            Rider::create([
                'user_id' => $user->id,
                'status' => RiderStatus::Verified->value,
                'city' => fake()->city(),
                'state' => fake()->state(),
                'verified_at' => now(),
                'rating' => 4.5,
                'is_online' => true,
                'is_available' => true,
            ]);
        });

        // Outlets
        Outlet::create([
            'name' => 'Hunnovate HQ',
            'code' => 'HQ-001',
            'address' => '12 Marina Road',
            'city' => 'Lagos',
            'state' => 'Lagos',
        ]);

        $this->command->info('✅ Seeded: 1 admin, 5 customers, 3 riders, 1 outlet');
        $this->command->info('   Admin login: admin@hunnovate.test / password');
    }
}
