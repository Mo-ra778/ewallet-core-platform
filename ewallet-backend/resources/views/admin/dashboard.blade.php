@extends('layouts.admin')

@section('title', 'لوحة القيادة المركزية والرقابة المالية')
@section('page_title', 'نظرة عامة على السيولة والعمليات')

@section('content')
<div class="space-y-7">

    <!-- Multi-Currency System Liquidity Master Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-900">إجمالي السيولة النقدية بالنظام (Total System Liquidity by Currency)</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">مجموع الأرصدة المودعة لدى العملاء والوكلاء في كافة المحافظ</p>
            </div>
            <a href="{{ route('admin.balance.form') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>تسوية وتغذية رصيد</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- YER Liquidity -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">السيولة بالريال اليمني</span>
                    <span class="w-7 h-7 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs">YER</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($systemLiquidity['YER'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] text-teal-700 font-bold mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                    <span>العملة الأساسية للنظام</span>
                </div>
            </div>

            <!-- SAR Liquidity -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">السيولة بالريال السعودي</span>
                    <span class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">SAR</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($systemLiquidity['SAR'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] text-emerald-700 font-bold mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>رصيد متداول نشط</span>
                </div>
            </div>

            <!-- USD Liquidity -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">السيولة بالدولار الأمريكي</span>
                    <span class="w-7 h-7 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">USD</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($systemLiquidity['USD'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] text-blue-700 font-bold mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <span>تغطية دولية فورية</span>
                </div>
            </div>

            <!-- EUR Liquidity -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">السيولة باليورو الأوروبي</span>
                    <span class="w-7 h-7 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-xs">EUR</span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">
                    {{ number_format($systemLiquidity['EUR'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] text-purple-700 font-bold mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                    <span>محفظة نقدية أجنبية</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Secondary Operational KPIs (Users & Agents) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Active Users -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">العملاء النشطون</span>
                    <div class="text-xl font-bold text-slate-900 num-font mt-1">{{ number_format($activeUsers) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 font-medium">من إجمالي {{ $totalUsers }} حساب مسجل</div>
        </div>

        <!-- Pending Approval Queue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">طلبات التسجيل المعلقة</span>
                    <div class="text-xl font-bold text-slate-900 num-font mt-1">{{ $pendingUsersCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
            </div>
            <div class="text-[11px] text-amber-800 font-bold mt-2 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>تتطلب مراجعة إدارية</span>
            </div>
        </div>

        <!-- Agents Network -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">الوكلاء ومراكز الخدمة</span>
                    <div class="text-xl font-bold text-slate-900 num-font mt-1">{{ $totalAgents }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75-.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 font-medium">نقاط السحب والإيداع المعتمدة</div>
        </div>

        <!-- Total Turnover -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">حجم التداول اليوم</span>
                    <div class="text-xl font-bold text-slate-900 num-font mt-1">{{ number_format($todayVolume, 0) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 font-medium">إجمالي التداول: {{ number_format($totalVolume, 0) }}</div>
        </div>

    </div>

    <!-- Verification Queue & Live Stream Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Pending Approval Queue (1 Col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h4 class="text-xs font-bold text-slate-900">طلبات التسجيل المعلقة</h4>
                    @if($pendingUsersCount > 0)
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingUsersCount }}</span>
                    @endif
                </div>
                <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="text-[11px] font-bold text-teal-700 hover:text-teal-800 transition">عرض الكل</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingUsers as $pUser)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition">
                    <div>
                        <div class="font-bold text-xs text-slate-900">{{ $pUser->full_name }}</div>
                        <div class="text-[11px] text-slate-400 num-font mt-0.5" dir="ltr">{{ $pUser->phone }}</div>
                    </div>
                    <form action="{{ route('admin.users.status', $pUser->id) }}" method="POST" class="flex items-center gap-1.5">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg transition shadow-xs">
                            موافقة
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    لا توجد طلبات تسجيل معلقة حالياً.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Live Transactions Stream (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h4 class="text-xs font-bold text-slate-900">سجل العمليات المالية اللحظي</h4>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <a href="{{ route('admin.transactions') }}" class="text-[11px] font-bold text-teal-700 hover:text-teal-800 transition">كامل دفتر الأستاذ &larr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5">نوع الحركة</th>
                            <th class="py-3 px-5">المبلغ والعملة</th>
                            <th class="py-3 px-5">العميل المستفيد</th>
                            <th class="py-3 px-5">الطرف المقابل</th>
                            <th class="py-3 px-5">التوقيت</th>
                            <th class="py-3 px-5 text-center">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5">
                                @if($tx->type === 'deposit')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        إيداع نقدي
                                    </span>
                                @elseif($tx->type === 'withdraw')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                        سحب نقدي
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                        تحويل رصيد
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 font-bold text-slate-900 num-font">
                                {{ number_format($tx->amount, 2) }} <span class="text-[10px] font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'YER' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                {{ $tx->user ? $tx->user->full_name : '—' }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-500">
                                {{ $tx->agent ? $tx->agent->full_name : ($tx->admin ? 'إشراف: ' . $tx->admin->username : 'داخلي') }}
                            </td>
                            <td class="py-3.5 px-5 num-font text-slate-400 text-[11px]">{{ $tx->created_at->format('H:i:s') }}</td>
                            <td class="py-3.5 px-5 text-center">
                                <button onclick='showTxDetails({
                                    id: "{{ $tx->id }}",
                                    type_label: "{{ $tx->type === "deposit" ? "إيداع نقدي" : ($tx->type === "withdraw" ? "سحب نقدي" : "تحويل رصيد") }}",
                                    amount: "{{ number_format($tx->amount, 2) }}",
                                    currency: "{{ $tx->currency ?? 'YER' }}",
                                    user_name: "{{ $tx->user ? $tx->user->full_name : '' }}",
                                    counterparty: "{{ $tx->agent ? $tx->agent->full_name : ($tx->admin ? $tx->admin->username : 'داخلي') }}",
                                    description: "{{ addslashes($tx->description) }}",
                                    created_at: "{{ $tx->created_at->format('Y-m-d H:i:s') }}"
                                })' class="text-slate-400 hover:text-slate-800 p-1 rounded-lg hover:bg-slate-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">لا توجد عمليات منفذة بعد.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
