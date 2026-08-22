@extends('layouts.agent')

@section('title', 'طلب سحب نقدي — الخطوة 1')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <div class="bg-surface-card p-6 sm:p-7 rounded-lg border border-surface-border space-y-5">
        <div class="pb-4 border-b border-surface-border flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold text-ink-primary">طلب سحب نقدي (Cash-Out Step 1)</h2>
                <p class="text-[11px] text-ink-muted mt-0.5">توليد كود التحقق الآمن (OTP) وإرساله لهاتف العميل</p>
            </div>
            <span class="text-[10px] font-mono text-fin-amber bg-fin-amberBg border border-fin-amberBorder px-2 py-0.5 rounded font-bold">الخطوة 1 من 2</span>
        </div>

        <div class="bg-surface-base border border-surface-border rounded p-3 text-xs text-ink-secondary space-y-1">
            <span class="font-semibold text-ink-primary block">آلية التحقق الآمن بخطوتين:</span>
            <p class="text-[11px] text-ink-muted leading-relaxed">1. إدخال رقم هاتف العميل والمبلغ المطلوب سحبه بالعملة المحددة.</p>
            <p class="text-[11px] text-ink-muted leading-relaxed">2. يقوم النظام بتوليد كود OTP صالح لمدة 5 دقائق وإرساله لتطبيق العميل.</p>
        </div>

        <form action="{{ route('agent.withdraw.otp') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1">رقم هاتف العميل المسجل</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                       class="w-full px-3 py-2 text-xs num-font bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition text-right">
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-ink-primary mb-1">المبلغ المراد سحبه</label>
                    <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                           class="w-full px-3 py-2 text-sm font-bold num-font bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">العملة</label>
                    <select name="currency" required class="w-full px-2.5 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                        <option value="SAR" {{ old('currency') === 'SAR' ? 'selected' : '' }}>SAR - سعودي</option>
                        <option value="YER" {{ old('currency') === 'YER' ? 'selected' : '' }}>YER - يمني</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - دولار</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2.5 px-4 rounded text-xs transition mt-2">
                توليد وإرسال كود التحقق (OTP) للعميل &larr;
            </button>
        </form>
    </div>

</div>
@endsection
