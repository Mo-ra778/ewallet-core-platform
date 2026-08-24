@extends('layouts.agent')

@section('title', 'محطة الصرافة والنقدية')

@section('content')
<div class="space-y-6">

    <!-- Welcome & Multi-Currency Liquidity Master Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">أرصدة العهدة النقدية المتاحة (Multi-Currency Vault)</h2>
                <p class="text-xs text-slate-400 mt-0.5">السيولة المتوفرة لدى مركزك للإيداع والتسليم النقدي</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                المركز مفعّل وجاهز
            </span>
        </div>

        <!-- 4 Multi-Currency Balance Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- YER Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">الريال اليمني (YER)</span>
                    <span class="w-7 h-7 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs">ر.ي</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($agent->getCurrencyBalance('YER'), 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                    <span>إجمالي الإيداعات:</span>
                    <span class="num-font font-bold text-slate-700">{{ number_format($depositsByCurrency['YER'] ?? 0, 0) }} ر.ي</span>
                </div>
            </div>

            <!-- SAR Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">الريال السعودي (SAR)</span>
                    <span class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">SAR</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($agent->getCurrencyBalance('SAR'), 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                    <span>إجمالي الإيداعات:</span>
                    <span class="num-font font-bold text-slate-700">{{ number_format($depositsByCurrency['SAR'] ?? 0, 2) }} SAR</span>
                </div>
            </div>

            <!-- USD Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">الدولار الأمريكي (USD)</span>
                    <span class="w-7 h-7 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">$</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($agent->getCurrencyBalance('USD'), 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                    <span>إجمالي الإيداعات:</span>
                    <span class="num-font font-bold text-slate-700">{{ number_format($depositsByCurrency['USD'] ?? 0, 2) }} $</span>
                </div>
            </div>

            <!-- EUR Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">اليورو (EUR)</span>
                    <span class="w-7 h-7 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-xs">€</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($agent->getCurrencyBalance('EUR'), 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                    <span>إجمالي الإيداعات:</span>
                    <span class="num-font font-bold text-slate-700">{{ number_format($depositsByCurrency['EUR'] ?? 0, 2) }} €</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Quick Action Launchers (Cash-In, Cash-Out & Remittance Payout) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('agent.deposit.form') }}" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:border-teal-500 hover:shadow-card transition flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition">إيداع نقدي (Cash-In)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">تغذية رصيد العميل فوراً</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-700 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </a>

        <a href="{{ route('agent.withdraw.form') }}" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:border-amber-500 hover:shadow-card transition flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-amber-800 transition">سحب نقدي (Cash-Out)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">سحب كاش بكود OTP</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-amber-800 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </a>

        <a href="{{ route('agent.remittance.form') }}" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:border-blue-500 hover:shadow-card transition flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700 transition">صرف حوالة نقدية</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">صرف كاش بالرقم والكود السري</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-700 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </a>
    </div>


    <!-- Recent Cash Transactions Stream -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900">آخر العمليات النقدية المنفذة في هذا المركز</h3>
            <a href="{{ route('agent.transactions') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition">
                عرض كامل السجل &larr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">نوع الحركة</th>
                        <th class="py-4 px-6">المبلغ والعملة</th>
                        <th class="py-4 px-6">بيانات العميل</th>
                        <th class="py-4 px-6">البيان</th>
                        <th class="py-4 px-6">التاريخ والتوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    إيداع نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    سحب نقدي
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'YER' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->user)
                                <div class="font-bold text-slate-900">{{ $tx->user->full_name }}</div>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-4 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">لا توجد حركات نقدية مسجلة بعد في هذا المركز.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
