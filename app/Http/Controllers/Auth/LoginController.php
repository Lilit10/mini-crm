<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, LoginAction $action): RedirectResponse
    {
        $data = $request->validated();

        return $action->execute(
            Arr::only($data, ['email', 'password']),
            (bool) ($data['remember'] ?? false),
        );
    }

    public function destroy(Request $request, LogoutAction $action): RedirectResponse
    {
        return $action->execute($request);
    }
}
