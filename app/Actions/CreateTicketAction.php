<?php

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Repositories\CustomerRepository;
use App\Repositories\TicketRepository;

class CreateTicketAction
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly TicketRepository $tickets,
    ) {}

    /**
     * @param  array{name: string, phone_e164: string, email?: string|null, subject: string, message: string}  $data
     */
    public function execute(array $data): Ticket
    {
        $customer = $this->customers->findOrCreateByPhone(
            $data['phone_e164'],
            $data['name'],
            $data['email'] ?? null,
        );

        $ticket = $this->tickets->createForCustomer(
            $customer->id,
            $data['subject'],
            $data['message'],
            TicketStatus::New,
        );

        return $ticket->load('customer');
    }
}
