@extends('layouts.admin')

@section('title', 'إدارة الحوالات النقدية')
@section('page_title', 'شبكة الحوالات النقدية غير المقيدة (Cash Remittance Network)')

@section('content')
<div class="space-y-6">

    <!-- Remittances KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي الحوالات الصادرة</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900 num-font">{{ number_format($stats['total_count']) }}</span>
                <span class="text-xs font-bold text-slate-500">حوالة</span>
            </div>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-xs font-bold text-amber-600 block mb-1">حوالات بانتظار الصرف (معلقة)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-700 num-font">{{ number_format($stats['pending_count']) }}</span>
                <span class="text-xs font-bold text-amber-600">جاهزة للاستلام</span>
            </div>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-xs font-bold text-emerald-600 block mb-1">حوالات تم صرفها بنجاح</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-700 num-font">{{ number_format($stats['paid_count']) }}</span>
                <span class="text-xs font-bold text-emerald-600">مسلمة نقداً</span>
            </div>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-xs font-bold text-slate-400 block mb-1">حوالات ملغاة ومسترجعة</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-600 num-font">{{ number_format($stats['cancelled_count']) }}</span>
                <span class="text-xs font-bold text-slate-400">مستردة</span>
            </div>
        </div>

    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col lg:flex-row items-center justify-between gap-4">
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 flex-wrap w-full lg:w-auto">
            <span class="text-xs font-bold text-slate-400 ml-2">الحالة:</span>
            
            <a href="{{ route('admin.remittances') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                الكل
            </a>
            <a href="{{ route('admin.remittances', ['status' => 'pending', 'currency' => request('currency')]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-amber-50 text-amber-800 border border-amber-200/80 shadow-xs' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50/50' }}">
                معلقة (بانتظار الصرف)
            </a>
            <a href="{{ route('admin.remittances', ['status' => 'paid', 'currency' => request('currency')]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'paid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/50' }}">
                تم الصرف
            </a>
            <a href="{{ route('admin.remittances', ['status' => 'cancelled', 'currency' => request('currency')]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'cancelled' ? 'bg-rose-50 text-rose-800 border border-rose-200/80 shadow-xs' : 'text-slate-600 hover:text-rose-800 hover:bg-rose-50/50' }}">
                ملغاة
            </a>
        </div>

        <!-- Currency Selector and Search -->
        <form method="GET" action="{{ route('admin.remittances') }}" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            
            <select name="currency" onchange="this.form.submit()" 
                    class="px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <option value="">جميع العملات</option>
                <option value="YER" {{ request('currency') === 'YER' ? 'selected' : '' }}>YER - ريال يمني</option>
                <option value="SAR" {{ request('currency') === 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                <option value="USD" {{ request('currency') === 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                <option value="EUR" {{ request('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
            </select>

            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث برقم الحوالة أو الاسم أو الهاتف..." 
                       class="w-full pl-3 pr-9 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
            </div>
        </form>

    </div>

    <!-- Remittances Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">رقم الحوالة</th>
                        <th class="py-4 px-6">المبلغ والرسوم</th>
                        <th class="py-4 px-6">المرسل (العميل)</th>
                        <th class="py-4 px-6">المستلم (الكاش)</th>
                        <th class="py-4 px-6">الوكيل الصارف</th>
                        <th class="py-4 px-6">الحالة</th>
                        <th class="py-4 px-6">التاريخ والتوقيت</th>
                        <th class="py-4 px-6 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($remittances as $rem)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        
                        <!-- Remittance Code -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900 num-font uppercase">{{ $rem->remittance_code }}</span>
                                <span class="text-[10px] text-slate-400 num-font bg-slate-100 px-1.5 py-0.5 rounded font-mono">PIN: {{ $rem->pin_code }}</span>
                            </div>
                        </td>

                        <!-- Amount & Fees -->
                        <td class="py-4 px-6">
                            <div class="font-extrabold text-slate-900 num-font text-sm">
                                {{ number_format($rem->amount, 2) }} <span class="text-xs text-slate-500 font-medium">{{ $rem->currency }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                رسوم: {{ number_format($rem->fee, 2) }} | عمولة وكيل: {{ number_format($rem->agent_commission, 2) }}
                            </div>
                        </td>

                        <!-- Sender -->
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">{{ $rem->sender_name }}</div>
                            <div class="text-[11px] text-slate-400 num-font">{{ $rem->sender_phone }}</div>
                        </td>

                        <!-- Recipient -->
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">{{ $rem->recipient_name }}</div>
                            <div class="text-[11px] text-slate-400 num-font">{{ $rem->recipient_phone }}</div>
                            @if($rem->recipient_id_number)
                                <div class="text-[10px] text-teal-700 font-medium mt-0.5">{{ $rem->recipient_id_type }}: {{ $rem->recipient_id_number }}</div>
                            @endif
                        </td>

                        <!-- Paying Agent -->
                        <td class="py-4 px-6">
                            @if($rem->payingAgent)
                                <div class="font-bold text-slate-900">{{ $rem->payingAgent->full_name }}</div>
                                <div class="text-[10px] text-slate-400 num-font">{{ $rem->payingAgent->phone }}</div>
                            @else
                                <span class="text-[11px] text-slate-400 italic">لم تُصرف بعد</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-6">
                            @if($rem->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    معلقة
                                </span>
                            @elseif($rem->status === 'paid')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    تم الصرف
                                </span>
                            @elseif($rem->status === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    ملغاة ومسترجعة
                                </span>
                            @endif
                        </td>

                        <!-- Timestamps -->
                        <td class="py-4 px-6 text-slate-500 num-font text-[11px]">
                            <div>{{ $rem->created_at->format('Y-m-d H:i') }}</div>
                            @if($rem->paid_at)
                                <div class="text-[10px] text-emerald-700 mt-0.5">صُرفت: {{ $rem->paid_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-center">
                            @if($rem->status === 'pending')
                                <form id="cancel-rem-form-{{ $rem->id }}" action="{{ route('admin.remittance.cancel', $rem->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="showConfirmDialog({ title: 'إلغاء الحوالة النقدية', message: 'هل أنت متأكد من إلغاء هذه الحوالة ({{ $rem->remittance_code }}) واسترجاع مبلغها لحساب المرسل؟', confirmText: 'نعم، إلغاء واسترجاع', confirmType: 'danger', onConfirm: () => document.getElementById('cancel-rem-form-{{ $rem->id }}').submit() });" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition border border-rose-200/60" title="إلغاء واسترجاع للمرسل">
                                        إلغاء واسترجاع
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            لا توجد حوالات نقدية مطابقة للمعايير المحددة.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($remittances->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $remittances->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
