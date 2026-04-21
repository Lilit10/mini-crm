<?php

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Repositories\CustomerRepository;
use App\Repositories\TicketRepository;
use Illuminate\Http\UploadedFile;

class CreateTicketAction
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly TicketRepository $tickets,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $attachments
     */
    public function handle(array $data, array $attachments = []): Ticket
    {
        return $this->createTicket($data, $attachments);
    }

    /**
     * @param  array{name: string, phone_e164: string, email?: string|null, subject: string, message: string}  $data
     * @param  array<int, UploadedFile>  $attachments
     */
    private function createTicket(array $data, array $attachments): Ticket
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

        foreach ($attachments as $file) {
            if ($file?->isValid()) {
                $ticket->addMedia($file)->toMediaCollection('attachments');
            }
        }

        return $ticket->load(['customer', 'media']);
    }
}
