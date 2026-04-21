@extends('layouts.app')

@section('title', 'Manager - Ticket #' . $ticket->id)

@section('content')
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
        <div>
            <div class="muted" style="font-size:0.875rem;">Ticket</div>
            <h1 style="margin:0;font-size:1.25rem;">#{{ $ticket->id }} - {{ $ticket->subject }}</h1>
            <p class="muted" style="margin:0.15rem 0 0;">
                Created {{ $ticket->created_at?->format('Y-m-d H:i') }}
                - Status: <span class="pill">{{ $ticket->status?->value ?? $ticket->status }}</span>
            </p>
        </div>
        <a class="btn" style="width:auto;" href="{{ route('manager.tickets.index') }}">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert--error" role="alert" style="margin-bottom:1rem;">
            <strong>Validation error.</strong>
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 2fr 1fr;">
        <div class="card">
            <h2 style="margin:0 0 0.5rem;font-size:1rem;">Message</h2>
            <div style="white-space:pre-wrap;">{{ $ticket->message }}</div>
        </div>

        <div class="grid">
            <div class="card">
                <h2 style="margin:0 0 0.75rem;font-size:1rem;">Customer</h2>
                <div style="font-weight:600;">{{ $ticket->customer?->name }}</div>
                <div class="muted">{{ $ticket->customer?->email ?? '—' }}</div>
                <div class="muted">{{ $ticket->customer?->phone_e164 }}</div>
            </div>

            <div class="card">
                <h2 style="margin:0 0 0.75rem;font-size:1rem;">Update status</h2>
                <form method="POST" action="{{ route('manager.tickets.status', ['ticketId' => $ticket->id]) }}" class="grid" style="gap:0.75rem;">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach ($statuses as $s)
                                <option value="{{ $s->value }}" @selected(($ticket->status?->value ?? $ticket->status) === $s->value)>{{ $s->value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn--primary">Save</button>
                </form>

                @if ($ticket->manager_replied_at)
                    <p class="muted" style="margin:0.75rem 0 0;font-size:0.875rem;">
                        Manager replied at: {{ $ticket->manager_replied_at->format('Y-m-d H:i') }}
                    </p>
                @endif
            </div>

            <div class="card">
                <h2 style="margin:0 0 0.75rem;font-size:1rem;">Attachments</h2>
                @php($media = $ticket->getMedia('attachments'))
                @if ($media->isEmpty())
                    <p class="muted" style="margin:0;">No attachments.</p>
                @else
                    <ul style="margin:0;padding-left:1.1rem;">
                        @foreach ($media as $m)
                            <li>
                                <a href="{{ $m->getUrl() }}" target="_blank" rel="noopener">
                                    {{ $m->file_name }}
                                </a>
                                <span class="muted" style="font-size:0.875rem;">({{ number_format($m->size / 1024, 1) }} KB)</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

