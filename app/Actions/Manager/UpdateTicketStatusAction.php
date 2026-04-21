<?php

namespace App\Actions\Manager;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Repositories\TicketRepository;

class UpdateTicketStatusAction
{
    public function __construct(private readonly TicketRepository $tickets) {}

    public function execute(int $ticketId, TicketStatus $status): Ticket
    {
        return $this->tickets->updateStatus($ticketId, $status, now());
    }
}
