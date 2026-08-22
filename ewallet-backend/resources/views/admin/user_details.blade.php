@extends('layouts.admin')

@section('title', 'الملف المالي للعميل')
@section('page_title', 'كشف الحساب والملف الشخصي')

@section('content')
<div class="space-y-6">

    <!-- Back Button & Master Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            <span>الرجوع لسجل المستخدمين</span>
        </a>
    </div>

    <!-- User Master Card & Multi-Currency Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Card -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-700 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        {{ mb_substr($user->full_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $user->full_name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-500 num-font font-medium" dir="ltr">{{ $user->phone }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs text-slate-400">{{ $user->email ?? 'بدون بريد إلكتروني' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    @if($user->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            حساب مفعّل
                        </span>
                    @elseif($user->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            قيد المراجعة
                        </span>
                    @elseif($user->status === 'suspended')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200/60">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            حساب معلّق
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            مرفوض
                        </span>
                    @endif
                </div>
            </div>

            <!-- Multi-Currency Wallet Balances -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-500 block">أرصدة محفظة العميل بالعملات:</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">يمني (YER)</span>
                        <div class="text-base font-bold text-slate-900 num-font mt-0.5">
                            {{ number_format($user->getCurrencyBalance('YER'), 2) }}
                        </div>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">سعودي (SAR)</span>
                        <div class="text-base font-bold text-slate-900 num-font mt-0.5">
                            {{ number_format($user->getCurrencyBalance('SAR'), 2) }}
                        </div>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">دولار (USD)</span>
                        <div class="text-base font-bold text-slate-900 num-font mt-0.5">
                            {{ number_format($user->getCurrencyBalance('USD'), 2) }}
                        </div>
                    </div>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">يورو (EUR)</span>
                        <div class="text-base font-bold text-slate-900 num-font mt-0.5">
                            {{ number_format($user->getCurrencyBalance('EUR'), 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Status Control Panel -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft space-y-4">
            <h3 class="text-xs font-bold text-slate-900 pb-3 border-b border-slate-100">التحكم في حالة الحساب</h3>

            <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">تغيير الحالة إلى:</label>
                    <select name="status" class="w-full px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>مفعّل ومصرح (Active)</option>
                        <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>معلّق مؤقتاً (Suspended)</option>
                        <option value="rejected" {{ $user->status === 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">سبب الإجراء (ملاحظة رقابية):</label>
                    <input type="text" name="reason" placeholder="مثال: تم تدقيق الهوية الوطنية بنجاح" 
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition shadow-xs">
                    تحديث الحالة وإرسال إشعار للعميل
                </button>
            </form>
        </div>

    </div>

    <!-- Personal Statement / Transaction History -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900">كشف الحساب المالي وسجل حركات العميل</h3>
            <span class="num-font text-xs font-semibold text-slate-400">{{ $transactions->total() }} حركة مسجلة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">نوع الحركة</th>
                        <th class="py-4 px-6">المبلغ والعملة</th>
                        <th class="py-4 px-6">الطرف المقابل</th>
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
                                    إيداع وارد
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    سحب صادر
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                    تحويل رصيد
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'YER' }}</span>
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
                        <td class="py-4 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-4 px-6 text-center">
                            <button onclick='showTxDetails({
                                id: "{{ $tx->id }}",
                                type_label: "{{ $tx->type === "deposit" ? "إيداع نقدي" : ($tx->type === "withdraw" ? "سحب نقدي" : "تحويل رصيد") }}",
                                amount: "{{ number_format($tx->amount, 2) }}",
                                currency: "{{ $tx->currency ?? 'YER' }}",
                                user_name: "{{ $user->full_name }}",
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
                        <td colspan="6" class="py-12 text-center text-slate-400">لا توجد عمليات مسجلة في كشف حساب هذا العميل حتى الآن.</td>
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
