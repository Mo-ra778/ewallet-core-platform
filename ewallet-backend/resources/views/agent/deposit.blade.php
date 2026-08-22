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
                    <p class="text-xs text-slate-400 mt-0.5">تغذية رصيد محفظة العميل فوراً وخصمه من عهدة الوكيل</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                تنفيذ فوري
            </span>
        </div>

        <form action="{{ route('agent.deposit') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف العميل المسجل بالمحفظة</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                       class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition text-right">
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المراد إيداعه</label>
                    <input type="number" step="0.01" min="1" max="{{ $agent->balance }}" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
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

            <!-- Agent Balance Summary Box -->
            <div class="text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center justify-between">
                <span class="font-medium text-slate-500">رصيد العهدة النقدية المتاح للوكيل:</span>
                <strong class="text-slate-900 num-font text-sm">{{ number_format($agent->balance, 2) }} ر.ي</strong>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">ملاحظة أو رقم سند الاستلام النقدي (اختياري)</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="مثال: سند قبض كاش رقم 450"
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
            </div>

            <button type="submit" onclick="return confirm('تأكيد خصم المبلغ من رصيد الوكيل وإيداعه للعميل فوراً؟')"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>تأكيد الإيداع الفوري للعميل</span>
            </button>
        </form>
    </div>

</div>
@endsection
