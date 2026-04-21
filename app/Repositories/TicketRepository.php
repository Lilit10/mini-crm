<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class TicketRepository
{
    /**
     * @return array{total: int, by_status: array<string, int>}
     */
    public function getPeriodStatistics(CarbonInterface $from, CarbonInterface $to): array
    {
        $query = $this->baseQuery()->createdBetween($from, $to);

        $total = (clone $query)->count();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($v) => (int) $v)
            ->all();

        return [
            'total' => $total,
            'by_status' => $byStatus,
        ];
    }

    public function existsTodayForPhone(string $phoneE164): bool
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        return $this->baseQuery()
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('customer', fn ($q) => $q->where('phone_e164', $phoneE164))
            ->exists();
    }

    public function existsTodayForEmail(string $email): bool
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        return $this->baseQuery()
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('customer', fn ($q) => $q->where('email', $email))
            ->exists();
    }

    public function createForCustomer(int $customerId, string $subject, string $message, TicketStatus $status): Ticket
    {
        return $this->baseQuery()->create([
            'customer_id' => $customerId,
            'subject' => $subject,
            'message' => $message,
            'status' => $status,
        ]);
    }

    /**
     * @return Builder<Ticket>
     */
    private function baseQuery(): Builder
    {
        return Ticket::query();
    }
}
