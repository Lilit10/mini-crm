<?php

namespace App\Actions\Manager;

use App\Models\Ticket;
use App\Repositories\TicketRepository;

class GetTicketAction
{
    public function __construct(private readonly TicketRepository $tickets) {}

    public function execute(int $ticketId): Ticket
    {
        return $this->tickets->getForManagerOrFail($ticketId);
    }
}
