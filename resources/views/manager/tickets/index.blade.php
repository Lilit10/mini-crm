@extends('layouts.app')

@section('title', 'Manager - Tickets')

@section('content')
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
        <div>
            <h1 style="margin:0;font-size:1.25rem;">Tickets</h1>
            <p class="muted" style="margin:0.15rem 0 0;">Filter by date, status, email or phone.</p>
        </div>
        <a href="{{ route('widget') }}" class="btn">Open widget</a>
    </div>

    <div class="card" style="margin-bottom:1rem;">
        <form method="GET" action="{{ route('manager.tickets.index') }}" class="grid" style="gap:0.85rem;">
            <div class="grid grid-2">
                <div class="field">
                    <label for="from">From</label>
                    <input id="from" type="date" name="from" value="{{ $filters['from'] }}">
                </div>
                <div class="field">
                    <label for="to">To</label>
                    <input id="to" type="date" name="to" value="{{ $filters['to'] }}">
                </div>
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">Any</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s->value }}" @selected($filters['status'] === $s->value)>{{ $s->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="email">Customer email</label>
                    <input id="email" type="text" name="email" value="{{ $filters['email'] }}" placeholder="e.g. john@example.com">
                </div>
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label for="phone_e164">Customer phone</label>
                    <input id="phone_e164" type="text" name="phone_e164" value="{{ $filters['phone_e164'] }}" placeholder="+15551234567">
                </div>
                <div style="display:flex;gap:0.5rem;align-items:flex-end;">
                    <button type="submit" class="btn btn--primary" style="width:auto;">Apply</button>
                    <a href="{{ route('manager.tickets.index') }}" class="btn" style="width:auto;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Created</th>
                <th>Status</th>
                <th>Customer</th>
                <th>Subject</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td>#{{ $ticket->id }}</td>
                    <td class="muted">{{ $ticket->created_at?->format('Y-m-d H:i') }}</td>
                    <td><span class="pill">{{ $ticket->status?->value ?? $ticket->status }}</span></td>
                    <td>
                        <div style="font-weight:600;">{{ $ticket->customer?->name }}</div>
                        <div class="muted" style="font-size:0.875rem;">
                            {{ $ticket->customer?->email ?? '—' }} - {{ $ticket->customer?->phone_e164 }}
                        </div>
                    </td>
                    <td>{{ $ticket->subject }}</td>
                    <td style="text-align:right;">
                        <a class="btn" style="width:auto;" href="{{ route('manager.tickets.show', ['ticketId' => $ticket->id]) }}">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">No tickets found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:1rem;">
            {{ $tickets->links() }}
        </div>
    </div>
@endsection

