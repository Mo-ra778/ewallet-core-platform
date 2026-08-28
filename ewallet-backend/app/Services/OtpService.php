<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    private int $ttlSeconds = 300; // 5 minutes validity

    /**
     * Generate an OTP for cash withdrawal by an agent
     */
    public function generateWithdrawalOtp(User $user, string $agentId, float $amount, string $currency = 'SAR'): string
    {
        $otp = (string) random_int(100000, 999999);
        $cacheKey = "otp_withdraw_{$user->id}";

        $payload = [
            'otp' => $otp,
            'user_id' => $user->id,
            'agent_id' => $agentId,
            'amount' => $amount,
            'currency' => $currency,
            'created_at' => now()->toIso8601String(),
        ];

        Cache::put($cacheKey, $payload, $this->ttlSeconds);

        // Create an in-app notification & push notification to user's device
        PushNotificationService::sendToUser(
            user: $user,
            title: '🔐 رمز التحقق للسحب النقدي (OTP)',
            message: "طلب سحب نقدي بمبلغ {$amount} {$currency} عبر الوكيل. رمز التأكيد الخاص بك هو: [ {$otp} ]. ينتهي خلال 5 دقائق.",
            data: [
                'type' => 'otp',
                'otp' => $otp,
                'amount' => $amount,
                'currency' => $currency,
            ],
            type: 'otp',
            sound: 'default',
            channelId: 'banking-alerts'
        );

        return $otp;
    }

    /**
     * Get active withdrawal request data for a user
     */
    public function getWithdrawalRequest(string $userId): ?array
    {
        return Cache::get("otp_withdraw_{$userId}");
    }

    /**
     * Verify and consume the OTP for withdrawal
     */
    public function verifyWithdrawalOtp(string $userId, string $agentId, string $otp): ?array
    {
        $cacheKey = "otp_withdraw_{$userId}";
        $data = Cache::get($cacheKey);

        if (!$data) {
            return null; // Expired or not found
        }

        if ($data['otp'] !== $otp || $data['agent_id'] !== $agentId) {
            return null; // Invalid OTP or mismatched agent
        }

        // OTP verified successfully -> invalidate from cache to prevent replay
        Cache::forget($cacheKey);

        return $data;
    }

    /**
     * Cancel an existing OTP withdrawal request
     */
    public function cancelWithdrawalOtp(string $userId): void
    {
        Cache::forget("otp_withdraw_{$userId}");
    }
}
