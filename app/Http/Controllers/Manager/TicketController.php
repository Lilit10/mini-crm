<?php

namespace App\Http\Controllers\Manager;

use App\Actions\Manager\GetTicketAction;
use App\Actions\Manager\ListTicketsAction;
use App\Actions\Manager\UpdateTicketStatusAction;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\UpdateTicketStatusRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request, ListTicketsAction $action): View
    {
        $result = $action->execute($request->only(['status', 'email', 'phone_e164', 'from', 'to']));

        return view('manager.tickets.index', [
            'tickets' => $result['tickets'],
            'statuses' => TicketStatus::cases(),
            'filters' => $result['filters'],
        ]);
    }

    public function show(int $ticketId, GetTicketAction $action): View
    {
        $ticket = $action->execute($ticketId);

        return view('manager.tickets.show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::cases(),
        ]);
    }

    public function updateStatus(UpdateTicketStatusRequest $request, int $ticketId, UpdateTicketStatusAction $action): RedirectResponse
    {
        $status = TicketStatus::from($request->validated('status'));
        $ticket = $action->execute($ticketId, $status);

        return redirect()
            ->route('manager.tickets.show', ['ticketId' => $ticket->id])
            ->with('status', 'Ticket status updated.');
    }
}
