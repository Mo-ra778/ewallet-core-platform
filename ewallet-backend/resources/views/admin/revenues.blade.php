@extends('layouts.admin')

@section('title', 'خزينة وأرباح المنصة المركزية')
@section('page_title', 'خزينة وأرباح المنصة')

@section('content')
<div class="space-y-7">

    <!-- Page Header Info & Live Treasury Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <h2 class="text-base font-bold text-slate-900">خزينة وعائدات المنصة المركزية (Platform Treasury & Revenue Center)</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">تتبع صافي الإيرادات والأرباح المحصلة آلياً من رسوم التحويلات، المصارفة، الحوالات، والسحب النقدي</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.transactions') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                <span>دفتر الأستاذ العام</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-brand-800 hover:bg-brand-900 text-white transition shadow-xs">
                <svg class="w-4 h-4 text-teal-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                <span>ضبط نسب الرسوم</span>
            </a>
        </div>
    </div>

    <!-- 1. Multi-Currency Net Revenue Cards -->
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">صافي أرباح المنصة المتراكمة حسب العملة (Platform Net Profits by Currency)</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- YER Revenue -->
            <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-white p-5 rounded-2xl border border-amber-200/90 shadow-soft">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-amber-900">أرباح الريال اليمني</span>
                    <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">YER</span>
                </div>
                <div class="text-2xl font-black text-slate-900 num-font tracking-tight">
                    {{ number_format($platformRevenue['YER'] ?? 0, 2) }} <span class="text-xs font-normal text-amber-800">YER</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-amber-200/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>عمولات الوكلاء الموزعة:</span>
                    <span class="font-bold text-slate-700 num-font">{{ number_format($totalAgentCommissions['YER'] ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- SAR Revenue -->
            <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-white p-5 rounded-2xl border border-emerald-200/90 shadow-soft">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-900">أرباح الريال السعودي</span>
                    <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-900 font-extrabold text-[10px] uppercase">SAR</span>
                </div>
                <div class="text-2xl font-black text-slate-900 num-font tracking-tight">
                    {{ number_format($platformRevenue['SAR'] ?? 0, 2) }} <span class="text-xs font-normal text-emerald-800">SAR</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-emerald-200/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>عمولات الوكلاء الموزعة:</span>
                    <span class="font-bold text-slate-700 num-font">{{ number_format($totalAgentCommissions['SAR'] ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- USD Revenue -->
            <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-white p-5 rounded-2xl border border-blue-200/90 shadow-soft">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-blue-900">أرباح الدولار الأمريكي</span>
                    <span class="px-2 py-0.5 rounded-lg bg-blue-100 text-blue-900 font-extrabold text-[10px] uppercase">USD</span>
                </div>
                <div class="text-2xl font-black text-slate-900 num-font tracking-tight">
                    {{ number_format($platformRevenue['USD'] ?? 0, 2) }} <span class="text-xs font-normal text-blue-800">USD</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-blue-200/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>عمولات الوكلاء الموزعة:</span>
                    <span class="font-bold text-slate-700 num-font">{{ number_format($totalAgentCommissions['USD'] ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- EUR Revenue -->
            <div class="bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-white p-5 rounded-2xl border border-purple-200/90 shadow-soft">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-purple-900">أرباح اليورو الأوروبي</span>
                    <span class="px-2 py-0.5 rounded-lg bg-purple-100 text-purple-900 font-extrabold text-[10px] uppercase">EUR</span>
                </div>
                <div class="text-2xl font-black text-slate-900 num-font tracking-tight">
                    {{ number_format($platformRevenue['EUR'] ?? 0, 2) }} <span class="text-xs font-normal text-purple-800">EUR</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-purple-200/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>عمولات الوكلاء الموزعة:</span>
                    <span class="font-bold text-slate-700 num-font">{{ number_format($totalAgentCommissions['EUR'] ?? 0, 2) }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. Revenue Streams Breakdown (Channels) -->
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">توزيع الإيرادات حسب قنوات وروافد الخدمات (Revenue Streams)</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 block">رسوم التحويلات بين العملاء</span>
                    <div class="text-lg font-bold text-slate-900 num-font mt-0.5">{{ number_format($channelStats['transfer'] ?? 0, 2) }}</div>
                    <span class="text-[10px] text-teal-700 font-medium">رسوم P2P Transfers</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 block">عمولات مصارفة العملات</span>
                    <div class="text-lg font-bold text-slate-900 num-font mt-0.5">{{ number_format($channelStats['exchange'] ?? 0, 2) }}</div>
                    <span class="text-[10px] text-blue-700 font-medium">فوارق FX Spread</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 block">رسوم الحوالات النقدية</span>
                    <div class="text-lg font-bold text-slate-900 num-font mt-0.5">{{ number_format($channelStats['remittance'] ?? 0, 2) }}</div>
                    <span class="text-[10px] text-purple-700 font-medium">حوالات كاش مصروفة</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 block">رسوم السحب عبر الوكلاء</span>
                    <div class="text-lg font-bold text-slate-900 num-font mt-0.5">{{ number_format($channelStats['withdraw'] ?? 0, 2) }}</div>
                    <span class="text-[10px] text-amber-800 font-medium">سحب نقدي OTP</span>
                </div>
            </div>

        </div>
    </div>

    <!-- 3. Fee Ledger & Revenue Generating Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        
        <!-- Filters & Search Header -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">سجل استقطاع الرسوم والعمولات (Fee Ledger & Audit Trail)</h3>
                <p class="text-xs text-slate-400 mt-0.5">تفاصيل الحركات التي تم احتساب رسوم تشغيلية أو عمولات وكلاء عليها</p>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('admin.revenues') }}" class="flex flex-wrap items-center gap-2">
                <!-- Currency Filter -->
                <select name="currency" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500">
                    <option value="">جميع العملات</option>
                    <option value="YER" {{ ($currency ?? '') === 'YER' ? 'selected' : '' }}>YER (ريال يمني)</option>
                    <option value="SAR" {{ ($currency ?? '') === 'SAR' ? 'selected' : '' }}>SAR (ريال سعودي)</option>
                    <option value="USD" {{ ($currency ?? '') === 'USD' ? 'selected' : '' }}>USD (دولار أمريكي)</option>
                    <option value="EUR" {{ ($currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR (يورو)</option>
                </select>

                <!-- Type Filter -->
                <select name="type" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500">
                    <option value="">جميع أنواع العمليات</option>
                    <option value="transfer" {{ ($type ?? '') === 'transfer' ? 'selected' : '' }}>تحويل رصيد</option>
                    <option value="exchange" {{ ($type ?? '') === 'exchange' ? 'selected' : '' }}>مصارفة عملات</option>
                    <option value="withdraw" {{ ($type ?? '') === 'withdraw' ? 'selected' : '' }}>سحب نقدي</option>
                    <option value="deposit" {{ ($type ?? '') === 'deposit' ? 'selected' : '' }}>إيداع نقدي</option>
                </select>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="بحث بالاسم أو البيان..." class="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500 w-44 sm:w-56">
                    <button type="submit" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </button>
                </div>

                @if(!empty($currency) || !empty($type) || !empty($search))
                    <a href="{{ route('admin.revenues') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-rose-600 font-semibold transition">إلغاء الفلترة</a>
                @endif
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <th class="py-3 px-4">رقم الحركة</th>
                        <th class="py-3 px-4">الطرف المنفذ</th>
                        <th class="py-3 px-4">نوع الخدمة</th>
                        <th class="py-3 px-4">مبلغ العملية</th>
                        <th class="py-3 px-4 text-emerald-800 bg-emerald-50/40">رسوم المنصة الصافية</th>
                        <th class="py-3 px-4 text-purple-800 bg-purple-50/40">عمولة الوكيل</th>
                        <th class="py-3 px-4">التاريخ والوقت</th>
                        <th class="py-3 px-4">البيان والتفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($revenueTransactions as $tx)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Reference -->
                            <td class="py-3.5 px-4 font-bold text-slate-900 num-font text-[11px]">
                                #{{ strtoupper(substr($tx->id, 0, 8)) }}
                            </td>

                            <!-- Entity -->
                            <td class="py-3.5 px-4">
                                @if($tx->user)
                                    <div class="font-bold text-slate-900">{{ $tx->user->full_name }}</div>
                                    <div class="text-[10px] text-slate-400 num-font">{{ $tx->user->phone }}</div>
                                @elseif($tx->agent)
                                    <div class="font-bold text-slate-900">{{ $tx->agent->full_name }}</div>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-sm bg-purple-50 text-purple-700 font-semibold">وكيل معتمد</span>
                                @else
                                    <span class="text-slate-400">إدارة النظام</span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td class="py-3.5 px-4">
                                @php
                                    $typeMap = [
                                        'transfer' => ['تحويل رصيد', 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'exchange' => ['مصارفة عملات', 'bg-teal-50 text-teal-700 border-teal-200'],
                                        'withdraw' => ['سحب نقدي', 'bg-amber-50 text-amber-800 border-amber-200'],
                                        'deposit' => ['إيداع نقدي', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    ];
                                    $curType = $typeMap[$tx->type] ?? [$tx->type, 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $curType[1] }}">
                                    {{ $curType[0] }}
                                </span>
                            </td>

                            <!-- Principal Amount -->
                            <td class="py-3.5 px-4 num-font font-bold text-slate-900">
                                {{ number_format($tx->amount, 2) }} <span class="text-[10px] text-slate-400 font-normal">{{ $tx->currency }}</span>
                            </td>

                            <!-- Platform Net Fee -->
                            <td class="py-3.5 px-4 num-font font-extrabold text-emerald-700 bg-emerald-50/40">
                                +{{ number_format($tx->fee, 2) }} <span class="text-[10px] text-emerald-600 font-normal">{{ $tx->currency }}</span>
                            </td>

                            <!-- Agent Commission -->
                            <td class="py-3.5 px-4 num-font font-bold text-purple-700 bg-purple-50/40">
                                {{ $tx->commission > 0 ? number_format($tx->commission, 2) . ' ' . $tx->currency : '—' }}
                            </td>

                            <!-- Date -->
                            <td class="py-3.5 px-4 num-font text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $tx->created_at->format('Y-m-d') }}
                                <span class="text-[10px] text-slate-400 block">{{ $tx->created_at->format('H:i A') }}</span>
                            </td>

                            <!-- Description -->
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate text-[11px]" title="{{ $tx->description }}">
                                {{ $tx->description ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                </div>
                                <span class="text-xs font-semibold">لا توجد حركات رسوم مسجلة مطابقة للبحث حالياً</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($revenueTransactions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/40">
                {{ $revenueTransactions->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
