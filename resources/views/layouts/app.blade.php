<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #fff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #b91c1c;
            --success: #15803d;
            --radius: 10px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: var(--text);
            background: var(--bg);
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-hover); text-decoration: underline; }
        header {
            background: var(--card);
            border-bottom: 1px solid var(--border);
        }
        .container {
            max-width: 980px;
            margin: 0 auto;
            padding: 1rem;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .brand {
            font-weight: 700;
            color: var(--text);
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
        }
        .muted { color: var(--muted); }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font: inherit;
            cursor: pointer;
        }
        .btn--primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .btn--primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .btn--danger { background: var(--danger); color: #fff; border-color: var(--danger); }
        .btn--danger:hover { filter: brightness(0.95); }
        .btn[disabled] { opacity: 0.6; cursor: not-allowed; }
        .grid { display: grid; gap: 1rem; }
        .grid-2 { grid-template-columns: 1fr 1fr; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.6rem 0.5rem; border-bottom: 1px solid var(--border); text-align: left; vertical-align: top; }
        th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--muted); }
        input, select, textarea {
            width: 100%;
            padding: 0.5rem 0.65rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
            background: #fff;
        }
        label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; color: var(--muted); }
        .field { display: grid; gap: 0.35rem; }
        .alert {
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            border: 1px solid var(--border);
            background: #fff;
        }
        .alert--success { border-color: #bbf7d0; background: #f0fdf4; color: var(--success); }
        .alert--error { border-color: #fecaca; background: #fef2f2; color: var(--danger); }
        .errors { margin: 0.35rem 0 0; color: var(--danger); font-size: 0.875rem; }
        .errors ul { margin: 0; padding-left: 1.1rem; }
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            font-size: 0.8125rem;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="topbar">
            <a class="brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                @auth
                    <span class="muted">{{ auth()->user()->email }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn">Logout</button>
                    </form>
                @else
                    <a class="btn" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<main class="container">
    @if (session('status'))
        <div class="alert alert--success" role="status" style="margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>

