@extends('layouts.admin')

@section('title', 'الملف المالي وكشف الحساب')
@section('page_title', 'الملف الرقابي وكشف الحساب — ' . $user->full_name)

@section('content')
<div class="space-y-6">

    <!-- Top Action Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users') }}" class="text-xs font-medium text-ink-muted hover:text-ink-primary transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            <span>الرجوع لسجل المستخدمين</span>
        </a>
    </div>

    <!-- User Header Master Record -->
    <div class="bg-surface-card rounded-lg border border-surface-border p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-base flex-shrink-0">
                    {{ mb_substr($user->full_name, 0, 1) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-sm font-bold text-ink-primary">{{ $user->full_name }}</h2>
                        @if($user->status === 'active')
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                نشط ومفعّل
                            </span>
                        @elseif($user->status === 'pending')
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                قيد المراجعة
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-fin-crimsonBg text-fin-crimson border border-fin-crimsonBorder">
                                معلّق
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs text-ink-muted">
                        <div>الهاتف: <strong class="text-ink-primary font-mono" dir="ltr">{{ $user->phone }}</strong></div>
                        <div>البريد: <strong class="text-ink-primary">{{ $user->email ?? '—' }}</strong></div>
                        <div>تاريخ التسجيل: <strong class="text-ink-primary num-font">{{ $user->created_at->format('Y-m-d') }}</strong></div>
                        <div>المعرف: <strong class="text-ink-muted font-mono text-[10px]">{{ $user->id }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Current Liquid Balance -->
            <div class="bg-surface-base border border-surface-border rounded-lg p-3.5 min-w-[180px] text-left">
                <span class="text-[11px] font-semibold text-ink-muted block">الرصيد المتاح الحالي</span>
                <div class="text-lg font-bold text-ink-primary num-font mt-0.5">
                    {{ number_format($user->balance, 2) }} <span class="text-[11px] font-sans font-medium text-ink-muted">ر.ي</span>
                </div>
            </div>
        </div>

        <!-- Quick Administration Actions -->
        <div class="mt-5 pt-4 border-t border-surface-border flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-ink-secondary ml-2">الإجراءات الرقابية:</span>
            @if($user->status === 'pending')
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-xs font-medium px-3 py-1 rounded transition">
                        اعتماد وتفعيل الحساب
                    </button>
                </form>
            @elseif($user->status === 'active')
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="suspended">
                    <button type="submit" onclick="return confirm('تأكيد تعليق حساب هذا العميل؟')" class="text-fin-crimson hover:bg-fin-crimsonBg border border-surface-border hover:border-fin-crimsonBorder text-xs font-medium px-3 py-1 rounded transition">
                        تعليق الحساب
                    </button>
                </form>
            @elseif($user->status === 'suspended')
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-xs font-medium px-3 py-1 rounded transition">
                        إلغاء التعليق
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.balance.form') }}" class="text-xs font-medium text-ink-secondary hover:text-ink-primary bg-surface-card hover:bg-surface-subtle border border-surface-border px-3 py-1 rounded transition">
                تسوية رصيد مباشر &larr;
            </a>
        </div>
    </div>

    <!-- Personal Statement Audit Table -->
    <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-ink-primary">كشف الحساب وسجل العمليات المالية الخاصة</h3>
                <p class="text-[11px] text-ink-muted mt-0.5">تفاصيل الإيداعات، السحوبات، والتحويلات مع بيان أطراف العملية</p>
            </div>
            <span class="text-xs font-mono text-ink-muted font-semibold">{{ $transactions->total() }} حركة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                    <tr>
                        <th class="py-3 px-4">نوع الحركة</th>
                        <th class="py-3 px-4">المبلغ والعملة</th>
                        <th class="py-3 px-4">الطرف الآخر (من / إلى)</th>
                        <th class="py-3 px-4">البيان والوصف</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">التاريخ والتوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-surface-base/80 transition">
                        <td class="py-3 px-4">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                    إيداع
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                    سحب نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-subtle text-ink-primary border border-surface-border">
                                    تحويل
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-semibold text-emerald-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($tx->agent)
                                <div class="text-[11px] text-fin-teal font-medium">الوكيل: {{ $tx->agent->full_name }}</div>
                            @elseif($tx->admin)
                                <div class="text-[11px] text-ink-muted font-medium">إشراف إداري: {{ $tx->admin->username }}</div>
                            @else
                                <span class="text-ink-muted">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-ink-secondary max-w-sm">{{ $tx->description }}</td>
                        <td class="py-3 px-4">
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                مكتمل
                            </span>
                        </td>
                        <td class="py-3 px-4 num-font text-ink-muted">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-muted">لا توجد حركات مالية مسجلة لهذا الحساب.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-3.5 border-t border-surface-border">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
