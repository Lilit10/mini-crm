<?php

namespace Database\Seeders;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $manager = User::query()->firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
            ],
        );

        $manager->syncRoles([RolesAndPermissionsSeeder::ROLE_MANAGER]);

        $customers = Customer::factory()->count(3)->create();

        foreach ($customers as $customer) {
            Ticket::factory()
                ->count(2)
                ->for($customer, 'customer')
                ->state(['status' => TicketStatus::New])
                ->create();
        }
    }
}
