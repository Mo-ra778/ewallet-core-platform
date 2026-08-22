@extends('layouts.agent')

@section('title', 'طلب سحب نقدي — الخطوة 1')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Step 1 Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">طلب سحب نقدي (Cash-Out Step 1)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">توليد وإرسال رمز التحقق الآمن (OTP) لتطبيق العميل</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-300/80">
                الخطوة 1 من 2
            </span>
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs text-slate-600 space-y-1.5">
            <span class="font-bold text-slate-800 block">بروتوكول السحب الآمن بخطوتين:</span>
            <p class="text-[11px] text-slate-500 leading-relaxed">&bull; أدخل رقم هاتف العميل والمبلغ المطلوب تسليمه كاش.</p>
            <p class="text-[11px] text-slate-500 leading-relaxed">&bull; يقوم النظام بفحص رصيد العميل وتوليد رمز OTP صالح لمدة 5 دقائق يصله على تطبيقه.</p>
        </div>

        <form action="{{ route('agent.withdraw.otp') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف العميل المسجل</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                       class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition text-right">
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المراد سحبه</label>
                    <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2 text-base font-extrabold num-font bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">العملة</label>
                    <select name="currency" required class="w-full px-3 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="SAR" {{ old('currency') === 'SAR' ? 'selected' : '' }}>SAR - سعودي</option>
                        <option value="YER" {{ old('currency') === 'YER' ? 'selected' : '' }}>YER - يمني</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - دولار</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <span>توليد وإرسال كود التحقق (OTP) للعميل</span>
                <span>&larr;</span>
            </button>
        </form>
    </div>

</div>
@endsection
