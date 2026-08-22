<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
     * Agent Dashboard
     */
    public function dashboard()
    {
        $agent = Agent::findOrFail(session('agent_id'));

        $totalTransactions = Transaction::where('agent_id', $agent->id)->count();

        $totalDeposited = (float) Transaction::where('agent_id', $agent->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        $totalWithdrawn = (float) Transaction::where('agent_id', $agent->id)
            ->where('type', 'withdraw')
            ->where('status', 'completed')
            ->sum('amount');

        $totalDeposits = $totalDeposited;
        $totalWithdrawals = $totalWithdrawn;

        $recentTransactions = Transaction::where('agent_id', $agent->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.dashboard', compact(
            'agent',
            'totalTransactions',
            'totalDeposited',
            'totalWithdrawn',
            'totalDeposits',
            'totalWithdrawals',
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
     * Process Direct Cash-In Deposit to User
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
        $currency = $request->input('currency', 'SAR');

        if ((float) $agent->balance < $amount) {
            return back()->withErrors(['amount' => 'رصيدك الحالي كوكيل لا يكفي لإتمام هذه العملية.'])->withInput();
        }

        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'العميل غير مسجل في النظام.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['phone' => 'حساب العميل غير نشط (' . $user->status . ').'])->withInput();
        }

        // Execute Deposit with DB::transaction
        DB::transaction(function () use ($agent, $user, $amount, $currency, $request) {
            $agent->decrement('balance', $amount);
            $user->increment('balance', $amount);

            Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'deposit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "إيداع نقدي عبر الوكيل: {$agent->full_name}" . ($request->input('notes') ? ' - ' . $request->input('notes') : ''),
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'إيداع نقدي ناجح',
                'message' => "تم إيداع مبلغ {$amount} {$currency} في حسابك بنجاح عبر الوكيل {$agent->full_name}.",
                'type' => 'transaction',
                'is_read' => false,
            ]);
        });

        return redirect()->route('agent.dashboard')->with('success', "تم إيداع مبلغ {$amount} {$currency} بنجاح في حساب العميل {$user->full_name}.");
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
     * Request Cash Withdrawal OTP (Step 1 Submission)
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
        $currency = $request->input('currency', 'SAR');
        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'العميل غير مسجل في النظام.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['phone' => 'حساب العميل غير نشط ولا يمكنه السحب حالياً.'])->withInput();
        }

        if ((float) $user->balance < $amount) {
            return back()->withErrors(['amount' => "رصيد العميل ({$user->balance} {$currency}) غير كافٍ لسحب مبلغ {$amount} {$currency}."])->withInput();
        }

        // Generate OTP & Notify User
        $otp = $this->otpService->generateWithdrawalOtp($user, $agent->id, $amount, $currency);

        return view('agent.withdraw_confirm', [
            'agent' => $agent,
            'user' => $user,
            'amount' => $amount,
            'currency' => $currency,
            'demo_otp' => $otp, // Shown for testing convenience
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
        $otp = $request->input('otp');

        // Verify OTP
        $otpData = $this->otpService->verifyWithdrawalOtp($user->id, $agent->id, $otp);

        if (!$otpData) {
            return back()->withErrors(['otp' => 'رمز التحقق غير صحيح أو انتهت صلاحيته (5 دقائق).'])->withInput();
        }

        $amount = (float) $otpData['amount'];
        $currency = $otpData['currency'] ?? 'SAR';

        if ((float) $user->balance < $amount) {
            return redirect()->route('agent.withdraw.form')->withErrors(['amount' => 'رصيد العميل لم يعد كافياً لإتمام السحب.']);
        }

        // Complete Withdrawal inside DB::transaction
        DB::transaction(function () use ($agent, $user, $amount, $currency) {
            $user->decrement('balance', $amount);
            $agent->increment('balance', $amount);

            Transaction::create([
                'user_id' => $user->id,
                'agent_id' => $agent->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "سحب نقدي (Cash-Out) عبر الوكيل: {$agent->full_name}",
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'recipient_type' => 'user',
                'title' => 'سحب نقدي مؤكد',
                'message' => "تم سحب مبلغ {$amount} {$currency} نقداً من حسابك عبر الوكيل {$agent->full_name}.",
                'type' => 'transaction',
                'is_read' => false,
            ]);
        });

        return redirect()->route('agent.dashboard')->with('success', "تم تأكيد سحب مبلغ {$amount} {$currency} بنجاح وتسليمه للعميل {$user->full_name}.");
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
}
