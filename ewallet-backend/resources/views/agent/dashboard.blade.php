@extends('layouts.agent')

@section('title', 'لوحة المؤشرات — محطة الوكيل')

@section('content')
<div class="space-y-6">

    <!-- Primary Liquidity & Quick Action Panel -->
    <div class="bg-surface rounded-xl border border-line p-6 sm:p-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-ink-500">الرصيد النقدي المتاح في عهدة المركز</span>
                <div class="text-3xl sm:text-4xl font-extrabold text-ink-900 num-font tracking-tight flex items-baseline gap-2">
                    <span>{{ number_format($agent->balance, 2) }}</span>
                    <span class="text-xs font-semibold text-ink-500 font-sans">ريال</span>
                </div>
                <div class="flex items-center gap-2 pt-2 text-xs text-ink-700">
                    <span class="w-2 h-2 rounded-full {{ $agent->status === 'active' ? 'bg-fin-teal' : 'bg-fin-ruby' }}"></span>
                    <span>حالة المحطة: <strong class="text-ink-900">{{ $agent->status === 'active' ? 'مفعّلة ومصرح بالعمليات' : 'معلّقة مؤقتاً' }}</strong></span>
                </div>
            </div>

            <!-- Terminal Cashier Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('agent.deposit.form') }}" class="flex items-center gap-2 bg-fin-teal hover:bg-teal-800 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>إيداع نقدي للعميل (Cash-In)</span>
                </a>
                <a href="{{ route('agent.withdraw.form') }}" class="flex items-center gap-2 bg-fin-amberBg hover:bg-amber-100 text-fin-amber border border-fin-amberLine text-xs font-semibold px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                    <span>سحب نقدي (Cash-Out OTP)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface p-4 rounded-xl border border-line shadow-sm">
            <span class="text-xs font-semibold text-ink-500">إجمالي حركات المركز</span>
            <div class="text-xl font-bold text-ink-900 num-font mt-1">{{ $totalTransactions }}</div>
            <p class="text-[10px] text-ink-400 mt-1">عمليات إيداع وسحب مؤكدة</p>
        </div>

        <div class="bg-surface p-4 rounded-xl border border-line shadow-sm">
            <span class="text-xs font-semibold text-ink-500">إجمالي مبالغ الإيداع</span>
            <div class="text-xl font-bold text-fin-teal num-font mt-1">{{ number_format($totalDeposited, 2) }} <span class="text-xs font-normal text-ink-400 font-sans">ر.ي</span></div>
            <p class="text-[10px] text-ink-400 mt-1">تغذية نقدية لمحفظات المواطنين</p>
        </div>

        <div class="bg-surface p-4 rounded-xl border border-line shadow-sm">
            <span class="text-xs font-semibold text-ink-500">إجمالي مبالغ السحب</span>
            <div class="text-xl font-bold text-fin-amber num-font mt-1">{{ number_format($totalWithdrawn, 2) }} <span class="text-xs font-normal text-ink-400 font-sans">ر.ي</span></div>
            <p class="text-[10px] text-ink-400 mt-1">تسليم كاش عبر كود OTP</p>
        </div>
    </div>

    <!-- Live Terminal Stream Table -->
    <div class="bg-surface rounded-xl border border-line shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-line flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-ink-900">أحدث العمليات المنفذة في محطة الوكيل</h3>
                <p class="text-[11px] text-ink-500 mt-0.5">سجل الحركات اللحظي لنقطة الخدمة</p>
            </div>
            <a href="{{ route('agent.transactions') }}" class="text-xs font-semibold text-fin-teal hover:text-ink-900 transition">
                فتح السجل الكامل &larr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-subtle text-ink-700 font-semibold border-b border-line">
                    <tr>
                        <th class="py-3 px-4">نوع الحركة</th>
                        <th class="py-3 px-4">المبلغ والعملة</th>
                        <th class="py-3 px-4">بيانات العميل المستفيد</th>
                        <th class="py-3 px-4">البيان والتفاصيل</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">التوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-subtle/50 transition">
                        <td class="py-3 px-4">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-tealBg text-fin-teal border border-fin-tealLine">
                                    إيداع نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-amberBg text-fin-amber border border-fin-amberLine">
                                    سحب نقدي
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-ink-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-semibold text-fin-teal font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($tx->user)
                                <div class="font-medium text-ink-900">{{ $tx->user->full_name }}</div>
                                <div class="text-[10px] text-ink-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-ink-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-ink-700 max-w-sm">{{ $tx->description }}</td>
                        <td class="py-3 px-4">
                            <span class="text-[10px] font-bold text-fin-teal bg-fin-tealBg border border-fin-tealLine px-1.5 py-0.5 rounded">
                                مكتمل
                            </span>
                        </td>
                        <td class="py-3 px-4 num-font text-ink-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-400">لم يتم تنفيذ أي عمليات مالية في هذا المركز بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
