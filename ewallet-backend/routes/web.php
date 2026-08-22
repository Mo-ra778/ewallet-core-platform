<?php

use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\AgentWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Redirection
Route::get('/', function () {
    return redirect()->route('admin.login.form');
});

// ==========================================
// Agent Portal Web Routes
// ==========================================
Route::prefix('agent')->group(function () {
    Route::get('/login', [AgentWebController::class, 'showLogin'])->name('agent.login.form');
    Route::post('/login', [AgentWebController::class, 'login'])->name('agent.login');
    Route::post('/logout', [AgentWebController::class, 'logout'])->name('agent.logout');

    Route::middleware(['role.check:agent'])->group(function () {
        Route::get('/dashboard', [AgentWebController::class, 'dashboard'])->name('agent.dashboard');
        Route::get('/deposit', [AgentWebController::class, 'depositForm'])->name('agent.deposit.form');
        Route::post('/deposit', [AgentWebController::class, 'deposit'])->name('agent.deposit');
        Route::get('/withdraw', [AgentWebController::class, 'withdrawForm'])->name('agent.withdraw.form');
        Route::post('/withdraw/otp', [AgentWebController::class, 'requestWithdrawalOtp'])->name('agent.withdraw.otp');
        Route::post('/withdraw/confirm', [AgentWebController::class, 'confirmWithdrawal'])->name('agent.withdraw.confirm');
        Route::get('/transactions', [AgentWebController::class, 'transactions'])->name('agent.transactions');
    });
});

// ==========================================
// Admin Dashboard Web Routes
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminWebController::class, 'showLogin'])->name('admin.login.form');
    Route::post('/login', [AdminWebController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminWebController::class, 'logout'])->name('admin.logout');

    Route::middleware(['role.check:admin'])->group(function () {
        Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminWebController::class, 'users'])->name('admin.users');
        Route::get('/users/{id}', [AdminWebController::class, 'userDetails'])->name('admin.users.show');
        Route::post('/users/{id}/status', [AdminWebController::class, 'updateUserStatus'])->name('admin.users.status');
        Route::get('/agents', [AdminWebController::class, 'agents'])->name('admin.agents');
        Route::post('/agents', [AdminWebController::class, 'createAgent'])->name('admin.agents.create');
        Route::post('/agents/{id}/status', [AdminWebController::class, 'updateAgentStatus'])->name('admin.agents.status');
        Route::get('/balance-adjustment', [AdminWebController::class, 'adjustBalanceForm'])->name('admin.balance.form');
        Route::post('/balance-adjustment', [AdminWebController::class, 'adjustBalance'])->name('admin.balance.adjust');
        Route::get('/transactions', [AdminWebController::class, 'transactions'])->name('admin.transactions');
        Route::get('/notifications', [AdminWebController::class, 'notifications'])->name('admin.notifications');
        Route::post('/notifications', [AdminWebController::class, 'notifications'])->name('admin.notifications.send');
    });
});
