@extends('layouts.agent')

@section('title', 'تأكيد السحب النقدي بالـ OTP — الخطوة 2')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <div class="bg-surface-card p-6 sm:p-7 rounded-lg border border-surface-border space-y-5">
        <div class="pb-4 border-b border-surface-border flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold text-ink-primary">تأكيد عملية السحب النقدي (Cash-Out Step 2)</h2>
                <p class="text-[11px] text-ink-muted mt-0.5">إدخال رمز التحقق المستلم من العميل لإتمام العملية</p>
            </div>
            <span class="text-[10px] font-mono text-fin-teal bg-fin-tealBg border border-fin-tealBorder px-2 py-0.5 rounded font-bold">الخطوة 2 من 2</span>
        </div>

        <!-- Transaction Details Card -->
        <div class="bg-surface-base border border-surface-border rounded p-4 space-y-2 text-xs">
            <div class="flex justify-between items-center">
                <span class="text-ink-muted font-medium">اسم العميل:</span>
                <span class="font-semibold text-ink-primary">{{ $user->full_name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-ink-muted font-medium">رقم الهاتف:</span>
                <span class="num-font text-slate-800" dir="ltr">{{ $user->phone }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-surface-border pt-2">
                <span class="text-ink-secondary font-semibold">المبلغ المطلوب تسليمه نقداً:</span>
                <span class="text-sm font-bold text-ink-primary num-font">{{ number_format($amount, 2) }} {{ $currency ?? 'SAR' }}</span>
            </div>
        </div>

        @if(isset($demo_otp))
        <div class="bg-surface-subtle border border-surface-border text-ink-secondary rounded p-3 text-xs flex items-center justify-between">
            <span class="text-[11px]">تم إرسال كود الـ OTP إلى هاتف العميل.</span>
            <span class="num-font font-bold text-xs bg-white text-slate-900 px-2 py-0.5 rounded border border-surface-border">OTP: {{ $demo_otp }}</span>
        </div>
        @endif

        <form action="{{ route('agent.withdraw.confirm') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <!-- OTP Input -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-2 text-center">أدخل رمز التحقق (OTP) المكون من 6 أرقام</label>
                <input type="text" name="otp" maxlength="6" autofocus required placeholder="••••••"
                       class="w-full text-center tracking-[0.5em] text-lg font-bold num-font py-2.5 bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                <p class="text-[10px] text-center text-ink-muted mt-1">صلاحية الرمز 5 دقائق فقط</p>
            </div>

            <button type="submit"
                    class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2.5 px-4 rounded text-xs transition">
                تأكيد السحب وتسليم النقد للعميل
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('agent.withdraw.form') }}" class="text-xs text-ink-muted hover:text-ink-primary transition">
                إلغاء العملية والعودة
            </a>
        </div>
    </div>

</div>
@endsection
