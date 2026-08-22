<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminApiController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Admin Login (REST API)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $admin = Admin::where('username', $request->input('username'))->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 401);
        }

        $token = $this->jwtService->generateToken($admin, 'admin');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول',
            'data' => [
                'token' => $token,
                'admin' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role,
                ],
            ],
        ]);
    }

    /**
     * Get Pending Registration Users (REST API)
     */
    public function pendingUsers(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin instanceof Admin) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $users = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Approve Pending User (REST API)
     */
    public function approveUser(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin instanceof Admin) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        $user->update(['status' => 'active']);

        Notification::create([
            'recipient_id' => $user->id,
            'recipient_type' => 'user',
            'title' => 'تمت الموافقة على الحساب',
            'message' => 'تمت الموافقة على حسابك وتفعيله بنجاح، يمكنك الآن استخدام المحفظة.',
            'type' => 'alert',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت الموافقة على الحساب',
            'data' => [
                'user_id' => $user->id,
                'status' => 'active',
            ],
        ]);
    }

    /**
     * Reject User Registration (REST API)
     */
    public function rejectUser(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin instanceof Admin) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        $user->update(['status' => 'rejected']);

        Notification::create([
            'recipient_id' => $user->id,
            'recipient_type' => 'user',
            'title' => 'رفض طلب التسجيل',
            'message' => 'تم رفض طلب التسجيل، يرجى التواصل مع الإدارة.',
            'type' => 'alert',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الحساب',
            'data' => [
                'user_id' => $user->id,
                'status' => 'rejected',
            ],
        ]);
    }

    /**
     * Send Notification to User (REST API)
     */
    public function notifyUser(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin instanceof Admin) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $phone = $request->input('recipient_phone') ?? $request->input('phone');
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'المستخدم غير موجود.'], 404);
        }

        Notification::create([
            'recipient_id' => $user->id,
            'recipient_type' => 'user',
            'title' => $request->input('title', 'تنبيه من الإدارة'),
            'message' => $request->input('message'),
            'type' => 'message',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الإشعار',
        ]);
    }
}
