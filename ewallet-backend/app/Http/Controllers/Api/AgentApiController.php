<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\JwtService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgentApiController extends Controller
{
    protected JwtService $jwtService;
    protected OtpService $otpService;

    public function __construct(JwtService $jwtService, OtpService $otpService)
    {
        $this->jwtService = $jwtService;
        $this->otpService = $otpService;
    }

    /**
     * Agent API Login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $agent = Agent::where('phone', $request->input('phone'))->first();

        if (!$agent || !Hash::check($request->input('password'), $agent->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 401);
        }

        if ($agent->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'حساب الوكيل معلق حالياً.',
            ], 403);
        }

        $token = $this->jwtService->generateToken($agent, 'agent');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول',
            'data' => [
                'token' => $token,
                'agent' => [
                    'id' => $agent->id,
                    'full_name' => $agent->full_name,
                    'phone' => $agent->phone,
                    'balance' => (float) $agent->balance,
                ],
            ],
        ]);
    }

    /**
     * Agent Cash-In Deposit (REST API)
     */
    public function deposit(Request $request): JsonResponse
    {
        $agent = $request->user();
        if (!$agent instanceof Agent) {
            return response()->json(['success' => false, 'message' => 'غير مصرح للوكيل فقط.'], 403);
        }

        $userPhone = $request->input('user_phone') ?? $request->input('phone');
        $amount = (float) $request->input('amount');
        $currency = $request->input('currency', 'SAR');

        if (!$userPhone || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'بيانات غير صحيحة.'], 400);
        }

        if ((float) $agent->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'رصيد الوكيل غير كافٍ.'], 400);
        }

        $user = User::where('phone', $userPhone)->first();
        if (!$user || $user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود أو حسابه غير مفعّل.'], 404);
        }

        $tx = DB::transaction(function () use ($agent, $user, $amount, $currency) {
            $agent->decrement('balance', $amount);
            $user->increment('balance', $amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'deposit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "إيداع نقدي عبر الوكيل: {$agent->full_name}",
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'إيداع نقدي',
                'message' => "تم إيداع مبلغ {$amount} {$currency} في حسابك.",
                'type' => 'transaction',
            ]);

            return $transaction;
        });

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم الإيداع بنجاح',
            'data' => [
                'transaction_id' => $tx->id,
                'user_new_balance' => (float) $user->balance,
            ],
        ]);
    }

    /**
     * Agent Cash-Out Request Step 1 (REST API)
     */
    public function requestWithdraw(Request $request): JsonResponse
    {
        $agent = $request->user();
        if (!$agent instanceof Agent) {
            return response()->json(['success' => false, 'message' => 'غير مصرح للوكيل فقط.'], 403);
        }

        $userPhone = $request->input('user_phone') ?? $request->input('phone');
        $amount = (float) $request->input('amount');
        $currency = $request->input('currency', 'SAR');

        $user = User::where('phone', $userPhone)->first();
        if (!$user || $user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود أو حسابه غير نشط.'], 404);
        }

        if ((float) $user->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'رصيد المستخدم غير كافٍ'], 400);
        }

        $otp = $this->otpService->generateWithdrawalOtp($user, $agent->id, $amount, $currency);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال كود التحقق للمستخدم',
            'data' => [
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $currency,
            ],
        ]);
    }

    /**
     * Agent Cash-Out Verify Step 2 (REST API)
     */
    public function verifyWithdraw(Request $request): JsonResponse
    {
        $agent = $request->user();
        if (!$agent instanceof Agent) {
            return response()->json(['success' => false, 'message' => 'غير مصرح للوكيل فقط.'], 403);
        }

        $userPhone = $request->input('user_phone') ?? $request->input('phone');
        $otpCode = $request->input('otp_code') ?? $request->input('otp');

        $user = User::where('phone', $userPhone)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'العميل غير مسجل.'], 404);
        }

        $otpData = $this->otpService->verifyWithdrawalOtp($user->id, $agent->id, $otpCode);
        if (!$otpData) {
            return response()->json(['success' => false, 'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية'], 400);
        }

        $amount = (float) $otpData['amount'];
        $currency = $otpData['currency'] ?? 'SAR';

        if ((float) $user->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'رصيد المستخدم لم يعد كافياً.'], 400);
        }

        $tx = DB::transaction(function () use ($agent, $user, $amount, $currency) {
            $user->decrement('balance', $amount);
            $agent->increment('balance', $amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "سحب نقدي عبر الوكيل: {$agent->full_name}",
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'سحب نقدي مؤكد',
                'message' => "تم سحب مبلغ {$amount} {$currency} من حسابك عبر الوكيل.",
                'type' => 'transaction',
            ]);

            return $transaction;
        });

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم السحب بنجاح',
            'data' => [
                'transaction_id' => $tx->id,
                'user_new_balance' => (float) $user->balance,
            ],
        ]);
    }
}
