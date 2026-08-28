<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\EmailNotificationService;
use App\Services\JwtService;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $hasEmail = !empty($user->email);
        $emailOtpSent = false;

        if ($hasEmail) {
            $emailOtp = (string) random_int(100000, 999999);
            // Cache OTP for 10 minutes (600 seconds)
            Cache::put("email_otp_{$user->id}", $emailOtp, 600);
            Cache::put("email_otp_by_email_" . md5(strtolower($user->email)), [
                'user_id' => $user->id,
                'otp' => $emailOtp,
            ], 600);

            // Send Verification Email
            $emailOtpSent = EmailNotificationService::sendEmailVerificationOtp($user, $emailOtp);
        }

        return response()->json([
            'success' => true,
            'message' => $hasEmail 
                ? 'تم إنشاء الحساب بنجاح، وتم إرسال رمز تفعيل البريد الإلكتروني (OTP) إلى بريدك.'
                : 'تم إنشاء الحساب بنجاح، حسابك قيد المراجعة بانتظار موافقة الإدارة.',
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
                'email_verification' => [
                    'required' => $hasEmail,
                    'email_sent' => $emailOtpSent,
                    'expires_in_seconds' => 600,
                ],
            ],
        ], 201);
    }

    /**
     * User Login
     */
    public function login(Request $request): JsonResponse
    {
        $identifier = trim((string) ($request->input('phone') ?? $request->input('email') ?? $request->input('login') ?? $request->input('identifier') ?? ''));
        $password = (string) $request->input('password');

        if (empty($identifier) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال رقم الهاتف أو البريد الإلكتروني وكلمة المرور.',
                'data' => null,
            ], 422);
        }

        $user = User::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة، يرجى التأكد من صحة رقم الهاتف أو البريد الإلكتروني وكلمة المرور.',
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

    /**
     * Verify email OTP after registration
     */
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'user_id' => 'nullable|string',
        ], [
            'otp.required' => 'رمز التحقق (OTP) المكون من 6 أرقام مطلوب.',
            'otp.size' => 'يجب أن يتكون رمز التحقق من 6 أرقام بالضبط.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $otp = trim($request->input('otp'));
        $email = trim((string) $request->input('email'));
        $phone = trim((string) $request->input('phone'));
        $userId = trim((string) $request->input('user_id'));

        // Find user by user_id, email, or phone
        $user = null;
        if (!empty($userId)) {
            $user = User::find($userId);
        } elseif (!empty($email)) {
            $user = User::where('email', $email)->first();
        } elseif (!empty($phone)) {
            $user = User::where('phone', $phone)->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على حساب مطابق لتأكيد الرمز.',
                'data' => null,
            ], 404);
        }

        // Check Cached OTP
        $cachedOtp = Cache::get("email_otp_{$user->id}");

        if (!$cachedOtp && !empty($user->email)) {
            $lookup = Cache::get("email_otp_by_email_" . md5(strtolower($user->email)));
            if (is_array($lookup) && isset($lookup['otp'])) {
                $cachedOtp = $lookup['otp'];
            }
        }

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية رمز التحقق أو لم يتم طلبه، يرجى طلب إعادة إرسال الرمز.',
                'data' => [
                    'expired' => true,
                ],
            ], 400);
        }

        if ($cachedOtp !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح، يرجى التأكد من الرمز وإعادة المحاولة.',
                'data' => [
                    'invalid' => true,
                ],
            ], 400);
        }

        // OTP is valid -> Update email_verified_at and clear Cache
        $user->update([
            'email_verified_at' => now(),
        ]);

        Cache::forget("email_otp_{$user->id}");
        if (!empty($user->email)) {
            Cache::forget("email_otp_by_email_" . md5(strtolower($user->email)));
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل وتأكيد بريدك الإلكتروني بنجاح! حسابك بانتظار اعتماد الإدارة النهائي.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at->toIso8601String(),
                'status' => $user->status,
            ],
        ]);
    }

    /**
     * Resend email verification OTP
     */
    public function resendEmailOtp(Request $request): JsonResponse
    {
        $identifier = trim((string) ($request->input('email') ?? $request->input('phone') ?? $request->input('user_id') ?? ''));

        if (empty($identifier)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إرسال البريد الإلكتروني أو رقم الهاتف لإعادة إرسال الرمز.',
                'data' => null,
            ], 422);
        }

        $user = User::where('id', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على مستخدم مسجل بهذه البيانات.',
                'data' => null,
            ], 404);
        }

        if (empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الحساب غير مربوط ببريد إلكتروني لإرسال الرمز إليه.',
                'data' => null,
            ], 422);
        }

        $emailOtp = (string) random_int(100000, 999999);
        Cache::put("email_otp_{$user->id}", $emailOtp, 600);
        Cache::put("email_otp_by_email_" . md5(strtolower($user->email)), [
            'user_id' => $user->id,
            'otp' => $emailOtp,
        ], 600);

        $sent = EmailNotificationService::sendEmailVerificationOtp($user, $emailOtp);

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة إرسال رمز التحقق الجديد إلى بريدك الإلكتروني بنجاح.',
            'data' => [
                'email' => $user->email,
                'email_sent' => $sent,
                'expires_in_seconds' => 600,
            ],
        ]);
    }
}
