<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\JwtService;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * User registration (Default status: pending)
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|unique:users,phone|max:20',
            'email' => 'nullable|email|max:100',
            'password' => 'required|string|min:6',
        ], [
            'full_name.required' => 'الاسم الكامل مطلوب.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'full_name' => $request->input('full_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password_hash' => Hash::make($request->input('password')),
            'balance' => 0.00,
            'status' => 'pending', // Pending admin approval
            'push_token' => $request->input('push_token'),
        ]);

        // Send a welcome notification
        PushNotificationService::sendToUser(
            user: $user,
            title: '👋 مرحباً بك في المحفظة الإلكترونية',
            message: 'تم استلام طلب تسجيلك بنجاح، وهو قيد المراجعة من قبل الإدارة.',
            data: ['type' => 'welcome', 'status' => 'pending'],
            type: 'alert'
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح، حسابك قيد المراجعة بانتظار موافقة الإدارة.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => $user->status,
                    'balance' => $user->balance,
                    'created_at' => $user->created_at,
                ],
            ],
        ], 201);
    }

    /**
     * User Login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة، يرجى التأكد من رقم الهاتف وكلمة المرور.',
                'data' => null,
            ], 401);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'حسابك قيد المراجعة، ينتظر موافقة الإدارة.',
                'data' => [
                    'status' => 'pending',
                ],
            ], 403);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'تم رفض حسابك، يرجى التواصل مع الإدارة.',
                'data' => [
                    'status' => 'rejected',
                ],
            ], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'حسابك موقف ومُعلّق، يرجى التواصل مع الإدارة.',
                'data' => [
                    'status' => 'suspended',
                ],
            ], 403);
        }

        if ($request->filled('push_token')) {
            $user->update(['push_token' => trim($request->input('push_token'))]);
        }

        $token = $this->jwtService->generateToken($user, 'user');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => $user->status,
                    'balance' => (float) $user->balance,
                    'push_token' => $user->push_token,
                ],
            ],
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب بيانات الحساب بنجاح.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => $user->status,
                    'balance' => (float) $user->balance,
                    'created_at' => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * User logout
     */
    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح.',
            'data' => null,
        ]);
    }
}
