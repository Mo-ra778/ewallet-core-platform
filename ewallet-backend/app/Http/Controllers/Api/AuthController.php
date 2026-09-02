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
use Illuminate\Support\Facades\DB;
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
            title: '👋 مرحباً بك في محفظة وافي باي  الإلكترونية',
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

        $message = $user->status === 'pending'
            ? 'تم تسجيل الدخول بنجاح. حسابك قيد المراجعة والتحقق من قبل الإدارة.'
            : 'تم تسجيل الدخول بنجاح.';

        return response()->json([
            'success' => true,
            'message' => $message,
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
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
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

        $token = $this->jwtService->generateToken($user, 'user');

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل وتأكيد بريدك الإلكتروني بنجاح! تم تسجيل دخولك لمحفظتك بنجاح.',
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
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                ],
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

    /**
     * Request Password Reset OTP (Forgot Password)
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ], [
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $input = trim((string) ($request->input('email') ?? $request->input('phone') ?? $request->input('identifier') ?? ''));

        if (empty($input)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال البريد الإلكتروني أو رقم الهاتف المسجل لاستعادة كلمة المرور.',
                'data' => null,
            ], 422);
        }

        $user = User::where('email', $input)
            ->orWhere('phone', $input)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على أي حساب مسجل بهذه البيانات، يرجى التأكد وإعادة المحاولة.',
                'data' => null,
            ], 404);
        }

        if (empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذا الحساب غير مربوط ببريد إلكتروني مفعل لاستلام رمز الأمان.',
                'data' => null,
            ], 422);
        }

        // Generate 6-digit OTP and store in both Cache and DB for 10 minutes (600 seconds)
        $resetOtp = (string) random_int(100000, 999999);
        Cache::put("password_reset_otp_{$user->id}", $resetOtp, 600);
        Cache::put("password_reset_by_email_" . md5(strtolower($user->email)), [
            'user_id' => $user->id,
            'otp' => $resetOtp,
        ], 600);

        try {
            DB::table('password_reset_tokens')->updateOrInsert(
                ['phone' => $user->phone],
                [
                    'token' => $resetOtp,
                    'created_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            // DB fallback handled
        }

        // Send professional email
        $sent = EmailNotificationService::sendPasswordResetOtp($user, $resetOtp);

        // Also send Push Notification if user has token
        PushNotificationService::sendToUser(
            user: $user,
            title: '🔐 طلب استعادة كلمة المرور',
            message: "تم طلب رمز استعادة كلمة المرور لحسابك. تفقد بريدك الإلكتروني ({$user->email}).",
            data: ['type' => 'password_reset'],
            type: 'alert'
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز استعادة كلمة المرور (OTP) إلى بريدك الإلكتروني بنجاح.',
            'data' => [
                'email' => $user->email,
                'email_sent' => $sent,
                'expires_in_seconds' => 600,
            ],
        ]);
    }

    /**
     * Verify Password Reset OTP (Separated Validation Step)
     */
    public function verifyResetOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'identifier' => 'nullable|string',
        ], [
            'otp.required' => 'رمز التحقق (OTP) مطلوب.',
            'otp.size' => 'يجب أن يتكون رمز التحقق من 6 أرقام.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $otp = trim((string) $request->input('otp'));
        $input = trim((string) ($request->input('email') ?? $request->input('phone') ?? $request->input('identifier') ?? ''));

        $user = null;
        if (!empty($input)) {
            $user = User::where('email', $input)
                ->orWhere('phone', $input)
                ->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الحساب المطلوب للتحقق من الرمز.',
                'data' => null,
            ], 404);
        }

        // Validate OTP from cache or DB
        $cachedOtp = Cache::get("password_reset_otp_{$user->id}");
        if (!$cachedOtp && !empty($user->email)) {
            $lookup = Cache::get("password_reset_by_email_" . md5(strtolower($user->email)));
            if (is_array($lookup) && isset($lookup['otp'])) {
                $cachedOtp = $lookup['otp'];
            }
        }

        // Fallback to database password_reset_tokens table (Valid within 10 minutes)
        if (!$cachedOtp) {
            try {
                $dbToken = DB::table('password_reset_tokens')
                    ->where('phone', $user->phone)
                    ->first();

                if ($dbToken && !empty($dbToken->token)) {
                    $createdAt = \Carbon\Carbon::parse($dbToken->created_at);
                    if ($createdAt->diffInMinutes(now()) <= 10) {
                        $cachedOtp = (string) $dbToken->token;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore DB read failure
            }
        }

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية رمز التحقق أو لم يتم طلبه، يرجى طلب رمز جديد.',
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

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق من الرمز بنجاح! يمكنك الآن تعيين كلمة المرور الجديدة.',
            'data' => [
                'verified' => true,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * Confirm OTP and Reset Password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'identifier' => 'nullable|string',
        ], [
            'otp.required' => 'رمز التحقق (OTP) مطلوب.',
            'otp.size' => 'يجب أن يتكون رمز التحقق من 6 أرقام.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $otp = trim((string) $request->input('otp'));
        $newPassword = (string) $request->input('password');
        $input = trim((string) ($request->input('email') ?? $request->input('phone') ?? $request->input('identifier') ?? ''));

        $user = null;
        if (!empty($input)) {
            $user = User::where('email', $input)
                ->orWhere('phone', $input)
                ->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الحساب المطلوب لتغيير كلمة المرور.',
                'data' => null,
            ], 404);
        }

        // Validate OTP from cache or DB table
        $cachedOtp = Cache::get("password_reset_otp_{$user->id}");
        if (!$cachedOtp && !empty($user->email)) {
            $lookup = Cache::get("password_reset_by_email_" . md5(strtolower($user->email)));
            if (is_array($lookup) && isset($lookup['otp'])) {
                $cachedOtp = $lookup['otp'];
            }
        }

        if (!$cachedOtp) {
            try {
                $dbToken = DB::table('password_reset_tokens')
                    ->where('phone', $user->phone)
                    ->first();

                if ($dbToken && !empty($dbToken->token)) {
                    $createdAt = \Carbon\Carbon::parse($dbToken->created_at);
                    if ($createdAt->diffInMinutes(now()) <= 10) {
                        $cachedOtp = (string) $dbToken->token;
                    }
                }
            } catch (\Throwable $e) {
                // DB fallback
            }
        }

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية رمز التحقق أو لم يتم طلبه، يرجى طلب رمز جديد.',
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

        // Update password hash and clear reset cache
        $user->update([
            'password_hash' => Hash::make($newPassword),
        ]);

        Cache::forget("password_reset_otp_{$user->id}");
        if (!empty($user->email)) {
            Cache::forget("password_reset_by_email_" . md5(strtolower($user->email)));
        }
        try {
            DB::table('password_reset_tokens')->where('phone', $user->phone)->delete();
        } catch (\Throwable $e) {
            // Ignore
        }

        // Notify user about successful password change
        PushNotificationService::sendToUser(
            user: $user,
            title: '✅ تم تغيير كلمة المرور بنجاح',
            message: 'تم تغيير كلمة المرور لحسابك في المحفظة بنجاح، يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.',
            data: ['type' => 'password_changed'],
            type: 'alert'
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
        ]);
    }
}
