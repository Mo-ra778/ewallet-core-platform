@extends('layouts.agent')

@section('title', 'إيداع نقدي للعميل')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <div class="bg-surface-card p-6 sm:p-7 rounded-lg border border-surface-border space-y-5">
        <div class="pb-4 border-b border-surface-border">
            <h2 class="text-xs font-bold text-ink-primary">إيداع نقدي في محفظة العميل (Cash-In Terminal)</h2>
            <p class="text-[11px] text-ink-muted mt-0.5">تحويل رصيد فوري من حساب الوكيل إلى محفظة العميل المسجل</p>
        </div>

        <form action="{{ route('agent.deposit') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1">رقم هاتف العميل المسجل بالمحفظة</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                       class="w-full px-3 py-2 text-xs num-font bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition text-right">
            </div>

            <!-- Amount and Currency Selector Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-ink-primary mb-1">المبلغ المراد إيداعه</label>
                    <input type="number" step="0.01" min="1" max="{{ $agent->balance }}" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
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

            <div class="text-[11px] text-ink-secondary bg-surface-base p-3 rounded border border-surface-border flex items-center justify-between">
                <span>الرصيد المتاح بعهدة الوكيل:</span>
                <strong class="text-ink-primary num-font text-xs">{{ number_format($agent->balance, 2) }} ر.ي</strong>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1">ملاحظة أو رقم سند الاستلام (اختياري)</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="مثال: سند استلام نقدي رقم 450"
                       class="w-full px-3 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
            </div>

            <button type="submit" onclick="return confirm('تأكيد خصم المبلغ من رصيد الوكيل وإيداعه للعميل فوراً؟')"
                    class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2.5 px-4 rounded text-xs transition mt-2">
                تأكيد الإيداع الفوري للعميل
            </button>
        </form>
    </div>

</div>
@endsection
