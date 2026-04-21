<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param  array{status?: string, email?: string, phone_e164?: string, from?: CarbonInterface|null, to?: CarbonInterface|null}  $filters
     */
    public function paginateForManager(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery()->with('customer');

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $email = $filters['email'] ?? null;
        $phone = $filters['phone_e164'] ?? null;

        $query
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when(
                $email || $phone,
                function ($q) use ($email, $phone) {
                    $q->whereHas('customer', function ($q2) use ($email, $phone) {
                        $q2
                            ->when($email, fn ($q3) => $q3->where('email', 'like', "%{$email}%"))
                            ->when($phone, fn ($q3) => $q3->where('phone_e164', 'like', "%{$phone}%"));
                    });
                },
            )
            ->when(
                $from instanceof CarbonInterface,
                fn ($q) => $q->where('created_at', '>=', $from->copy()->startOfDay()),
            )
            ->when(
                $to instanceof CarbonInterface,
                fn ($q) => $q->where('created_at', '<=', $to->copy()->endOfDay()),
            );

        return $query
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getForManagerOrFail(int $ticketId): Ticket
    {
        return $this->baseQuery()
            ->with(['customer', 'media'])
            ->findOrFail($ticketId);
    }

    public function updateStatus(int $ticketId, TicketStatus $status, CarbonInterface $now): Ticket
    {
        $ticket = $this->baseQuery()->findOrFail($ticketId);

        $ticket->setStatus($status, $now);
        $ticket->save();

        return $ticket;
    }

    /**
     * @return Builder<Ticket>
     */
    private function baseQuery(): Builder
    {
        return Ticket::query();
    }
}
