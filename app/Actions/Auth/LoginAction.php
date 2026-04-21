<?php

namespace App\Actions\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function execute(array $credentials, bool $remember): RedirectResponse
    {
        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => __('auth.failed')])
                ->withInput(['email' => $credentials['email']]);
        }

        session()->regenerate();

        return redirect()->intended(route('manager.tickets.index'));
    }
}
