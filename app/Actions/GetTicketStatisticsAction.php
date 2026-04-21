<?php

namespace App\Actions;

use App\Repositories\TicketRepository;
use Carbon\CarbonImmutable;

class GetTicketStatisticsAction
{
    public function __construct(
        private readonly TicketRepository $tickets,
    ) {}

    /**
     * @return array{day: array{total: int, by_status: array<string, int>}, week: array{total: int, by_status: array<string, int>}, month: array{total: int, by_status: array<string, int>}}
     */
    public function execute(): array
    {
        $now = CarbonImmutable::now();

        return [
            'day' => $this->tickets->getPeriodStatistics($now->subDay(), $now),
            'week' => $this->tickets->getPeriodStatistics($now->subWeek(), $now),
            'month' => $this->tickets->getPeriodStatistics($now->subMonth(), $now),
        ];
    }
}
