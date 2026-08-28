<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List user notifications
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = Notification::where('recipient_id', $user->id)
            ->where('recipient_type', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإشعارات بنجاح.',
            'data' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notification = Notification::where('id', $id)
            ->where('recipient_id', $user->id)
            ->where('recipient_type', 'user')
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'الإشعار غير موجود.',
                'data' => null,
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الإشعار كمقروء.',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        Notification::where('recipient_id', $user->id)
            ->where('recipient_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين جميع الإشعارات كمقروءة.',
            'data' => null,
        ]);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $count = Notification::where('recipient_id', $user->id)
            ->where('recipient_type', 'user')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب عدد الإشعارات غير المقروءة.',
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    /**
     * Register or update Expo/FCM Push Token for the authenticated user
     */
    public function registerPushToken(Request $request): JsonResponse
    {
        $pushToken = trim((string) ($request->input('push_token') ?? $request->input('token') ?? ''));

        if (empty($pushToken)) {
            return response()->json([
                'success' => false,
                'message' => 'رمز توكن الإشعارات (push_token أو token) مطلوب.',
                'data' => null,
            ], 422);
        }

        /** @var User $user */
        $user = $request->user();

        if (!$user instanceof User) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير صالح.',
                'data' => null,
            ], 403);
        }

        $user->update(['push_token' => $pushToken]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل وتحديث رمز الإشعارات السحابية بنجاح.',
            'data' => [
                'user_id' => $user->id,
                'push_token' => $pushToken,
                'updated_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
