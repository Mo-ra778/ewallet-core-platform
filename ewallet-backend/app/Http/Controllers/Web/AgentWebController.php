<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\OtpService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AgentWebController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show Agent Login Page
     */
    public function showLogin()
    {
        if (session()->has('agent_id')) {
            return redirect()->route('agent.dashboard');
        }
        return view('agent.login');
    }

    /**
     * Handle Agent Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required' => 'يرجى إدخال رقم هاتف الوكيل.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        $agent = Agent::where('phone', $request->input('phone'))->first();

        if (!$agent || !Hash::check($request->input('password'), $agent->password_hash)) {
            return back()->withErrors(['phone' => 'بيانات الدخول غير صحيحة.'])->withInput();
        }

        if ($agent->status !== 'active') {
            return back()->withErrors(['phone' => 'حساب الوكيل معلق حالياً، يرجى مراجعة إدارة النظام.'])->withInput();
        }

        session(['agent_id' => $agent->id, 'agent_name' => $agent->full_name]);

        return redirect()->route('agent.dashboard')->with('success', 'مرحباً بك مجدداً في بوابة الوكلاء.');
    }

    /**
     * Handle Agent Logout
     */
    public function logout()
    {
        session()->forget(['agent_id', 'agent_name']);
        return redirect()->route('agent.login.form')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * AJAX Lookup User details by Phone for instant UI validation & confirmation card
     */
    public function lookupUser(Request $request)
    {
        $phone = trim((string) $request->input('phone', ''));
        if (empty($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال رقم الهاتف.',
            ], 422);
        }

        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'العميل غير مسجل في النظام.',
            ], 404);
        }

        $statusLabels = [
            'active' => 'حساب نشط',
            'pending' => 'بانتظار موافقة الإدارة',
            'suspended' => 'حساب معلق',
            'rejected' => 'حساب مرفوض',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'status' => $user->status,
                'status_label' => $statusLabels[$user->status] ?? $user->status,
                'is_active' => $user->status === 'active',
                'balances' => [
                    'YER' => number_format($user->getCurrencyBalance('YER'), 0),
                    'SAR' => number_format($user->getCurrencyBalance('SAR'), 2),
                    'USD' => number_format($user->getCurrencyBalance('USD'), 2),
                    'EUR' => number_format($user->getCurrencyBalance('EUR'), 2),
                ],
            ],
        ]);
    }

    /**
     * Agent Dashboard with Multi-Currency Balances & Turnover
     */
    public function dashboard()
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $totalTransactions = Transaction::where('agent_id', $agent->id)->count();

        // Calculate Multi-Currency Turnover
        $depositsByCurrency = Transaction::where('agent_id', $agent->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->select('currency', DB::raw('SUM(amount) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency')
            ->toArray();

        $withdrawalsByCurrency = Transaction::where('agent_id', $agent->id)
            ->where('type', 'withdraw')
            ->where('status', 'completed')
            ->select('currency', DB::raw('SUM(amount) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency')
            ->toArray();

        $recentTransactions = Transaction::where('agent_id', $agent->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.dashboard', compact(
            'agent',
            'totalTransactions',
            'depositsByCurrency',
            'withdrawalsByCurrency',
            'recentTransactions'
        ));
    }

    /**
     * Show Deposit Form
     */
    public function depositForm()
    {
        $agent = Agent::findOrFail(session('agent_id'));
        return view('agent.deposit', compact('agent'));
    }

    /**
     * Process Direct Cash-In Deposit to User in Selected Currency
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:SAR,YER,USD,EUR',
            'notes' => 'nullable|string|max:200',
        ], [
            'phone.required' => 'رقم هاتف العميل مطلوب.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر.',
            'currency.required' => 'يرجى تحديد العملة.',
        ]);

        $agent = Agent::findOrFail(session('agent_id'));
        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));

        // Check agent balance in this specific currency
        if (!$agent->hasSufficientBalance($amount, $currency)) {
            $currentBal = number_format($agent->getCurrencyBalance($currency), 2);
            return back()->withErrors(['amount' => "رصيد العهدة المتاح بعملة {$currency} ({$currentBal}) لا يكفي لإتمام هذه العملية."])->withInput();
        }

        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'العميل غير مسجل في النظام.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['phone' => 'حساب العميل غير نشط (' . $user->status . ').'])->withInput();
        }

        // Execute Deposit with DB::transaction
        try {
            $tx = null;
            DB::transaction(function () use ($agent, $user, $amount, $currency, $request, &$tx) {
                $agentModel = Agent::where('id', $agent->id)->lockForUpdate()->firstOrFail();
                $userModel = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

                $agentModel->decrementCurrency($currency, $amount);
                $userModel->incrementCurrency($currency, $amount);

                $tx = Transaction::create([
                    'user_id' => $userModel->id,
                    'agent_id' => $agentModel->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'fee' => 0.00,
                    'commission' => 0.00,
                    'currency' => $currency,
                    'status' => 'completed',
                    'description' => "إيداع نقدي ({$currency}) عبر الوكيل: {$agentModel->full_name}" . ($request->input('notes') ? ' - ' . $request->input('notes') : ''),
                ]);

                try {
                    PushNotificationService::sendToUser(
                        user: $userModel,
                        title: '💵 إيداع نقدي ناجح',
                        message: "تم إيداع مبلغ " . number_format($amount, 2) . " {$currency} في محفظتك بنجاح عبر الوكيل {$agentModel->full_name}.",
                        data: ['type' => 'deposit', 'amount' => $amount, 'currency' => $currency],
                        type: 'transaction'
                    );
                } catch (\Throwable $notifEx) {
                    Log::warning("Deposit push notification failed: " . $notifEx->getMessage());
                }
            });

            $receipt = [
                'type' => 'deposit',
                'title' => 'سند إيداع نقدي فوري',
                'amount' => $amount,
                'currency' => $currency,
                'user_name' => $user->full_name,
                'user_phone' => $user->phone,
                'agent_name' => $agent->full_name,
                'reference' => strtoupper(substr($tx->id ?? uniqid(), 0, 13)),
                'date' => now()->format('Y-m-d H:i:s'),
                'notes' => $request->input('notes'),
            ];

            return redirect()->route('agent.dashboard')
                ->with('success', "تم إيداع مبلغ " . number_format($amount, 2) . " {$currency} بنجاح في حساب العميل {$user->full_name}.")
                ->with('receipt', $receipt);
        } catch (\Throwable $e) {
            Log::error("Agent Deposit Failed: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'تعذر إتمام عملية الإيداع: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show Cash Withdrawal Form (Step 1)
     */
    public function withdrawForm()
    {
        $agent = Agent::findOrFail(session('agent_id'));
        return view('agent.withdraw', compact('agent'));
    }

    /**
     * Request Cash Withdrawal OTP (Step 1 Submission) in Selected Currency
     */
    public function requestWithdrawalOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:SAR,YER,USD,EUR',
        ], [
            'phone.required' => 'رقم هاتف العميل مطلوب.',
            'amount.required' => 'المبلغ مطلوب.',
            'currency.required' => 'يرجى تحديد العملة.',
        ]);

        $agent = Agent::findOrFail(session('agent_id'));
        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));
        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'العميل غير مسجل في النظام.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['phone' => 'حساب العميل غير نشط ولا يمكنه السحب حالياً.'])->withInput();
        }

        // Calculate withdrawal fee
        $feeInfo = \App\Services\FeeService::calculateWithdrawalFee($amount);
        $totalRequired = $amount + $feeInfo['fee'];

        // Check user balance in specified currency (amount + fee)
        if (!$user->hasSufficientBalance($totalRequired, $currency)) {
            $userBal = number_format($user->getCurrencyBalance($currency), 2);
            return back()->withErrors(['amount' => "رصيد العميل بعملة {$currency} ({$userBal}) غير كافٍ لتغطية مبلغ السحب مع الرسوم (" . number_format($totalRequired, 2) . " {$currency})."])->withInput();
        }

        // Generate OTP & Notify User
        $otp = $this->otpService->generateWithdrawalOtp($user, $agent->id, $amount, $currency);

        session([
            'pending_withdraw_' . $user->id => [
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'amount' => $amount,
                'currency' => $currency,
                'otp' => $otp,
                'fee' => $feeInfo['fee'],
                'agent_commission' => $feeInfo['agent_commission'],
                'total_debit' => $totalRequired,
                'expires_at' => now()->addMinutes(5)->timestamp,
            ]
        ]);

        return view('agent.withdraw_confirm', [
            'agent' => $agent,
            'user' => $user,
            'amount' => $amount,
            'fee' => $feeInfo['fee'],
            'agent_commission' => $feeInfo['agent_commission'],
            'total_debit' => $totalRequired,
            'currency' => $currency,
            'demo_otp' => $otp, // Shown for test convenience
        ]);
    }

    /**
     * Confirm Cash Withdrawal with OTP (Step 2 Submission)
     */
    public function confirmWithdrawal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'رمز التحقق (OTP) مطلوب.',
            'otp.size' => 'رمز التحقق يجب أن يتكون من 6 أرقام.',
        ]);

        $agent = Agent::findOrFail(session('agent_id'));
        $user = User::findOrFail($request->input('user_id'));
        $otp = trim($request->input('otp'));

        // Check active request from session or cache
        $sessionKey = 'pending_withdraw_' . $user->id;
        $activeRequest = session($sessionKey) ?? $this->otpService->getWithdrawalRequest($user->id);

        if (!$activeRequest || (isset($activeRequest['expires_at']) && now()->timestamp > $activeRequest['expires_at'])) {
            session()->forget($sessionKey);
            return redirect()->route('agent.withdraw.form')
                ->withErrors(['phone' => 'انتهت صلاحية رمز التحقق (5 دقائق)، يرجى إنشاء طلب سحب جديد.'])
                ->withInput();
        }

        // Verify OTP
        $expectedOtp = trim((string) ($activeRequest['otp'] ?? ''));
        if ($otp !== $expectedOtp) {
            $reqAmount = (float) $activeRequest['amount'];
            $reqCurrency = strtoupper($activeRequest['currency'] ?? 'YER');
            $fee = $activeRequest['fee'] ?? \App\Services\FeeService::calculateWithdrawalFee($reqAmount)['fee'];
            $comm = $activeRequest['agent_commission'] ?? \App\Services\FeeService::calculateWithdrawalFee($reqAmount)['agent_commission'];

            return view('agent.withdraw_confirm', [
                'agent' => $agent,
                'user' => $user,
                'amount' => $reqAmount,
                'fee' => $fee,
                'agent_commission' => $comm,
                'total_debit' => $activeRequest['total_debit'] ?? ($reqAmount + $fee),
                'currency' => $reqCurrency,
                'demo_otp' => $expectedOtp,
            ])->withErrors(['otp' => 'رمز التحقق (OTP) غير صحيح، يرجى التأكد وإعادة المحاولة.']);
        }

        // Consume OTP
        Cache::forget("otp_withdraw_{$user->id}");
        session()->forget($sessionKey);

        $amount = (float) $activeRequest['amount'];
        $currency = strtoupper($activeRequest['currency'] ?? 'YER');

        // Calculate fee and agent commission
        $feeInfo = \App\Services\FeeService::calculateWithdrawalFee($amount);
        $fee = $feeInfo['fee'];
        $agentCommission = $feeInfo['agent_commission'];
        $totalDebit = $amount + $fee;

        if (!$user->hasSufficientBalance($totalDebit, $currency)) {
            return redirect()->route('agent.withdraw.form')->withErrors(['amount' => "رصيد العميل بعملة {$currency} لم يعد كافياً لتغطية السحب والرسوم."]);
        }

        // Complete Withdrawal inside DB::transaction
        $tx = null;
        DB::transaction(function () use ($agent, $user, $amount, $fee, $agentCommission, $totalDebit, $currency, &$tx) {
            $agentModel = Agent::where('id', $agent->id)->lockForUpdate()->firstOrFail();
            $userModel = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            // Deduct total (amount + fee) from user
            $userModel->decrementCurrency($currency, $totalDebit);

            // Credit agent with cash replenishment + earned commission share!
            $totalAgentCredit = $amount + $agentCommission;
            $agentModel->incrementCurrency($currency, $totalAgentCredit);

            $tx = Transaction::create([
                'user_id' => $userModel->id,
                'agent_id' => $agentModel->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'fee' => $fee,
                'commission' => $agentCommission,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "سحب نقدي ({$currency}) عبر الوكيل: {$agentModel->full_name}" . ($fee > 0 ? " (رسوم: {$fee} {$currency} - عمولة الوكيل: {$agentCommission} {$currency})" : ''),
            ]);

            // 1. Notify User (Customer)
            PushNotificationService::sendToUser(
                user: $userModel,
                title: '🏧 سحب نقدي مؤكد',
                message: "تم تسليم مبلغ " . number_format($amount, 2) . " {$currency} نقداً من حسابك عبر الوكيل {$agentModel->full_name}" . ($fee > 0 ? " (رسوم الخدمة: " . number_format($fee, 2) . " {$currency})" : '') . ".",
                data: ['type' => 'withdraw', 'amount' => $amount, 'currency' => $currency],
                type: 'transaction'
            );

            // 2. Notify Agent with Full Breakdown: Cash Reimbursement + Commission
            $formattedAmount = number_format($amount, 2);
            $formattedCommission = number_format($agentCommission, 2);
            $formattedTotal = number_format($totalAgentCredit, 2);

            Notification::create([
                'recipient_id' => $agentModel->id,
                'recipient_type' => 'agent',
                'title' => '💵 إيداع تعويض نقدية وعمولة السحب',
                'message' => "تمت إضافة إجمالي {$formattedTotal} {$currency} إلى عهدتك الإلكترونية بنجاح (مبلغ السحب المسترد: {$formattedAmount} {$currency}" . ($agentCommission > 0 ? " + عمولة أرباحك: {$formattedCommission} {$currency}" : '') . ") مقابل صرف نقدية للعميل {$userModel->full_name}.",
                'type' => 'transaction',
                'is_read' => false,
            ]);
        });

        $receipt = [
            'type' => 'withdraw',
            'title' => 'سند سحب نقدي موثق (OTP)',
            'amount' => $amount,
            'currency' => $currency,
            'user_name' => $user->full_name,
            'user_phone' => $user->phone,
            'agent_name' => $agent->full_name,
            'fee' => $fee,
            'agent_commission' => $agentCommission,
            'reference' => strtoupper(substr($tx->id ?? uniqid(), 0, 13)),
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        return redirect()->route('agent.dashboard')
            ->with('success', "تم تأكيد سحب مبلغ " . number_format($amount, 2) . " {$currency} بنجاح وتسليمه للعميل {$user->full_name}، وتمت إضافة عمولة الربح (" . number_format($agentCommission, 2) . " {$currency}) إلى رصيدك.")
            ->with('receipt', $receipt);
    }

    /**
     * Agent Transactions History
     */
    public function transactions(Request $request)
    {
        $agent = Agent::findOrFail(session('agent_id'));
        $currency = $request->query('currency');

        $query = Transaction::where('agent_id', $agent->id)->with('user');

        if ($currency && in_array($currency, ['SAR', 'YER', 'USD', 'EUR'])) {
            $query->where('currency', $currency);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('agent.transactions', compact('agent', 'transactions', 'currency'));
    }

    /**
     * Agent Notifications Center
     */
    public function notifications()
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $notifications = Notification::where('recipient_id', $agent->id)
            ->where('recipient_type', 'agent')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = Notification::where('recipient_id', $agent->id)
            ->where('recipient_type', 'agent')
            ->where('is_read', false)
            ->count();

        return view('agent.notifications', compact('agent', 'notifications', 'unreadCount'));
    }

    /**
     * Mark Notification as Read
     */
    public function markNotificationRead(string $id)
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $notification = Notification::where('id', $id)
            ->where('recipient_id', $agent->id)
            ->where('recipient_type', 'agent')
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }

    /**
     * Mark All Agent Notifications as Read
     */
    public function markAllNotificationsRead()
    {
        $agent = Agent::findOrFail(session('agent_id'));

        Notification::where('recipient_id', $agent->id)
            ->where('recipient_type', 'agent')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    /**
     * Show Remittance Cash Payout Page & Handle Search
     */
    public function showRemittancePayout(Request $request)
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $remittance = null;

        if ($request->filled('remittance_code') && $request->filled('pin_code')) {
            $code = trim($request->input('remittance_code'));
            $pin = trim($request->input('pin_code'));

            $remittance = \App\Models\Remittance::where('remittance_code', $code)
                ->where('pin_code', $pin)
                ->first();

            if (!$remittance) {
                return back()->withErrors(['remittance_code' => 'بيانات الحوالة غير صحيحة أو الكود السري غير مطابق.'])->withInput();
            }

            if ($remittance->status !== 'pending') {
                $statusArabic = match($remittance->status) {
                    'paid' => 'مصروفة مسبقاً',
                    'cancelled' => 'ملغاة ومسترجعة للمرسل',
                    default => $remittance->status
                };
                return back()->withErrors(['remittance_code' => "هذه الحوالة غير قابلة للصرف لأنها ({$statusArabic})."])->withInput();
            }
        }

        return view('agent.remittance_payout', compact('agent', 'remittance'));
    }

    /**
     * Process Remittance Payout to Recipient
     */
    public function processRemittancePayout(Request $request)
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $request->validate([
            'remittance_id' => 'required|uuid',
            'recipient_id_type' => 'required|string|max:50',
            'recipient_id_number' => 'required|string|max:50',
        ], [
            'remittance_id.required' => 'الحوالة المحددة غير صحيحة.',
            'recipient_id_type.required' => 'يرجى تحديد نوع وثيقة إثبات هوية المستلم.',
            'recipient_id_number.required' => 'يرجى إدخال رقم وثيقة إثبات الهوية.',
        ]);

        $remittance = \App\Models\Remittance::where('id', $request->input('remittance_id'))->firstOrFail();

        if ($remittance->status !== 'pending') {
            return redirect()->route('agent.remittance.form')->withErrors(['remittance_code' => 'هذه الحوالة لم تعد في حالة معلقة ولا يمكن صرفها.']);
        }

        $idType = trim($request->input('recipient_id_type'));
        $idNumber = trim($request->input('recipient_id_number'));

        $tx = null;
        DB::transaction(function () use ($agent, $remittance, $idType, $idNumber, &$tx) {
            // Update Remittance to Paid
            $remittance->update([
                'status' => 'paid',
                'paid_by_agent_id' => $agent->id,
                'paid_at' => now(),
                'recipient_id_type' => $idType,
                'recipient_id_number' => $idNumber,
            ]);

            // When agent pays cash, agent gets system credit (amount + commission) in their vault
            $totalCredit = (float) $remittance->amount + (float) $remittance->agent_commission;
            $agent->incrementCurrency($remittance->currency, $totalCredit);

            // Record transaction for agent
            $tx = Transaction::create([
                'agent_id' => $agent->id,
                'user_id' => $remittance->sender_id,
                'type' => 'withdraw',
                'amount' => $remittance->amount,
                'fee' => $remittance->fee,
                'commission' => $remittance->agent_commission,
                'currency' => $remittance->currency,
                'status' => 'completed',
                'description' => "صرف حوالة نقدية للمستلم {$remittance->recipient_name} (رقم: {$remittance->remittance_code}) - إثبات هوية ({$idType}: {$idNumber}) - عمولة الوكيل: {$remittance->agent_commission} {$remittance->currency}",
            ]);

            // If sender is a registered user, notify them
            if ($remittance->sender_id) {
                PushNotificationService::sendToUser(
                    user: $remittance->sender_id,
                    title: '✅ تم استلام وصرف الحوالة',
                    message: "قام المستلم {$remittance->recipient_name} باستلام الحوالة رقم {$remittance->remittance_code} بمبلغ " . number_format($remittance->amount, 2) . " {$remittance->currency} نقداً عبر نقطتكم المعتمدة.",
                    data: ['type' => 'remittance_paid', 'remittance_code' => $remittance->remittance_code],
                    type: 'transaction'
                );
            }
        });

        $receipt = [
            'type' => 'remittance_payout',
            'title' => 'سند صرف حوالة نقدية',
            'amount' => $remittance->amount,
            'currency' => $remittance->currency,
            'recipient_name' => $remittance->recipient_name,
            'recipient_phone' => $remittance->recipient_phone,
            'sender_name' => $remittance->sender_name,
            'agent_name' => $agent->full_name,
            'remittance_code' => $remittance->remittance_code,
            'agent_commission' => $remittance->agent_commission,
            'reference' => strtoupper(substr($tx->id ?? uniqid(), 0, 13)),
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        return redirect()->route('agent.remittance.form')
            ->with('success', "تم صرف الحوالة بنجاح بمبلغ " . number_format($remittance->amount, 2) . " {$remittance->currency} للمستلم ({$remittance->recipient_name})، وتمت إضافة عمولتكم ({$remittance->agent_commission} {$remittance->currency}) إلى رصيدكم.")
            ->with('receipt', $receipt);
    }
}

