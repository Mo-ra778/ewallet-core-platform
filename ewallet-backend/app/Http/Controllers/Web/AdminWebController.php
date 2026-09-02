<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminWebController extends Controller
{
    /**
     * Show Admin Login Page
     */
    public function showLogin()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Handle Admin Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'يرجى إدخال اسم المستخدم.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        $admin = Admin::where('username', $request->input('username'))->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password_hash)) {
            return back()->withErrors(['username' => 'بيانات الاعتماد غير صحيحة.'])->withInput();
        }

        session([
            'admin_id' => $admin->id,
            'admin_username' => $admin->username,
            'admin_role' => $admin->role,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'تم تسجيل الدخول بنجاح كمدير للنظام.');
    }

    /**
     * Handle Admin Logout
     */
    public function logout()
    {
        session()->forget(['admin_id', 'admin_username', 'admin_role']);
        return redirect()->route('admin.login.form')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Admin Dashboard Overview with Multi-Currency Liquidity
     */
    public function dashboard()
    {
        $admin = Admin::findOrFail(session('admin_id'));

        $totalUsers = User::count();
        $pendingUsersCount = User::where('status', 'pending')->count();
        $activeUsersCount = User::where('status', 'active')->count();
        $totalAgents = Agent::count();

        // Calculate Multi-Currency Total Liquidity (Users + Agents)
        $systemLiquidity = [
            'YER' => (float) (User::sum('balance_yer') + Agent::sum('balance_yer')),
            'SAR' => (float) (User::sum('balance_sar') + Agent::sum('balance_sar')),
            'USD' => (float) (User::sum('balance_usd') + Agent::sum('balance_usd')),
            'EUR' => (float) (User::sum('balance_eur') + Agent::sum('balance_eur')),
        ];

        // Overall Primary Currency (YER) Total
        $totalSystemCirculation = $systemLiquidity['YER'];
        $totalSystemBalance = $totalSystemCirculation;
        $activeUsers = $activeUsersCount;

        // Calculate Platform Accumulated Revenue & Profits (Sum of transaction fees)
        $platformRevenue = [
            'YER' => (float) Transaction::where('status', 'completed')->where('currency', 'YER')->sum('fee'),
            'SAR' => (float) Transaction::where('status', 'completed')->where('currency', 'SAR')->sum('fee'),
            'USD' => (float) Transaction::where('status', 'completed')->where('currency', 'USD')->sum('fee'),
            'EUR' => (float) Transaction::where('status', 'completed')->where('currency', 'EUR')->sum('fee'),
        ];

        $totalAgentCommissions = [
            'YER' => (float) Transaction::where('status', 'completed')->where('currency', 'YER')->sum('commission'),
            'SAR' => (float) Transaction::where('status', 'completed')->where('currency', 'SAR')->sum('commission'),
            'USD' => (float) Transaction::where('status', 'completed')->where('currency', 'USD')->sum('commission'),
            'EUR' => (float) Transaction::where('status', 'completed')->where('currency', 'EUR')->sum('commission'),
        ];

        // Transaction Volume Stats
        $totalVolume = Transaction::where('status', 'completed')->sum('amount');
        $todayVolume = Transaction::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTransactions = Transaction::with(['user', 'agent', 'admin'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'admin',
            'totalUsers',
            'activeUsers',
            'pendingUsersCount',
            'activeUsersCount',
            'totalAgents',
            'systemLiquidity',
            'totalSystemBalance',
            'totalSystemCirculation',
            'platformRevenue',
            'totalAgentCommissions',
            'totalVolume',
            'todayVolume',
            'pendingUsers',
            'recentTransactions'
        ));
    }

    /**
     * Users Management List
     */
    public function users(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = User::query();

        if ($status && in_array($status, ['pending', 'active', 'suspended', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $pendingCount = User::where('status', 'pending')->count();

        return view('admin.users', compact('users', 'status', 'search', 'pendingCount'));
    }

    /**
     * Update User Status (Approve, Reject, Suspend, Activate)
     */
    public function updateUserStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,pending,suspended,rejected',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $oldStatus = $user->status;
        $newStatus = $request->input('status');
        $reason = $request->input('reason');

        $user->update(['status' => $newStatus]);

        $statusLabels = [
            'active' => 'تفعيل الحساب',
            'suspended' => 'تعليق الحساب',
            'rejected' => 'رفض الحساب',
            'pending' => 'إعادة للمراجعة',
        ];

        PushNotificationService::sendToUser(
            user: $user,
            title: '🔔 تحديث حالة الحساب',
            message: "تم تغيير حالة حسابك إلى (" . ($statusLabels[$newStatus] ?? $newStatus) . ")." . ($reason ? " السبب: {$reason}" : ''),
            data: ['type' => 'status_change', 'status' => $newStatus],
            type: 'alert'
        );

        return back()->with('success', "تم تحديث حالة حساب العميل {$user->full_name} إلى {$newStatus} بنجاح.");
    }

    /**
     * User Details and Account Statement
     */
    public function showUser(string $id)
    {
        $user = User::findOrFail($id);
        $transactions = Transaction::where('user_id', $user->id)
            ->with(['agent', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.user_details', compact('user', 'transactions'));
    }

    /**
     * Agents Management List
     */
    public function agents()
    {
        $agents = Agent::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.agents', compact('agents'));
    }

    /**
     * Show Agent Profile, Vault Balances, and Financial Movements
     */
    public function showAgent(string $id)
    {
        $agent = Agent::findOrFail($id);

        $totalTransactions = Transaction::where('agent_id', $agent->id)->count();

        // Multi-currency turnover stats
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

        $transactions = Transaction::where('agent_id', $agent->id)
            ->with(['user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.agent_details', compact(
            'agent',
            'transactions',
            'depositsByCurrency',
            'withdrawalsByCurrency',
            'totalTransactions'
        ));
    }

    /**
     * Create New Authorized Agent with Multi-Currency Initial Vault Balances
     */
    public function createAgent(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:agents,phone',
            'password' => 'required|string|min:6',
            'initial_balance_yer' => 'nullable|numeric|min:0',
            'initial_balance_sar' => 'nullable|numeric|min:0',
            'initial_balance_usd' => 'nullable|numeric|min:0',
            'initial_balance_eur' => 'nullable|numeric|min:0',
        ], [
            'full_name.required' => 'اسم الوكيل / المركز مطلوب.',
            'phone.required' => 'رقم هاتف الدخول للوكيل مطلوب.',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً لمركز وكيل آخر.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 6 أحرف.',
        ]);

        $yer = (float) $request->input('initial_balance_yer', 0);
        $sar = (float) $request->input('initial_balance_sar', 0);
        $usd = (float) $request->input('initial_balance_usd', 0);
        $eur = (float) $request->input('initial_balance_eur', 0);

        $agent = DB::transaction(function () use ($request, $yer, $sar, $usd, $eur) {
            $agent = Agent::create([
                'full_name' => $request->input('full_name'),
                'phone' => $request->input('phone'),
                'password_hash' => Hash::make($request->input('password')),
                'balance' => $yer,
                'balance_yer' => $yer,
                'balance_sar' => $sar,
                'balance_usd' => $usd,
                'balance_eur' => $eur,
                'status' => 'active',
            ]);

            $admin = Admin::where('id', session('admin_id'))->first() ?? Admin::first();
            $adminId = $admin ? $admin->id : null;

            // Record initial seed transactions for non-zero balances
            $initialCurrencies = [
                'YER' => $yer,
                'SAR' => $sar,
                'USD' => $usd,
                'EUR' => $eur,
            ];

            foreach ($initialCurrencies as $curr => $amt) {
                if ($amt > 0) {
                    Transaction::create([
                        'agent_id' => $agent->id,
                        'admin_id' => $adminId,
                        'type' => 'deposit',
                        'amount' => $amt,
                        'currency' => $curr,
                        'status' => 'completed',
                        'description' => "تغذية عهدة افتتاحية ({$curr}) عند تدشين مركز الوكيل",
                    ]);
                }
            }

            return $agent;
        });

        return back()->with('success', "تم إنشاء واعتماد مركز الوكيل {$agent->full_name} وتغذية العهدة الافتتاحية بنجاح.");
    }

    /**
     * Update Agent Status
     */
    public function updateAgentStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended',
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update(['status' => $request->input('status')]);

        return back()->with('success', "تم تحديث حالة الوكيل {$agent->full_name} بنجاح.");
    }

    /**
     * Direct Balance Adjustment (Credit / Debit) Form
     */
    public function adjustBalanceForm()
    {
        $users = User::where('status', 'active')->orderBy('full_name')->get();
        $agents = Agent::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.adjust_balance', compact('users', 'agents'));
    }

    /**
     * Execute Direct Balance Adjustment in Selected Currency
     */
    public function adjustBalance(Request $request)
    {
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $normalizedAmount = str_replace(
                ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', ','],
                ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ''],
                $rawAmount
            );
            $request->merge(['amount' => $normalizedAmount]);
        }

        $request->validate([
            'target_type' => 'required|in:user,agent',
            'target_id' => 'required|string',
            'operation' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:SAR,YER,USD,EUR',
            'reason' => 'required|string|max:255',
        ], [
            'amount.required' => 'المبلغ مطلوب.',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر.',
            'currency.required' => 'يرجى تحديد العملة.',
            'reason.required' => 'يرجى كتابة سبب العملية (ملاحظة إدارية).',
        ]);

        $targetType = $request->input('target_type');
        $targetId = $request->input('target_id');
        $operation = $request->input('operation');
        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));
        $reason = $request->input('reason');
        $admin = Admin::where('id', session('admin_id'))->first() ?? Admin::first();
        $adminId = $admin ? $admin->id : null;

        try {
            $entity = $targetType === 'user' 
                ? User::where('id', $targetId)->first() 
                : Agent::where('id', $targetId)->first();

            if (!$entity) {
                return back()->withErrors(['target_id' => 'الحساب المستهدف غير موجود أو غير نشط في النظام.'])->withInput();
            }

            if ($operation === 'debit' && !$entity->hasSufficientBalance($amount, $currency)) {
                $curBal = number_format($entity->getCurrencyBalance($currency), 2);
                return back()->withErrors(['amount' => "الرصيد الحالي بعملة {$currency} ({$curBal}) غير كافٍ لإجراء الخصم."])->withInput();
            }

            $tx = null;
            DB::transaction(function () use ($targetId, $targetType, $operation, $amount, $currency, $reason, $adminId, &$tx, &$entity) {
                $entity = $targetType === 'user' 
                    ? User::where('id', $targetId)->lockForUpdate()->firstOrFail() 
                    : Agent::where('id', $targetId)->lockForUpdate()->firstOrFail();

                if ($operation === 'credit') {
                    $entity->incrementCurrency($currency, $amount);
                    $txType = 'deposit';
                    $actionText = 'إيداع/تغذية إدارية';
                } else {
                    $entity->decrementCurrency($currency, $amount);
                    $txType = 'withdraw';
                    $actionText = 'خصم إداري';
                }

                $tx = Transaction::create([
                    'user_id' => $targetType === 'user' ? $entity->id : null,
                    'agent_id' => $targetType === 'agent' ? $entity->id : null,
                    'admin_id' => $adminId,
                    'type' => $txType,
                    'amount' => $amount,
                    'fee' => 0.00,
                    'commission' => 0.00,
                    'currency' => $currency,
                    'status' => 'completed',
                    'description' => "{$actionText} ({$currency}): {$reason}",
                ]);

                try {
                    if ($targetType === 'user') {
                        PushNotificationService::sendToUser(
                            user: $entity,
                            title: '💳 ' . $actionText,
                            message: "تم تنفيذ عملية {$actionText} بمبلغ " . number_format($amount, 2) . " {$currency} على حسابك. السبب: {$reason}",
                            data: ['type' => 'adjustment', 'amount' => $amount, 'currency' => $currency],
                            type: 'transaction'
                        );
                    } else {
                        Notification::create([
                            'recipient_id' => $entity->id,
                            'recipient_type' => $targetType,
                            'title' => $actionText,
                            'message' => "تم تنفيذ عملية {$actionText} بمبلغ " . number_format($amount, 2) . " {$currency} على حسابك. السبب: {$reason}",
                            'type' => 'transaction',
                            'is_read' => false,
                        ]);
                    }
                } catch (\Throwable $notifEx) {
                    Log::warning("Adjustment Notification skipped: " . $notifEx->getMessage());
                }
            });

            $receipt = [
                'type' => 'adjustment',
                'title' => $operation === 'credit' ? 'سند تسوية وتغذية رصيد إدارية (+)' : 'سند تسوية وخصم إداري (-)',
                'amount' => $amount,
                'currency' => $currency,
                'target_name' => $entity->full_name,
                'target_type' => $targetType === 'user' ? 'مشترك (عميل)' : 'وكيل معتمد',
                'operation' => $operation === 'credit' ? 'تغذية رصيد (+)' : 'خصم مباشر (-)',
                'reason' => $reason,
                'reference' => strtoupper(substr($tx->id ?? uniqid(), 0, 13)),
                'date' => now()->format('Y-m-d H:i:s'),
            ];

            return redirect()->route('admin.balance.form')
                ->with('success', "تم تنفيذ التسوية بنجاح بمبلغ " . number_format($amount, 2) . " {$currency} على حساب {$entity->full_name}.")
                ->with('receipt', $receipt);
        } catch (\Throwable $e) {
            Log::error("Balance Adjustment Error: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'تعذر تنفيذ التسوية: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * All System Financial Transactions
     */
    public function transactions(Request $request)
    {
        $type = $request->query('type');
        $currency = $request->query('currency');
        $search = $request->query('search');

        $query = Transaction::with(['user', 'agent', 'admin']);

        if ($type && in_array($type, ['deposit', 'withdraw', 'transfer'])) {
            $query->where('type', $type);
        }

        if ($currency && in_array($currency, ['SAR', 'YER', 'USD', 'EUR'])) {
            $query->where('currency', $currency);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.transactions', compact('transactions', 'type', 'currency', 'search'));
    }

    /**
     * Notifications Center & Broadcast
     */
    public function notifications()
    {
        $users = User::where('status', 'active')->orderBy('full_name')->get();
        $agents = Agent::where('status', 'active')->orderBy('full_name')->get();
        $notifications = Notification::with('recipient')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications', compact('users', 'agents', 'notifications'));
    }

    /**
     * Send Broadcast Notification to Users or Agents
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:alert,message,transaction',
            'recipient_target' => 'nullable|string',
        ], [
            'title.required' => 'عنوان الإشعار مطلوب.',
            'message.required' => 'نص الإشعار مطلوب.',
        ]);

        $target = $request->input('recipient_target', 'all_users');
        $title = $request->input('title');
        $message = $request->input('message');
        $type = $request->input('type');

        if ($target === 'all_users' || empty($target)) {
            $users = User::where('status', 'active')->get();
            foreach ($users as $u) {
                PushNotificationService::sendToUser(
                    user: $u,
                    title: $title,
                    message: $message,
                    data: ['type' => 'broadcast'],
                    type: $type
                );
            }
            $msg = 'تم بث الإشعار بنجاح لكافة المستخدمين النشطين (' . $users->count() . ' مستخدم).';
        } elseif ($target === 'all_agents') {
            $agents = Agent::where('status', 'active')->get();
            foreach ($agents as $a) {
                Notification::create([
                    'recipient_id' => $a->id,
                    'recipient_type' => 'agent',
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                ]);
            }
            $msg = 'تم بث الإشعار بنجاح لكافة الوكلاء المعتمدين (' . $agents->count() . ' وكيل).';
        } elseif (str_starts_with($target, 'user:')) {
            $userId = substr($target, 5);
            $user = User::findOrFail($userId);
            PushNotificationService::sendToUser(
                user: $user,
                title: $title,
                message: $message,
                data: ['type' => 'direct_message'],
                type: $type
            );
            $msg = "تم إرسال الإشعار للعميل {$user->full_name} بنجاح.";
        } elseif (str_starts_with($target, 'agent:')) {
            $agentId = substr($target, 6);
            $agent = Agent::findOrFail($agentId);
            Notification::create([
                'recipient_id' => $agent->id,
                'recipient_type' => 'agent',
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);
            $msg = "تم إرسال الإشعار لمركز الوكيل {$agent->full_name} بنجاح.";
        } else {
            $msg = 'تم إرسال الإشعار بنجاح.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Wallet Control Center & Settings
     */
    public function settings()
    {
        $rates = \App\Models\ExchangeRate::orderBy('from_currency')->get();
        $settings = \App\Models\SystemSetting::all()->keyBy('key');

        return view('admin.settings', compact('rates', 'settings'));
    }

    /**
     * Update Batch of Exchange Rates
     */
    public function updateExchangeRates(Request $request)
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*.id' => 'required|string',
            'rates.*.rate' => 'required|numeric|min:0.000001',
            'rates.*.buy_rate' => 'nullable|numeric|min:0',
            'rates.*.sell_rate' => 'nullable|numeric|min:0',
            'rates.*.custom_fee_percent' => 'nullable|numeric|min:0|max:100',
            'rates.*.min_exchange_amount' => 'nullable|numeric|min:0',
            'rates.*.max_exchange_amount' => 'nullable|numeric|min:0',
            'rates.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($request->input('rates') as $rData) {
            $rate = \App\Models\ExchangeRate::find($rData['id']);
            if ($rate) {
                $rate->update([
                    'rate' => $rData['rate'],
                    'buy_rate' => $rData['buy_rate'] ?? $rData['rate'],
                    'sell_rate' => $rData['sell_rate'] ?? $rData['rate'],
                    'custom_fee_percent' => isset($rData['custom_fee_percent']) && $rData['custom_fee_percent'] !== '' ? (float) $rData['custom_fee_percent'] : null,
                    'min_exchange_amount' => isset($rData['min_exchange_amount']) && $rData['min_exchange_amount'] !== '' ? (float) $rData['min_exchange_amount'] : null,
                    'max_exchange_amount' => isset($rData['max_exchange_amount']) && $rData['max_exchange_amount'] !== '' ? (float) $rData['max_exchange_amount'] : null,
                    'is_active' => isset($rData['is_active']) ? (bool) $rData['is_active'] : false,
                ]);
            }
        }

        return back()->with('success', 'تم تحديث مصفوفة أسعار الصرف والعمولات المخصصة بنجاح.');
    }

    /**
     * Create New Custom Currency Exchange Pair Dynamically
     */
    public function createExchangeRate(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string|max:10',
            'to_currency' => 'required|string|max:10|different:from_currency',
            'rate' => 'required|numeric|min:0.000001',
            'buy_rate' => 'nullable|numeric|min:0',
            'sell_rate' => 'nullable|numeric|min:0',
            'custom_fee_percent' => 'nullable|numeric|min:0|max:100',
            'min_exchange_amount' => 'nullable|numeric|min:0',
            'max_exchange_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ], [
            'from_currency.required' => 'العملة المصدر مطلوبة (مثال: USD, SAR, AED).',
            'to_currency.required' => 'العملة المستهدفة مطلوبة.',
            'to_currency.different' => 'العملة المصدر والمستهدفة يجب أن تكونا مختلفتين.',
            'rate.required' => 'سعر الصرف مطلوب.',
            'rate.min' => 'سعر الصرف يجب أن يكون أكبر من 0.',
        ]);

        $from = strtoupper(trim($request->input('from_currency')));
        $to = strtoupper(trim($request->input('to_currency')));

        \App\Models\ExchangeRate::updateOrCreate(
            ['from_currency' => $from, 'to_currency' => $to],
            [
                'rate' => (float) $request->input('rate'),
                'buy_rate' => (float) ($request->input('buy_rate') ?? $request->input('rate')),
                'sell_rate' => (float) ($request->input('sell_rate') ?? $request->input('rate')),
                'custom_fee_percent' => $request->filled('custom_fee_percent') ? (float) $request->input('custom_fee_percent') : null,
                'min_exchange_amount' => $request->filled('min_exchange_amount') ? (float) $request->input('min_exchange_amount') : null,
                'max_exchange_amount' => $request->filled('max_exchange_amount') ? (float) $request->input('max_exchange_amount') : null,
                'notes' => $request->input('notes'),
                'is_active' => true,
            ]
        );

        return back()->with('success', "تم إضافة زوج الصرف ({$from} &rarr; {$to}) مع شروط وعمولة المصارفة بنجاح.");
    }

    /**
     * Delete Exchange Rate Pair
     */
    public function deleteExchangeRate(string $id)
    {
        $rate = \App\Models\ExchangeRate::findOrFail($id);
        $pairName = "{$rate->from_currency} &rarr; {$rate->to_currency}";
        $rate->delete();

        return back()->with('success', "تم حذف زوج الصرف ({$pairName}) من النظام.");
    }

    /**
     * Update System Fees and Operating Limits
     */
    public function updateSettings(Request $request)
    {
        $fields = [
            'transfer_fee_percent',
            'transfer_fee_fixed',
            'withdrawal_fee_percent',
            'agent_commission_percent',
            'exchange_fee_percent',
            'min_transfer_amount',
            'max_transfer_amount',
            'daily_transfer_limit',
            'app_name',
            'maintenance_mode',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                \App\Models\SystemSetting::set($field, $request->input($field));
            }
        }

        return back()->with('success', 'تم حفظ وتطبيق إعدادات المحفظة والرسوم بنجاح.');
    }

    /**
     * Cash Remittances Management & Ledger
     */
    public function remittances(Request $request)
    {
        $query = \App\Models\Remittance::with(['sender:id,full_name,phone', 'payingAgent:id,full_name,phone']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('remittance_code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('sender_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($currency = $request->query('currency')) {
            $query->where('currency', strtoupper($currency));
        }

        $remittances = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_count' => \App\Models\Remittance::count(),
            'pending_count' => \App\Models\Remittance::where('status', 'pending')->count(),
            'paid_count' => \App\Models\Remittance::where('status', 'paid')->count(),
            'cancelled_count' => \App\Models\Remittance::where('status', 'cancelled')->count(),
            'total_volume_yer' => (float) \App\Models\Remittance::where('currency', 'YER')->where('status', 'paid')->sum('amount'),
            'total_volume_sar' => (float) \App\Models\Remittance::where('currency', 'SAR')->where('status', 'paid')->sum('amount'),
            'total_volume_usd' => (float) \App\Models\Remittance::where('currency', 'USD')->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.remittances', compact('remittances', 'stats'));
    }

    /**
     * Cancel an Unclaimed Remittance by Admin and Refund Sender
     */
    public function cancelRemittance(string $id)
    {
        $remittance = \App\Models\Remittance::findOrFail($id);

        if ($remittance->status !== 'pending') {
            return back()->withErrors(['error' => "لا يمكن إلغاء الحوالة لأنها في حالة ({$remittance->status})."]);
        }

        DB::transaction(function () use ($remittance) {
            $remittance->update(['status' => 'cancelled']);

            if ($remittance->sender_id) {
                $user = User::find($remittance->sender_id);
                if ($user) {
                    $user->incrementCurrency($remittance->currency, (float) $remittance->amount);

                    Transaction::create([
                        'user_id' => $user->id,
                        'admin_id' => session('admin_id'),
                        'type' => 'transfer',
                        'amount' => $remittance->amount,
                        'fee' => 0.00,
                        'commission' => 0.00,
                        'currency' => $remittance->currency,
                        'status' => 'completed',
                        'description' => "إلغاء واسترجاع حوالة نقدية إدارياً (رقم: {$remittance->remittance_code}) للمستلم {$remittance->recipient_name}",
                    ]);

                    PushNotificationService::sendToUser(
                        user: $user,
                        title: '↩️ إلغاء حوالة نقدية واسترجاع المبلغ',
                        message: "تم إلغاء الحوالة رقم {$remittance->remittance_code} من قبل الإدارة واسترجاع مبلغ " . number_format($remittance->amount, 2) . " {$remittance->currency} إلى حسابك.",
                        data: ['type' => 'remittance_cancelled_by_admin', 'remittance_code' => $remittance->remittance_code],
                        type: 'transaction'
                    );
                }
            }
        });

        return back()->with('success', "تم إلغاء الحوالة رقم ({$remittance->remittance_code}) بنجاح واسترجاع المبلغ لحساب المرسل.");
    }

    /**
     * Platform Treasury & Revenue Center
     */
    public function revenues(Request $request)
    {
        $currency = $request->query('currency');
        $type = $request->query('type');
        $search = $request->query('search');

        // Total Revenues by Currency
        $platformRevenue = [
            'YER' => (float) Transaction::where('status', 'completed')->where('currency', 'YER')->sum('fee'),
            'SAR' => (float) Transaction::where('status', 'completed')->where('currency', 'SAR')->sum('fee'),
            'USD' => (float) Transaction::where('status', 'completed')->where('currency', 'USD')->sum('fee'),
            'EUR' => (float) Transaction::where('status', 'completed')->where('currency', 'EUR')->sum('fee'),
        ];

        // Total Agent Commissions by Currency
        $totalAgentCommissions = [
            'YER' => (float) Transaction::where('status', 'completed')->where('currency', 'YER')->sum('commission'),
            'SAR' => (float) Transaction::where('status', 'completed')->where('currency', 'SAR')->sum('commission'),
            'USD' => (float) Transaction::where('status', 'completed')->where('currency', 'USD')->sum('commission'),
            'EUR' => (float) Transaction::where('status', 'completed')->where('currency', 'EUR')->sum('commission'),
        ];

        // Total Revenues by Channel/Type
        $channelStats = [
            'transfer' => (float) Transaction::where('status', 'completed')->where('type', 'transfer')->sum('fee'),
            'exchange' => (float) Transaction::where('status', 'completed')->where('type', 'exchange')->sum('fee'),
            'withdraw' => (float) Transaction::where('status', 'completed')->where('type', 'withdraw')->sum('fee'),
            'remittance' => (float) \App\Models\Remittance::where('status', 'paid')->sum('fee'),
        ];

        // Query Fee-generating Transactions
        $query = Transaction::with(['user', 'agent', 'admin'])
            ->where(function ($q) {
                $q->where('fee', '>', 0)
                  ->orWhere('commission', '>', 0);
            });

        if ($currency && in_array($currency, ['SAR', 'YER', 'USD', 'EUR'])) {
            $query->where('currency', $currency);
        }

        if ($type && in_array($type, ['deposit', 'withdraw', 'transfer', 'exchange'])) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $revenueTransactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.revenues', compact(
            'platformRevenue',
            'totalAgentCommissions',
            'channelStats',
            'revenueTransactions',
            'currency',
            'type',
            'search'
        ));
    }
}

