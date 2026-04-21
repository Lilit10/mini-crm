<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Manager\TicketController as ManagerTicketController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/widget', WidgetController::class)->name('widget');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('manager')
    ->name('manager.')
    ->middleware(['auth', 'role:manager'])
    ->group(function () {
        Route::get('/tickets', [ManagerTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticketId}', [ManagerTicketController::class, 'show'])->name('tickets.show');
        Route::patch('/tickets/{ticketId}/status', [ManagerTicketController::class, 'updateStatus'])->name('tickets.status');
    });
