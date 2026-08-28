<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send in-app notification & push notification to a user simultaneously
     */
    public static function sendToUser(
        User|string $user,
        string $title,
        string $message,
        array $data = [],
        string $type = 'transaction',
        string $sound = 'default',
        string $channelId = 'banking-alerts'
    ): Notification {
        if (is_string($user)) {
            $user = User::findOrFail($user);
        }

        // 1. Create In-App Notification record in Database
        $notification = Notification::create([
            'recipient_id' => $user->id,
            'recipient_type' => 'user',
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);

        // 2. Push to mobile device if user has a valid push token
        if (!empty($user->push_token)) {
            self::sendExpoPush(
                pushToken: $user->push_token,
                title: $title,
                body: $message,
                data: array_merge($data, [
                    'notification_id' => $notification->id,
                    'type' => $type,
                ]),
                sound: $sound,
                channelId: $channelId
            );
        }

        return $notification;
    }

    /**
     * Send Expo Push Notification payload via HTTP API
     */
    public static function sendExpoPush(
        string $pushToken,
        string $title,
        string $body,
        array $data = [],
        string $sound = 'default',
        string $channelId = 'banking-alerts',
        int $priority = 10
    ): bool {
        // Basic sanity check for valid Expo push token
        if (!str_starts_with($pushToken, 'ExponentPushToken') && !str_starts_with($pushToken, 'ExpoPushToken')) {
            // Also accept direct FCM tokens if configured
            Log::info("Push notification dispatching to token: " . substr($pushToken, 0, 15) . "...");
        }

        try {
            $payload = [
                'to' => $pushToken,
                'title' => $title,
                'body' => $body,
                'sound' => $sound,
                'priority' => 'high',
                'channelId' => $channelId,
                'data' => $data,
            ];

            $response = Http::timeout(4)->withHeaders([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Content-Type' => 'application/json',
            ])->post(self::EXPO_PUSH_URL, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Expo Push Notification response error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Expo Push Notification network exception: ' . $e->getMessage());
            return false;
        }
    }
}
