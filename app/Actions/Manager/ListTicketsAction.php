<?php

namespace App\Actions\Manager;

use App\Repositories\TicketRepository;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTicketsAction
{
    public function __construct(private readonly TicketRepository $tickets) {}

    /**
     * @param  array{status?: string, email?: string, phone_e164?: string, from?: string, to?: string}  $filters
     * @return array{tickets: LengthAwarePaginator, filters: array{status: string, email: string, phone_e164: string, from: string, to: string}}
     */
    public function execute(array $filters): array
    {
        $status = trim($filters['status'] ?? '');
        $email = trim($filters['email'] ?? '');
        $phone = trim($filters['phone_e164'] ?? '');
        $fromRaw = trim($filters['from'] ?? '');
        $toRaw = trim($filters['to'] ?? '');

        $from = $this->parseDate($fromRaw);
        $to = $this->parseDate($toRaw);

        $tickets = $this->tickets->paginateForManager([
            'status' => $status,
            'email' => $email,
            'phone_e164' => $phone,
            'from' => $from,
            'to' => $to,
        ]);

        return [
            'tickets' => $tickets,
            'filters' => [
                'status' => $status,
                'email' => $email,
                'phone_e164' => $phone,
                'from' => $fromRaw,
                'to' => $toRaw,
            ],
        ];
    }

    private function parseDate(string $value): ?CarbonImmutable
    {
        if ($value === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
