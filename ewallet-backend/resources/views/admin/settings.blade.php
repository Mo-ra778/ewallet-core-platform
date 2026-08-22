@extends('layouts.admin')

@section('title', 'إدارة أزواج الصرف والسياسات المالية')
@section('page_title', 'محرك الصرف المفتوح وإدارة العمولات الديناميكية')

@section('content')
<div class="space-y-6">

    <!-- Header Banner & Action -->
    <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">محرك الصرف المفتوح والتحكم الديناميكي بالعمولات</h2>
                <p class="text-xs text-slate-400 mt-0.5">حرية كاملة لإضافة أي أزواج عملات وتحديد أسعار الصرف ونسب العمولات والحدود بشكل مستقل</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="#new-pair-form" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-teal-700 hover:bg-teal-800 text-white transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>إضافة زوج مصارفة جديد</span>
            </a>
        </div>
    </div>

    <!-- Section 1: Dynamic Add New Pair Card -->
    <div id="new-pair-form" class="bg-white p-6 sm:p-7 rounded-2xl border border-teal-200/90 shadow-soft space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-teal-700 text-white flex items-center justify-center font-bold text-xs">
                    +
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">إضافة وتعيين زوج عملات جديد (Dynamic Currency Pair)</h3>
                    <p class="text-[11px] text-slate-400">يمكنك كتابة رمز أي عملة (مثال: SAR, USD, YER, AED, KWD, EUR, OMR) وتحديد سعره وعمولته الخاصة</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.rates.create') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- From Currency -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">العملة المصدر (From) <span class="text-rose-500">*</span></label>
                    <input type="text" name="from_currency" value="{{ old('from_currency') }}" required placeholder="مثال: USD أو AED" uppercase
                           class="w-full px-3 py-2 text-xs font-bold uppercase bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none transition">
                </div>

                <!-- To Currency -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">العملة المستهدفة (To) <span class="text-rose-500">*</span></label>
                    <input type="text" name="to_currency" value="{{ old('to_currency') }}" required placeholder="مثال: YER أو SAR" uppercase
                           class="w-full px-3 py-2 text-xs font-bold uppercase bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Exchange Rate -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">سعر الصرف المعتمد (Rate) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.000001" min="0.000001" name="rate" value="{{ old('rate') }}" required placeholder="مثال: 1600.00" 
                           class="w-full px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 num-font focus:bg-white focus:outline-none transition">
                </div>

                <!-- Custom Fee Percent -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">العمولة المخصصة للزوج (%) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="custom_fee_percent" value="{{ old('custom_fee_percent') }}" placeholder="افتراضي: {{ $settings['exchange_fee_percent']->value ?? '0.25' }}%" 
                           class="w-full px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 num-font focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-1">
                <!-- Buy Rate -->
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">سعر الشراء (Buy Rate)</label>
                    <input type="number" step="0.000001" min="0" name="buy_rate" value="{{ old('buy_rate') }}" placeholder="سعر الشراء الرقابي" 
                           class="w-full px-3 py-2 text-xs font-medium bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 num-font focus:bg-white focus:outline-none transition">
                </div>

                <!-- Sell Rate -->
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">سعر البيع (Sell Rate)</label>
                    <input type="number" step="0.000001" min="0" name="sell_rate" value="{{ old('sell_rate') }}" placeholder="سعر البيع الرقابي" 
                           class="w-full px-3 py-2 text-xs font-medium bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 num-font focus:bg-white focus:outline-none transition">
                </div>

                <!-- Min Exchange Amount -->
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">الحد الأدنى لمصارفة هذا الزوج</label>
                    <input type="number" step="1" min="0" name="min_exchange_amount" value="{{ old('min_exchange_amount') }}" placeholder="مثال: 5" 
                           class="w-full px-3 py-2 text-xs font-medium bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 num-font focus:bg-white focus:outline-none transition">
                </div>

                <!-- Max Exchange Amount -->
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">الحد الأقصى لمصارفة هذا الزوج</label>
                    <input type="number" step="1" min="0" name="max_exchange_amount" value="{{ old('max_exchange_amount') }}" placeholder="مثال: 50000" 
                           class="w-full px-3 py-2 text-xs font-medium bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 num-font focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-6 rounded-xl text-xs transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>حفظ وتفعيل زوج الصرف فوراً</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Section 2: Active Exchange Rates Matrix Table & Batch Update Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">سجل أزواج الصرف المتاحة في المنظومة (Active Currency Pairs)</h3>
                    <p class="text-[11px] text-slate-400">يمكنك تعديل الأسعار والعمولات المخصصة مباشرة من الجدول أو حذف أي زوج</p>
                </div>
            </div>
            <span class="num-font text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-200/60">{{ $rates->count() }} زوج متاح</span>
        </div>

        <form action="{{ route('admin.settings.rates') }}" method="POST">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">زوج العملات</th>
                            <th class="py-3 px-4">سعر الصرف (السوق)</th>
                            <th class="py-3 px-4">سعر الشراء</th>
                            <th class="py-3 px-4">سعر البيع</th>
                            <th class="py-3 px-4">عمولة الزوج (%)</th>
                            <th class="py-3 px-4 text-center">التفعيل</th>
                            <th class="py-3 px-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rates as $index => $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <input type="hidden" name="rates[{{ $index }}][id]" value="{{ $r->id }}">
                            
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800 border border-teal-200/60 font-mono text-xs font-bold">{{ $r->from_currency }}</span>
                                    <span class="text-slate-400 font-bold">&rarr;</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 border border-slate-200/60 font-mono text-xs font-bold">{{ $r->to_currency }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <input type="number" step="0.000001" name="rates[{{ $index }}][rate]" value="{{ old('rates.'.$index.'.rate', $r->rate) }}" required
                                       class="w-28 px-2.5 py-1.5 text-xs font-bold bg-slate-50 border rounded-lg text-slate-900 num-font focus:bg-white focus:outline-none transition border-slate-200">
                            </td>

                            <td class="py-3.5 px-4">
                                <input type="number" step="0.000001" name="rates[{{ $index }}][buy_rate]" value="{{ old('rates.'.$index.'.buy_rate', $r->buy_rate) }}"
                                       class="w-28 px-2.5 py-1.5 text-xs font-medium bg-slate-50 border rounded-lg text-slate-700 num-font focus:bg-white focus:outline-none transition border-slate-200">
                            </td>

                            <td class="py-3.5 px-4">
                                <input type="number" step="0.000001" name="rates[{{ $index }}][sell_rate]" value="{{ old('rates.'.$index.'.sell_rate', $r->sell_rate) }}"
                                       class="w-28 px-2.5 py-1.5 text-xs font-medium bg-slate-50 border rounded-lg text-slate-700 num-font focus:bg-white focus:outline-none transition border-slate-200">
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <input type="number" step="0.01" min="0" max="100" name="rates[{{ $index }}][custom_fee_percent]" value="{{ old('rates.'.$index.'.custom_fee_percent', $r->custom_fee_percent) }}" placeholder="عام ({{ $settings['exchange_fee_percent']->value ?? '0.25' }}%)"
                                           class="w-24 px-2 py-1 text-xs font-bold bg-slate-50 border rounded-lg text-slate-900 num-font focus:bg-white focus:outline-none transition border-slate-200">
                                    <span class="text-[10px] text-slate-400 font-bold">%</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <input type="hidden" name="rates[{{ $index }}][is_active]" value="0">
                                <input type="checkbox" name="rates[{{ $index }}][is_active]" value="1" {{ $r->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="if(confirm('تأكيد حذف زوج الصرف ({{ $r->from_currency }} -> {{ $r->to_currency }}) نهائياً؟')) { document.getElementById('delete-rate-{{ $r->id }}').submit(); }"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="حذف هذا الزوج">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">لا توجد أزواج عملات مسجلة بعد. يمكنك إضافة أي زوج عملات تريده من النموذج بالأعلى!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rates->count() > 0)
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <span class="text-[11px] text-slate-400">أي تعديل في الأسعار والعمولات يظهر فورياً في تطبيق العميل دون الحاجة لإعادة تشغيل النظام.</span>
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-6 rounded-xl text-xs transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span>حفظ كافة التعديلات في جدول الأسعار</span>
                </button>
            </div>
            @endif
        </form>
    </div>

    <!-- Hidden Delete Forms -->
    @foreach($rates as $r)
    <form id="delete-rate-{{ $r->id }}" action="{{ route('admin.settings.rates.delete', $r->id) }}" method="POST" class="hidden">
        @csrf
    </form>
    @endforeach

    <!-- Section 3: Central Fees & Operating Limits -->
    <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft space-y-5">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">السياسات المالية العامة والعمولات المركزية للمنظومة</h3>
                    <p class="text-[11px] text-slate-400">تحديد نسب الرسوم التلقائية على التحويلات والسحب وحصص الوكلاء</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.system') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Transfer Fee Card -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-3">
                    <span class="text-xs font-bold text-slate-800 block">رسوم التحويل بين العملاء (P2P Transfers)</span>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">النسبة المئوية (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="transfer_fee_percent" 
                                   value="{{ old('transfer_fee_percent', $settings['transfer_fee_percent']->value ?? '0.5') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg text-slate-900 num-font focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">مبلغ رسم ثابت إضافي</label>
                            <input type="number" step="0.01" min="0" name="transfer_fee_fixed" 
                                   value="{{ old('transfer_fee_fixed', $settings['transfer_fee_fixed']->value ?? '0.00') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg text-slate-900 num-font focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Cash-out Fee & Agent Commission Share -->
                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-200/70 space-y-3">
                    <span class="text-xs font-bold text-emerald-900 block">السحب النقدي وأرباح الوكيل (Cash-Out)</span>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-emerald-800 mb-1">رسوم السحب المفروضة على العميل (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="withdrawal_fee_percent" 
                                   value="{{ old('withdrawal_fee_percent', $settings['withdrawal_fee_percent']->value ?? '1.0') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-emerald-200 rounded-lg text-emerald-900 num-font focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-emerald-800 mb-1">حصة الوكيل من الرسم (أرباح فورية %)</label>
                            <input type="number" step="0.01" min="0" max="100" name="agent_commission_percent" 
                                   value="{{ old('agent_commission_percent', $settings['agent_commission_percent']->value ?? '60.0') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-emerald-300 rounded-lg text-emerald-950 num-font focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Default Exchange Fee & Limits -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-3">
                    <span class="text-xs font-bold text-slate-800 block">عمولة الصرف العامة والأسقف</span>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">العمولة العامة لصرف العملات (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="exchange_fee_percent" 
                                   value="{{ old('exchange_fee_percent', $settings['exchange_fee_percent']->value ?? '0.25') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg text-slate-900 num-font focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">السقف اليومي التراكمي للعميل</label>
                            <input type="number" step="1" min="1" name="daily_transfer_limit" 
                                   value="{{ old('daily_transfer_limit', $settings['daily_transfer_limit']->value ?? '20000000') }}" 
                                   class="w-full px-3 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg text-slate-900 num-font focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-6 rounded-xl text-xs transition shadow-xs">
                    حفظ السياسات والرسوم المركزية
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
