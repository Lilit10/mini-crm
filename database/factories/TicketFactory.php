<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement([
                TicketStatus::New,
                TicketStatus::InProgress,
                TicketStatus::Processed,
            ]),
            'manager_replied_at' => null,
        ];
    }
}

