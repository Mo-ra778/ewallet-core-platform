@extends('layouts.agent')

@section('title', 'إيداع نقدي للعميل')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Cash-In Terminal Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">محطة الإيداع النقدي (Cash-In Terminal)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">تغذية رصيد محفظة العميل فوراً بالعملة المحددة</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                تنفيذ فوري
            </span>
        </div>

        <!-- Multi-Currency Available Liquidity Box -->
        <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl space-y-2">
            <span class="text-xs font-bold text-slate-500 block">رصيد العهدة المتاح لك للتسليم والإيداع:</span>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">يمني (YER)</span>
                    <strong class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('YER'), 0) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">سعودي (SAR)</span>
                    <strong class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('SAR'), 2) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">دولار (USD)</span>
                    <strong class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('USD'), 2) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">يورو (EUR)</span>
                    <strong class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('EUR'), 2) }}</strong>
                </div>
            </div>
        </div>

        <form action="{{ route('agent.deposit') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف العميل المسجل بالمحفظة <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                       class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition text-right @error('phone') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                @error('phone')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المراد إيداعه <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2 text-base font-extrabold num-font bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition @error('amount') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                    @error('amount')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" required class="w-full px-3 py-2.5 text-xs font-bold bg-slate-50 border rounded-xl text-slate-800 focus:bg-white focus:outline-none transition @error('currency') border-rose-400 ring-2 ring-rose-500/20 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                        <option value="YER" {{ old('currency') === 'YER' ? 'selected' : '' }}>YER - يمني</option>
                        <option value="SAR" {{ old('currency') === 'SAR' ? 'selected' : '' }}>SAR - سعودي</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - دولار</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                    </select>
                    @error('currency')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">ملاحظة أو رقم سند الاستلام النقدي (اختياري)</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="مثال: سند قبض كاش رقم 450"
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
            </div>

            <button type="submit" onclick="return confirm('تأكيد خصم المبلغ من رصيد الوكيل بالعملة المختارة وإيداعه للعميل فوراً؟')"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>تأكيد الإيداع الفوري للعميل</span>
            </button>
        </form>
    </div>

</div>
@endsection
