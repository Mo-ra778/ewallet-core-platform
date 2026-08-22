@extends('layouts.admin')

@section('title', 'الملف المالي لمركز الوكيل')
@section('page_title', 'كشف حساب الوكيل وإدارة العهدة النقدية')

@section('content')
<div class="space-y-6">

    <!-- Back Button & Master Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.agents') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            <span>الرجوع لدليل الوكلاء المعتمدين</span>
        </a>
    </div>

    <!-- Agent Master Card & Vault Balances -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Card & Multi-Currency Vault (2 Columns) -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-purple-700 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        {{ mb_substr($agent->full_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $agent->full_name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-500 num-font font-medium" dir="ltr">هاتف: {{ $agent->phone }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs text-slate-400 font-mono" dir="ltr">UUID: {{ substr($agent->id, 0, 8) }}...</span>
                        </div>
                    </div>
                </div>

                <div>
                    @if($agent->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            مركز معتمد ومفعّل
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200/60">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            مركز معلّق
                        </span>
                    @endif
                </div>
            </div>

            <!-- Multi-Currency Vault Balances Grid -->
            <div class="space-y-3">
                <span class="text-xs font-bold text-slate-500 block">أرصدة العهدة النقدية المتوفرة لدى المركز:</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    
                    <!-- YER -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">ريال يمني (YER)</span>
                        <div class="text-base font-extrabold text-slate-900 num-font mt-1">
                            {{ number_format($agent->getCurrencyBalance('YER'), 2) }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-2 border-t border-slate-200/60 pt-1.5">
                            إيداعات: <strong class="num-font text-slate-700">{{ number_format($depositsByCurrency['YER'] ?? 0, 0) }}</strong>
                        </div>
                    </div>

                    <!-- SAR -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">ريال سعودي (SAR)</span>
                        <div class="text-base font-extrabold text-slate-900 num-font mt-1">
                            {{ number_format($agent->getCurrencyBalance('SAR'), 2) }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-2 border-t border-slate-200/60 pt-1.5">
                            إيداعات: <strong class="num-font text-slate-700">{{ number_format($depositsByCurrency['SAR'] ?? 0, 2) }}</strong>
                        </div>
                    </div>

                    <!-- USD -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">دولار أمريكي (USD)</span>
                        <div class="text-base font-extrabold text-slate-900 num-font mt-1">
                            {{ number_format($agent->getCurrencyBalance('USD'), 2) }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-2 border-t border-slate-200/60 pt-1.5">
                            إيداعات: <strong class="num-font text-slate-700">{{ number_format($depositsByCurrency['USD'] ?? 0, 2) }}</strong>
                        </div>
                    </div>

                    <!-- EUR -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">يورو أوروبي (EUR)</span>
                        <div class="text-base font-extrabold text-slate-900 num-font mt-1">
                            {{ number_format($agent->getCurrencyBalance('EUR'), 2) }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-2 border-t border-slate-200/60 pt-1.5">
                            إيداعات: <strong class="num-font text-slate-700">{{ number_format($depositsByCurrency['EUR'] ?? 0, 2) }}</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Direct Quick Vault Top-Up / Adjustment Terminal (1 Column) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-900">تغذية أو تسوية عهدة هذا الوكيل</h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <form action="{{ route('admin.balance.adjust') }}" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="target_type" value="agent">
                <input type="hidden" name="target_id" value="{{ $agent->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع العملية</label>
                    <select name="operation" class="w-full px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none transition">
                        <option value="credit">تغذية عهدة إضافية (+) Credit</option>
                        <option value="debit">خصم / استرداد عهدة (-) Debit</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="1" name="amount" required placeholder="0.00" 
                               class="w-full px-3 py-2 text-sm font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 num-font focus:bg-white focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                        <select name="currency" class="w-full px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none transition">
                            <option value="YER">YER (يمني)</option>
                            <option value="SAR">SAR (سعودي)</option>
                            <option value="USD">USD (دولار)</option>
                            <option value="EUR">EUR (يورو)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">البيان الرقابي <span class="text-rose-500">*</span></label>
                    <input type="text" name="reason" required placeholder="مثال: تغذية عهدة بموجب سند بنكي رقم 442" 
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none transition">
                </div>

                <button type="submit" onclick="return confirm('تأكيد تنفيذ عملية التغذية/التسوية على عهدة الوكيل؟')"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>تنفيذ التغذية الفورية للعهدة</span>
                </button>
            </form>
        </div>

    </div>

    <!-- Agent Transaction History & Audit Trail Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-900">سجل حركات وعمليات هذا المركز المعتمد</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">عمليات الإيداع، السحب، والتغذية الإدارية</p>
            </div>
            <span class="num-font text-xs font-semibold text-slate-400">{{ $transactions->total() }} حركة مسجلة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">نوع الحركة</th>
                        <th class="py-4 px-6">المبلغ والعملة</th>
                        <th class="py-4 px-6">بيانات العميل المستفيد</th>
                        <th class="py-4 px-6">البيان والسبب</th>
                        <th class="py-4 px-6">التوقيت</th>
                        <th class="py-4 px-6 text-center">عرض السند</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    إيداع نقدي
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    سحب نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                    تحويل / تسوية
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'YER' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->user)
                                <a href="{{ route('admin.users.show', $tx->user->id) }}" class="font-bold text-slate-900 hover:text-teal-700 transition block">
                                    {{ $tx->user->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-slate-400">تغذية إدارية مباشرة</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-4 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-4 px-6 text-center">
                            <button onclick='showTxDetails({
                                id: "{{ $tx->id }}",
                                type_label: "{{ $tx->type === "deposit" ? "إيداع نقدي" : ($tx->type === "withdraw" ? "سحب نقدي" : "تحويل رصيد") }}",
                                amount: "{{ number_format($tx->amount, 2) }}",
                                currency: "{{ $tx->currency ?? 'YER' }}",
                                user_name: "{{ $tx->user ? $tx->user->full_name : 'مركز الوكيل' }}",
                                counterparty: "{{ $agent->full_name }}",
                                description: "{{ addslashes($tx->description) }}",
                                created_at: "{{ $tx->created_at->format('Y-m-d H:i:s') }}"
                            })' class="text-slate-400 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100 transition" title="عرض السند">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">لا توجد عمليات مسجلة في سجل هذا المركز بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
