<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Feedback</title>
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
        .wrap {
            max-width: 420px;
            margin: 0 auto;
            padding: 1rem;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        }
        h1 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 0.25rem;
        }
        .sub {
            font-size: 0.8125rem;
            color: var(--muted);
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--muted);
            margin-bottom: 0.35rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 0.5rem 0.65rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font: inherit;
            background: #fff;
        }
        textarea { min-height: 100px; resize: vertical; }
        .field { margin-bottom: 0.85rem; }
        .hint { font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem; }
        button[type="submit"] {
            width: 100%;
            padding: 0.65rem 1rem;
            border: none;
            border-radius: 6px;
            font: inherit;
            font-weight: 600;
            color: #fff;
            background: var(--primary);
            cursor: pointer;
        }
        button[type="submit"]:hover { background: var(--primary-hover); }
        button[type="submit"]:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .alert {
            display: none;
            padding: 0.65rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .alert.show { display: block; }
        .alert--error {
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }
        .alert--success {
            background: #f0fdf4;
            color: var(--success);
            border: 1px solid #bbf7d0;
        }
        .errors {
            font-size: 0.8125rem;
            color: var(--danger);
            margin-top: 0.25rem;
        }
        .field-error input,
        .field-error textarea {
            border-color: #f87171;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Contact us</h1>
        <p class="sub">We will get back to you shortly.</p>

        <div id="alert-error" class="alert alert--error" role="alert"></div>
        <div id="alert-success" class="alert alert--success" role="status"></div>

        <form id="ticket-form" novalidate>
            <div class="field" data-field="name">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" required maxlength="255" autocomplete="name">
                <div class="errors" data-errors-for="name"></div>
            </div>
            <div class="field" data-field="phone_e164">
                <label for="phone_e164">Phone (E.164)</label>
                <input id="phone_e164" name="phone_e164" type="text" required placeholder="+15551234567" autocomplete="tel">
                <p class="hint">Include country code, e.g. +15551234567.</p>
                <div class="errors" data-errors-for="phone_e164"></div>
            </div>
            <div class="field" data-field="email">
                <label for="email">Email <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
                <input id="email" name="email" type="email" maxlength="255" autocomplete="email">
                <div class="errors" data-errors-for="email"></div>
            </div>
            <div class="field" data-field="subject">
                <label for="subject">Subject</label>
                <input id="subject" name="subject" type="text" required maxlength="255">
                <div class="errors" data-errors-for="subject"></div>
            </div>
            <div class="field" data-field="message">
                <label for="message">Message</label>
                <textarea id="message" name="message" required maxlength="10000"></textarea>
                <div class="errors" data-errors-for="message"></div>
            </div>
            <div class="field" data-field="attachments">
                <label for="attachments">Attachments <span style="font-weight:400;color:var(--muted)">(optional, max 5)</span></label>
                <input id="attachments" name="attachments[]" type="file" multiple>
                <div class="errors" data-errors-for="attachments"></div>
            </div>
            <button type="submit" id="submit-btn">Send</button>
        </form>
    </div>
</div>
<script>
(function () {
    const form = document.getElementById('ticket-form');
    const btn = document.getElementById('submit-btn');
    const alertError = document.getElementById('alert-error');
    const alertSuccess = document.getElementById('alert-success');
    const ticketsUrl = '{{ $ticketsApiUrl }}';

    function clearFieldErrors() {
        document.querySelectorAll('.field-error').forEach(function (el) { el.classList.remove('field-error'); });
        document.querySelectorAll('[data-errors-for]').forEach(function (el) { el.textContent = ''; });
    }

    function showFieldErrors(errors) {
        clearFieldErrors();
        if (!errors || typeof errors !== 'object') return;

        const attachmentMessages = [];

        Object.keys(errors).forEach(function (key) {
            const messages = errors[key];
            if (!Array.isArray(messages)) return;

            if (key === 'attachments' || key.indexOf('attachments.') === 0) {
                messages.forEach(function (m) { attachmentMessages.push(m); });
                return;
            }

            const wrap = document.querySelector('[data-field="' + key + '"]');
            const errEl = document.querySelector('[data-errors-for="' + key + '"]');
            if (wrap) wrap.classList.add('field-error');
            if (errEl) errEl.textContent = messages.join(' ');
        });

        if (attachmentMessages.length) {
            const wrap = document.querySelector('[data-field="attachments"]');
            const errEl = document.querySelector('[data-errors-for="attachments"]');
            if (wrap) wrap.classList.add('field-error');
            if (errEl) errEl.textContent = attachmentMessages.join(' ');
        }
    }

    function hideAlerts() {
        alertError.classList.remove('show');
        alertSuccess.classList.remove('show');
        alertError.textContent = '';
        alertSuccess.textContent = '';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        hideAlerts();
        clearFieldErrors();

        const fd = new FormData(form);
        const hasFiles = document.getElementById('attachments').files.length;
        if (!hasFiles) {
            fd.delete('attachments[]');
        }

        btn.disabled = true;

        fetch(ticketsUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.status === 201) {
                    alertSuccess.textContent = 'Thank you! Your request has been submitted.';
                    alertSuccess.classList.add('show');
                    form.reset();
                    return;
                }
                if (result.status === 422 && result.data && result.data.errors) {
                    showFieldErrors(result.data.errors);
                    alertError.textContent = 'Please fix the errors below.';
                    alertError.classList.add('show');
                    return;
                }
                alertError.textContent = (result.data && result.data.message) ? result.data.message : 'Something went wrong. Please try again.';
                alertError.classList.add('show');
            })
            .catch(function () {
                alertError.textContent = 'Network error. Please try again.';
                alertError.classList.add('show');
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
</script>
</body>
</html>
