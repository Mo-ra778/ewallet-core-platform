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
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'token' => $token,
                'agent' => [
                    'id' => $agent->id,
                    'full_name' => $agent->full_name,
                    'phone' => $agent->phone,
                    'balances' => $agent->getAllBalances(),
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
        $currency = strtoupper($request->input('currency', 'YER'));

        if (!$userPhone || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'بيانات غير صحيحة.'], 400);
        }

        if (!$agent->hasSufficientBalance($amount, $currency)) {
            return response()->json([
                'success' => false,
                'message' => "رصيد العهدة المتاح بعملة {$currency} لا يكفي لإتمام هذه العملية.",
                'data' => ['balances' => $agent->getAllBalances()],
            ], 400);
        }

        $user = User::where('phone', $userPhone)->first();
        if (!$user || $user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود أو حسابه غير مفعّل.'], 404);
        }

        $tx = DB::transaction(function () use ($agent, $user, $amount, $currency) {
            $agent->decrementCurrency($currency, $amount);
            $user->incrementCurrency($currency, $amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'deposit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "إيداع نقدي ({$currency}) عبر الوكيل: {$agent->full_name}",
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'إيداع نقدي ناجح',
                'message' => "تم إيداع مبلغ " . number_format($amount, 2) . " {$currency} في محفظتك عبر الوكيل {$agent->full_name}.",
                'type' => 'transaction',
            ]);

            return $transaction;
        });

        $user->refresh();
        $agent->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم الإيداع بنجاح.',
            'data' => [
                'transaction_id' => $tx->id,
                'amount' => $amount,
                'currency' => $currency,
                'agent_balances' => $agent->getAllBalances(),
                'user_new_balance' => $user->getCurrencyBalance($currency),
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
        $currency = strtoupper($request->input('currency', 'YER'));

        if (!$userPhone || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'بيانات غير صحيحة.'], 400);
        }

        $user = User::where('phone', $userPhone)->first();
        if (!$user || $user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود أو حسابه غير مفعّل.'], 404);
        }

        if (!$user->hasSufficientBalance($amount, $currency)) {
            return response()->json([
                'success' => false,
                'message' => "رصيد العميل بعملة {$currency} غير كافٍ لإتمام عملية السحب.",
            ], 400);
        }

        // Generate OTP and notify user
        $otp = $this->otpService->generateWithdrawalOtp($user, $agent->id, $amount, $currency);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق OTP للعميل بنجاح.',
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'amount' => $amount,
                'currency' => $currency,
                'expires_in' => 300,
                'demo_otp' => $otp, // convenient for integration testing
            ],
        ]);
    }

    /**
     * Agent Cash-Out Verification Step 2 (REST API)
     */
    public function verifyWithdraw(Request $request): JsonResponse
    {
        $agent = $request->user();
        if (!$agent instanceof Agent) {
            return response()->json(['success' => false, 'message' => 'غير مصرح للوكيل فقط.'], 403);
        }

        $userId = $request->input('user_id');
        $otp = $request->input('otp');

        if (!$userId || !$otp) {
            return response()->json(['success' => false, 'message' => 'معرف العميل ورمز الـ OTP مطلوبان.'], 400);
        }

        $otpData = $this->otpService->verifyWithdrawalOtp($userId, $agent->id, $otp);
        if (!$otpData) {
            return response()->json(['success' => false, 'message' => 'رمز التحقق غير صحيح أو انتهت صلاحيته.'], 422);
        }

        $amount = (float) $otpData['amount'];
        $currency = strtoupper($otpData['currency'] ?? 'YER');

        $user = User::findOrFail($userId);
        if (!$user->hasSufficientBalance($amount, $currency)) {
            return response()->json(['success' => false, 'message' => 'رصيد العميل لم يعد كافياً.'], 400);
        }

        $tx = DB::transaction(function () use ($agent, $user, $amount, $currency) {
            $user->decrementCurrency($currency, $amount);
            $agent->incrementCurrency($currency, $amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "سحب نقدي ({$currency}) عبر الوكيل: {$agent->full_name}",
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'سحب نقدي مؤكد',
                'message' => "تم تسليم مبلغ " . number_format($amount, 2) . " {$currency} نقداً عبر الوكيل {$agent->full_name}.",
                'type' => 'transaction',
            ]);

            return $transaction;
        });

        $agent->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد السحب النقدي بنجاح.',
            'data' => [
                'transaction_id' => $tx->id,
                'amount' => $amount,
                'currency' => $currency,
                'agent_balances' => $agent->getAllBalances(),
            ],
        ]);
    }
}
