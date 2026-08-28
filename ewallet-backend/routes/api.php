<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| E-Wallet Mobile Application REST APIs
*/

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Agent API Public & Protected Routes
Route::prefix('agent')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AgentApiController::class, 'login']);
    
    Route::middleware(['jwt.auth'])->group(function () {
        Route::post('/deposit', [\App\Http\Controllers\Api\AgentApiController::class, 'deposit']);
        Route::post('/withdraw/request', [\App\Http\Controllers\Api\AgentApiController::class, 'requestWithdraw']);
        Route::post('/withdraw/verify', [\App\Http\Controllers\Api\AgentApiController::class, 'verifyWithdraw']);
        
        // Agent Cash Remittance Payout Endpoints
        Route::post('/remittance/search', [\App\Http\Controllers\Api\AgentApiController::class, 'searchRemittance']);
        Route::post('/remittance/payout', [\App\Http\Controllers\Api\AgentApiController::class, 'payoutRemittance']);
    });
});

// Admin API Public & Protected Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AdminApiController::class, 'login']);
    
    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/pending-users', [\App\Http\Controllers\Api\AdminApiController::class, 'pendingUsers']);
        Route::post('/user/approve', [\App\Http\Controllers\Api\AdminApiController::class, 'approveUser']);
        Route::post('/user/reject', [\App\Http\Controllers\Api\AdminApiController::class, 'rejectUser']);
        Route::post('/notify-user', [\App\Http\Controllers\Api\AdminApiController::class, 'notifyUser']);
    });
});

// Authenticated User Routes (Require valid JWT)
Route::middleware(['jwt.auth'])->group(function () {
    
    // User Account & Profile
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // In-App & Push Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('/register-token', [NotificationController::class, 'registerPushToken']);
        Route::post('/push-token', [NotificationController::class, 'registerPushToken']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    // Financial Operations (Require Active Status)
    Route::middleware(['check.status'])->prefix('wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'getBalance']);
        Route::post('/transfer', [WalletController::class, 'transfer']);
        Route::get('/exchange-rates', [WalletController::class, 'getExchangeRates']);
        Route::post('/exchange/preview', [WalletController::class, 'previewExchange']);
        Route::post('/exchange', [WalletController::class, 'exchange']);
        Route::get('/transactions', [WalletController::class, 'transactions']);

        // Cash Remittance (Send to non-subscriber & management)
        Route::post('/remittance/preview', [WalletController::class, 'previewRemittance']);
        Route::post('/remittance/send', [WalletController::class, 'sendRemittance']);
        Route::get('/remittances', [WalletController::class, 'myRemittances']);
        Route::post('/remittance/{id}/cancel', [WalletController::class, 'cancelRemittance']);
    });
});

