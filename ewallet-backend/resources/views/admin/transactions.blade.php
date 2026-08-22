@extends('layouts.admin')

@section('title', 'دفتر الأستاذ العام وسجل التدقيق')
@section('page_title', 'السجل المالي الرقابي العام (General Ledger & Audit Trail)')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col lg:flex-row items-center justify-between gap-4">
        
        <!-- Type Segmented Tabs -->
        <div class="flex items-center gap-1.5 flex-wrap w-full lg:w-auto">
            <span class="text-xs font-bold text-slate-400 ml-2">نوع الحركة:</span>
            
            <a href="{{ route('admin.transactions') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !$type && !$currency ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                كافة العمليات
            </a>
            <a href="{{ route('admin.transactions', ['type' => 'deposit', 'currency' => $currency]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $type === 'deposit' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/50' }}">
                إيداعات نقدية
            </a>
            <a href="{{ route('admin.transactions', ['type' => 'withdraw', 'currency' => $currency]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $type === 'withdraw' ? 'bg-amber-50 text-amber-800 border border-amber-200/80 shadow-xs' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50/50' }}">
                سحوبات كاش
            </a>
            <a href="{{ route('admin.transactions', ['type' => 'transfer', 'currency' => $currency]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $type === 'transfer' ? 'bg-blue-50 text-blue-800 border border-blue-200/80 shadow-xs' : 'text-slate-600 hover:text-blue-800 hover:bg-blue-50/50' }}">
                تحويلات رصيد
            </a>
        </div>

        <!-- Currency Selector and Live Search -->
        <form method="GET" action="{{ route('admin.transactions') }}" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
            @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
            
            <select name="currency" onchange="this.form.submit()" 
                    class="px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <option value="">جميع العملات</option>
                <option value="SAR" {{ $currency === 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                <option value="YER" {{ $currency === 'YER' ? 'selected' : '' }}>YER - ريال يمني</option>
                <option value="USD" {{ $currency === 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                <option value="EUR" {{ $currency === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
            </select>

            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالبيان أو رقم الهاتف..." 
                       class="w-full pl-3 pr-9 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
            </div>
        </form>

    </div>

    <!-- General Ledger Audit Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">رقم السند (UUID)</th>
                        <th class="py-4 px-6">نوع الحركة</th>
                        <th class="py-4 px-6">المبلغ والعملة</th>
                        <th class="py-4 px-6">الطرف الأول (العميل)</th>
                        <th class="py-4 px-6">الطرف المقابل</th>
                        <th class="py-4 px-6">البيان الرقابي والتفاصيل</th>
                        <th class="py-4 px-6">التوقيت الدقيق</th>
                        <th class="py-4 px-6 text-center">عرض السند</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-mono text-[10px] text-slate-400">
                            {{ substr($tx->id, 0, 8) }}...
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    إيداع نقدي
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    سحب نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    تحويل رصيد
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->user)
                                <a href="{{ route('admin.users.show', $tx->user->id) }}" class="font-bold text-slate-900 hover:text-teal-700 transition block">
                                    {{ $tx->user->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->agent)
                                <div class="font-bold text-slate-800">{{ $tx->agent->full_name }}</div>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">وكيل: {{ $tx->agent->phone }}</div>
                            @elseif($tx->admin)
                                <div class="font-semibold text-purple-800">إشراف: {{ $tx->admin->username }}</div>
                            @else
                                <span class="text-slate-400">تحويل داخلي</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-4 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-4 px-6 text-center">
                            <button onclick='showTxDetails({
                                id: "{{ $tx->id }}",
                                type_label: "{{ $tx->type === "deposit" ? "إيداع نقدي" : ($tx->type === "withdraw" ? "سحب نقدي" : "تحويل رصيد") }}",
                                amount: "{{ number_format($tx->amount, 2) }}",
                                currency: "{{ $tx->currency ?? 'SAR' }}",
                                user_name: "{{ $tx->user ? $tx->user->full_name : '' }}",
                                counterparty: "{{ $tx->agent ? $tx->agent->full_name : ($tx->admin ? $tx->admin->username : 'داخلي') }}",
                                description: "{{ addslashes($tx->description) }}",
                                created_at: "{{ $tx->created_at->format('Y-m-d H:i:s') }}"
                            })' class="text-slate-400 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100 transition" title="عرض السند">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">لا توجد عمليات مالية مطابقة لخيارات التصفية المحددة.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection