@extends('layouts.admin')

@section('title', 'لوحة الرقابة والسيولة')
@section('page_title', 'لوحة الرقابة المصرفية والسيولة المالية')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Summary Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: System Liquidity -->
        <div class="bg-surface-card p-5 rounded-lg border border-surface-border hover:border-surface-borderHover transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-muted">السيولة الإجمالية المودعة</span>
                <span class="text-[10px] font-mono text-emerald-700 bg-fin-tealBg border border-fin-tealBorder px-1.5 py-0.5 rounded">YER</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-ink-primary num-font tracking-tight">{{ number_format($totalSystemBalance, 2) }}</span>
                <span class="text-[11px] font-medium text-ink-secondary">ر.ي</span>
            </div>
            <p class="text-[11px] text-ink-muted mt-2">إجمالي أرصدة محافظ المستخدمين</p>
        </div>

        <!-- Metric 2: Active Accounts -->
        <div class="bg-surface-card p-5 rounded-lg border border-surface-border hover:border-surface-borderHover transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-muted">حسابات العملاء</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-ink-primary num-font">{{ $activeUsers }}</span>
                <span class="text-xs text-ink-muted">من أصل {{ $totalUsers }}</span>
            </div>
            <p class="text-[11px] text-emerald-700 font-medium mt-2">حسابات مفعلة ونشطة في النظام</p>
        </div>

        <!-- Metric 3: Pending Registrations -->
        <div class="bg-surface-card p-5 rounded-lg border border-surface-border hover:border-surface-borderHover transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-muted">طلبات فتح الحسابات</span>
                @if($pendingUsers->count() > 0)
                    <span class="text-[10px] font-bold text-fin-amber bg-fin-amberBg border border-fin-amberBorder px-1.5 py-0.5 rounded">تتطلب إجراء</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-ink-primary num-font">{{ $pendingUsers->count() }}</span>
                <span class="text-xs text-ink-muted">طلب معلق</span>
            </div>
            <p class="text-[11px] {{ $pendingUsers->count() > 0 ? 'text-fin-amber font-semibold' : 'text-ink-muted' }} mt-2">
                {{ $pendingUsers->count() > 0 ? 'بانتظار مراجعة الإدارة واعتمادها' : 'تم اعتماد كافة الطلبات' }}
            </p>
        </div>

        <!-- Metric 4: Agents Network -->
        <div class="bg-surface-card p-5 rounded-lg border border-surface-border hover:border-surface-borderHover transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-muted">شبكة الوكلاء المعتمدين</span>
                <span class="text-[10px] font-mono text-slate-700 bg-surface-subtle border border-surface-border px-1.5 py-0.5 rounded">AGENTS</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-ink-primary num-font">{{ $totalAgents }}</span>
                <span class="text-xs text-ink-muted">مركز مالي</span>
            </div>
            <p class="text-[11px] text-ink-muted mt-2">نقاط تغذية وسحب نقدي للمواطنين</p>
        </div>
    </div>

    <!-- Section: Pending Approvals Queue -->
    <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <h3 class="text-xs font-bold text-ink-primary">طلبات فتح الحسابات قيد التدقيق (Pending Verification Queue)</h3>
            </div>
            <span class="text-xs font-mono text-ink-muted font-semibold">{{ $pendingUsers->count() }} طلب</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                    <tr>
                        <th class="py-3 px-4">اسم مقدم الطلب</th>
                        <th class="py-3 px-4">رقم الهاتف</th>
                        <th class="py-3 px-4">البريد الإلكتروني</th>
                        <th class="py-3 px-4">توقيت التسجيل</th>
                        <th class="py-3 px-4 text-center">القرار الإداري</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @forelse($pendingUsers as $user)
                    <tr class="hover:bg-surface-base/80 transition">
                        <td class="py-3 px-4">
                            <div class="font-semibold text-ink-primary">{{ $user->full_name }}</div>
                            <div class="text-[10px] text-ink-muted font-mono">{{ $user->id }}</div>
                        </td>
                        <td class="py-3 px-4 num-font font-medium text-slate-800" dir="ltr">{{ $user->phone }}</td>
                        <td class="py-3 px-4 text-ink-secondary">{{ $user->email ?? '—' }}</td>
                        <td class="py-3 px-4 num-font text-ink-muted">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="bg-slate-900 hover:bg-black text-white px-3 py-1 rounded text-xs font-medium transition">
                                        اعتماد وتفعيل
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" onclick="return confirm('تأكيد رفض طلب التسجيل؟')" class="bg-surface-card hover:bg-fin-crimsonBg text-fin-crimson border border-surface-border hover:border-fin-crimsonBorder px-3 py-1 rounded text-xs font-medium transition">
                                        رفض
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-ink-muted">
                            لا توجد أي طلبات معلقة حالياً، طابور التدقيق مكتمل.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Live Audit Stream -->
    <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-ink-primary">سجل العمليات المالية اللحظي</h3>
                <p class="text-[11px] text-ink-muted mt-0.5">مراقبة مباشرة لتدفقات الأموال بين الحسابات والوكلاء</p>
            </div>
            <a href="{{ route('admin.transactions') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink-primary transition">
                فتح السجل الكامل &larr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                    <tr>
                        <th class="py-3 px-4">نوع الحركة</th>
                        <th class="py-3 px-4">المبلغ والعملة</th>
                        <th class="py-3 px-4">الطرف المستفيد / المحول</th>
                        <th class="py-3 px-4">البيان الرقابي</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">التوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-surface-base/80 transition">
                        <td class="py-3 px-4">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                    إيداع نقدي
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                    سحب نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-subtle text-ink-primary border border-surface-border">
                                    تحويل رصيد
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-semibold text-ink-muted font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($tx->user)
                                <div class="font-medium text-ink-primary">{{ $tx->user->full_name }}</div>
                            @endif
                            @if($tx->agent)
                                <div class="text-[11px] text-fin-teal font-medium">الوكيل: {{ $tx->agent->full_name }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-ink-secondary max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-3 px-4">
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                مكتمل
                            </span>
                        </td>
                        <td class="py-3 px-4 num-font text-ink-muted">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-muted">لا توجد عمليات منفذة في السجل حتى الآن.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
