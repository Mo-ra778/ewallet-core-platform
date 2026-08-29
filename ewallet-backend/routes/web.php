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

// One-click Cloud Database Setup (Migrations & Seeds on Neon)
Route::get('/setup-cloud-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $seedEx) {
            $seedOutput = 'Seeder already ran or skipped: ' . $seedEx->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Cloud Database Migrated & Seeded Successfully on Neon PostgreSQL!',
            'migrate_output' => $migrateOutput,
            'seed_output' => $seedOutput,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

// ==========================================
// Agent Portal Web Routes
// ==========================================
Route::prefix('agent')->group(function () {
    Route::get('/login', [AgentWebController::class, 'showLogin'])->name('agent.login.form');
    Route::post('/login', [AgentWebController::class, 'login'])->name('agent.login');
    Route::post('/logout', [AgentWebController::class, 'logout'])->name('agent.logout');

    Route::middleware(['role.check:agent'])->group(function () {
        Route::get('/lookup-user', [AgentWebController::class, 'lookupUser'])->name('agent.lookup.user');
        Route::get('/dashboard', [AgentWebController::class, 'dashboard'])->name('agent.dashboard');
        Route::get('/deposit', [AgentWebController::class, 'depositForm'])->name('agent.deposit.form');
        Route::post('/deposit', [AgentWebController::class, 'deposit'])->name('agent.deposit');
        Route::get('/withdraw', [AgentWebController::class, 'withdrawForm'])->name('agent.withdraw.form');
        Route::post('/withdraw/otp', [AgentWebController::class, 'requestWithdrawalOtp'])->name('agent.withdraw.otp');
        Route::post('/withdraw/confirm', [AgentWebController::class, 'confirmWithdrawal'])->name('agent.withdraw.confirm');
        Route::get('/remittance-payout', [AgentWebController::class, 'showRemittancePayout'])->name('agent.remittance.form');
        Route::post('/remittance-payout', [AgentWebController::class, 'processRemittancePayout'])->name('agent.remittance.payout');
        Route::get('/transactions', [AgentWebController::class, 'transactions'])->name('agent.transactions');
        Route::get('/notifications', [AgentWebController::class, 'notifications'])->name('agent.notifications');
        Route::post('/notifications/{id}/read', [AgentWebController::class, 'markNotificationRead'])->name('agent.notifications.read');
        Route::post('/notifications/read-all', [AgentWebController::class, 'markAllNotificationsRead'])->name('agent.notifications.readAll');
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
        Route::get('/users/{id}', [AdminWebController::class, 'showUser'])->name('admin.users.show');
        Route::post('/users/{id}/status', [AdminWebController::class, 'updateUserStatus'])->name('admin.users.status');
        Route::get('/agents', [AdminWebController::class, 'agents'])->name('admin.agents');
        Route::get('/agents/{id}', [AdminWebController::class, 'showAgent'])->name('admin.agents.show');
        Route::post('/agents', [AdminWebController::class, 'createAgent'])->name('admin.agents.create');
        Route::post('/agents/{id}/status', [AdminWebController::class, 'updateAgentStatus'])->name('admin.agents.status');
        Route::get('/balance-adjustment', [AdminWebController::class, 'adjustBalanceForm'])->name('admin.balance.form');
        Route::post('/balance-adjustment', [AdminWebController::class, 'adjustBalance'])->name('admin.balance.adjust');
        Route::get('/transactions', [AdminWebController::class, 'transactions'])->name('admin.transactions');
        Route::get('/remittances', [AdminWebController::class, 'remittances'])->name('admin.remittances');
        Route::post('/remittances/{id}/cancel', [AdminWebController::class, 'cancelRemittance'])->name('admin.remittance.cancel');
        Route::get('/notifications', [AdminWebController::class, 'notifications'])->name('admin.notifications');
        Route::post('/notifications', [AdminWebController::class, 'sendNotification'])->name('admin.notifications.send');
        Route::get('/settings', [AdminWebController::class, 'settings'])->name('admin.settings');
        Route::post('/settings/exchange-rates', [AdminWebController::class, 'updateExchangeRates'])->name('admin.settings.rates');
        Route::post('/settings/exchange-rates/create', [AdminWebController::class, 'createExchangeRate'])->name('admin.settings.rates.create');
        Route::post('/settings/exchange-rates/{id}/delete', [AdminWebController::class, 'deleteExchangeRate'])->name('admin.settings.rates.delete');
        Route::post('/settings/system', [AdminWebController::class, 'updateSettings'])->name('admin.settings.system');
    });
});

