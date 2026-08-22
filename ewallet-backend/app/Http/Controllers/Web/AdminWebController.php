<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    /**
     * Show Admin Login Form
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
            'username.required' => 'اسم المستخدم مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        $admin = Admin::where('username', $request->input('username'))->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password_hash)) {
            return back()->withErrors(['username' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'])->withInput();
        }

        session(['admin_id' => $admin->id, 'admin_username' => $admin->username, 'admin_role' => $admin->role]);

        return redirect()->route('admin.dashboard')->with('success', 'تم تسجيل الدخول إلى لوحة التحكم بنجاح.');
    }

    /**
     * Admin Logout
     */
    public function logout()
    {
        session()->forget(['admin_id', 'admin_username', 'admin_role']);
        return redirect()->route('admin.login.form')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Admin Dashboard Overview
     */
    public function dashboard()
    {
        $admin = Admin::findOrFail(session('admin_id'));

        $totalUsers = User::count();
        $pendingUsersCount = User::where('status', 'pending')->count();
        $activeUsersCount = User::where('status', 'active')->count();
        $totalAgents = Agent::count();

        $totalUserBalances = User::sum('balance');
        $totalAgentBalances = Agent::sum('balance');
        $totalSystemCirculation = $totalUserBalances + $totalAgentBalances;

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

        $activeUsers = $activeUsersCount;
        $totalSystemBalance = $totalSystemCirculation;

        return view('admin.dashboard', compact(
            'admin',
            'totalUsers',
            'activeUsers',
            'pendingUsersCount',
            'activeUsersCount',
            'totalAgents',
            'totalSystemBalance',
            'totalSystemCirculation',
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
            'status' => 'required|in:active,suspended,rejected',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $oldStatus = $user->status;
        $newStatus = $request->input('status');

        $user->update(['status' => $newStatus]);

        // Send notification to user about status change
        $statusMessages = [
            'active' => 'تمت الموافقة على حسابك وتفعيله بنجاح. يمكنك الآن إجراء التحويلات والإيداعات.',
            'suspended' => 'تم تعليق حسابك مؤقتاً من قبل الإدارة.',
            'rejected' => 'تم رفض طلب التسجيل الخاص بحسابك.',
        ];

        Notification::create([
            'recipient_id' => $user->id,
            'recipient_type' => 'user',
            'title' => 'تحديث حالة الحساب',
            'message' => $statusMessages[$newStatus] . ($request->input('reason') ? ' السبب: ' . $request->input('reason') : ''),
            'type' => 'alert',
            'is_read' => false,
        ]);

        return back()->with('success', "تم تحديث حالة المستخدم [{$user->full_name}] إلى " . match($newStatus) {
            'active' => 'مفعّل (Approved)',
            'suspended' => 'معلّق (Suspended)',
            'rejected' => 'مرفوض (Rejected)',
            default => $newStatus
        });
    }

    /**
     * Show User Details & Personal Financial Statement
     */
    public function userDetails(string $id)
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
     * Create New Agent
     */
    public function createAgent(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|unique:agents,phone|max:20',
            'password' => 'required|string|min:6',
            'initial_balance' => 'nullable|numeric|min:0',
        ], [
            'full_name.required' => 'اسم الوكيل مطلوب.',
            'phone.required' => 'رقم هاتف الوكيل مطلوب.',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لوكيل آخر.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        $initialBalance = (float) ($request->input('initial_balance') ?? 0);

        DB::transaction(function () use ($request, $initialBalance) {
            $agent = Agent::create([
                'full_name' => $request->input('full_name'),
                'phone' => $request->input('phone'),
                'password_hash' => Hash::make($request->input('password')),
                'balance' => $initialBalance,
                'status' => 'active',
            ]);

            if ($initialBalance > 0) {
                Transaction::create([
                    'agent_id' => $agent->id,
                    'admin_id' => session('admin_id'),
                    'type' => 'deposit',
                    'amount' => $initialBalance,
                    'currency' => $request->input('currency', 'SAR'),
                    'status' => 'completed',
                    'description' => 'تغذية رصيد افتتاحي للوكيل من الإدارة',
                ]);
            }
        });

        return back()->with('success', 'تم إنشاء حساب الوكيل الجديد بنجاح.');
    }

    /**
     * Update Agent Status (Suspend / Activate)
     */
    public function updateAgentStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended',
        ]);

        $agent = Agent::findOrFail($id);
        $newStatus = $request->input('status');
        $agent->update(['status' => $newStatus]);

        Notification::create([
            'recipient_id' => $agent->id,
            'recipient_type' => 'agent',
            'title' => 'تحديث حالة حساب الوكيل',
            'message' => $newStatus === 'active' ? 'تم إعادة تفعيل حساب الوكيل الخاص بك.' : 'تم تعليق حساب الوكيل من قبل الإدارة المركزية.',
            'type' => 'alert',
            'is_read' => false,
        ]);

        return back()->with('success', "تم تحديث حالة الوكيل [{$agent->full_name}] إلى " . ($newStatus === 'active' ? 'مفعّل' : 'معلّق'));
    }

    /**
     * Direct Balance Adjustment (Credit / Debit) Form & List
     */
    public function adjustBalanceForm()
    {
        $users = User::where('status', 'active')->orderBy('full_name')->get();
        $agents = Agent::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.adjust_balance', compact('users', 'agents'));
    }

    /**
     * Execute Direct Balance Adjustment
     */
    public function adjustBalance(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:user,agent',
            'target_id' => 'required|string',
            'operation' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
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
        $currency = $request->input('currency', 'SAR');
        $reason = $request->input('reason');
        $adminId = session('admin_id');

        $entity = $targetType === 'user' ? User::findOrFail($targetId) : Agent::findOrFail($targetId);

        if ($operation === 'debit' && (float) $entity->balance < $amount) {
            return back()->withErrors(['amount' => "الرصيد الحالي ({$entity->balance} {$currency}) غير كافٍ للخصم."])->withInput();
        }

        DB::transaction(function () use ($entity, $targetType, $operation, $amount, $currency, $reason, $adminId) {
            if ($operation === 'credit') {
                $entity->increment('balance', $amount);
                $txType = 'deposit';
                $actionText = 'إيداع/تغذية إدارية';
            } else {
                $entity->decrement('balance', $amount);
                $txType = 'withdraw';
                $actionText = 'خصم إداري';
            }

            Transaction::create([
                'user_id' => $targetType === 'user' ? $entity->id : null,
                'agent_id' => $targetType === 'agent' ? $entity->id : null,
                'admin_id' => $adminId,
                'type' => $txType,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "{$actionText}: {$reason}",
            ]);

            Notification::create([
                'recipient_id' => $entity->id,
                'recipient_type' => $targetType,
                'title' => $actionText,
                'message' => "تم تنفيذ عملية {$actionText} بمبلغ {$amount} {$currency} على حسابك. السبب: {$reason}",
                'type' => 'transaction',
                'is_read' => false,
            ]);
        });

        return back()->with('success', "تم تنفيذ العملية بنجاح على حساب {$entity->full_name}.");
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
                      $uq->where('phone', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('phone', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
         }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.transactions', compact('transactions', 'type', 'currency', 'search'));
    }

    /**
     * Send Custom In-App Notification
     */
    public function notifications(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:150',
                'message' => 'required|string',
                'type' => 'nullable|string|in:alert,message,transaction,otp',
            ]);

            $userId = $request->input('user_id') ?? $request->input('target_id');
            $recType = $request->input('recipient_type', 'user');
            $notifType = $request->input('type', 'alert');
            $title = $request->input('title');
            $message = $request->input('message');

            if ($recType === 'all' || !$userId) {
                $users = User::all();
                foreach ($users as $u) {
                    Notification::create([
                        'recipient_id' => $u->id,
                        'recipient_type' => 'user',
                        'title' => $title,
                        'message' => $message,
                        'type' => $notifType,
                    ]);
                }
            } else {
                $user = User::findOrFail($userId);
                Notification::create([
                    'recipient_id' => $user->id,
                    'recipient_type' => 'user',
                    'title' => $title,
                    'message' => $message,
                    'type' => $notifType,
                ]);
            }

            return back()->with('success', 'تم إرسال الإشعار بنجاح.');
        }

        $users = User::orderBy('full_name')->get();
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(15);
        $recentNotifications = $notifications;

        return view('admin.notifications', compact('users', 'notifications', 'recentNotifications'));
    }
}
