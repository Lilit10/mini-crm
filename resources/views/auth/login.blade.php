@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="card" style="max-width:420px;margin:0 auto;">
        <h1 style="margin:0 0 0.25rem;font-size:1.125rem;">Manager login</h1>
        <p class="muted" style="margin:0 0 1rem;">Use the seeded manager account to access the dashboard.</p>

        @if ($errors->any())
            <div class="alert alert--error" role="alert" style="margin-bottom:1rem;">
                <strong>Login failed.</strong>
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="grid" style="gap:0.85rem;">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <label style="display:flex;gap:0.5rem;align-items:center;text-transform:none;font-weight:600;letter-spacing:normal;">
                <input type="checkbox" name="remember" value="1" style="width:auto;">
                Remember me
            </label>

            <button type="submit" class="btn btn--primary">Login</button>

            <p class="muted" style="margin:0;font-size:0.875rem;">
                Seeded credentials: <code>manager@example.com</code> / <code>password</code>
            </p>
        </form>
    </div>
@endsection

