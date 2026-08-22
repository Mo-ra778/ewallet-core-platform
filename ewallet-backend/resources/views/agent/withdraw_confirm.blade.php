@extends('layouts.agent')

@section('title', 'تأكيد السحب النقدي بالـ OTP — الخطوة 2')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Step 2 Confirmation Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">تأكيد عملية السحب النقدي (Cash-Out Step 2)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">إدخال رمز التحقق المستلم من العميل لإتمام العملية</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-900 border border-teal-300/80">
                الخطوة 2 من 2
            </span>
        </div>

        <!-- Transaction Details Card -->
        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 space-y-3 text-xs">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">اسم العميل المستفيد:</span>
                <span class="font-bold text-slate-900">{{ $user->full_name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">رقم هاتف العميل:</span>
                <span class="num-font font-bold text-slate-800" dir="ltr">{{ $user->phone }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-slate-200/80 pt-3">
                <span class="text-slate-700 font-bold">المبلغ المطلوب تسليمه نقداً:</span>
                <span class="text-base font-extrabold text-teal-700 num-font">{{ number_format($amount, 2) }} {{ $currency ?? 'SAR' }}</span>
            </div>
        </div>

        @if(isset($demo_otp))
        <div class="bg-slate-50 border border-slate-200/80 text-slate-700 rounded-xl p-3 text-xs flex items-center justify-between">
            <span class="text-[11px] text-slate-500">تم إرسال كود الـ OTP إلى هاتف العميل.</span>
            <span class="num-font font-bold text-xs bg-white text-slate-900 px-3 py-1 rounded-lg border border-slate-200 shadow-xs">OTP: {{ $demo_otp }}</span>
        </div>
        @endif

        <form action="{{ route('agent.withdraw.confirm') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <!-- OTP Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2 text-center">أدخل رمز التحقق (OTP) المكون من 6 أرقام</label>
                <input type="text" name="otp" maxlength="6" autofocus required placeholder="••••••"
                       class="w-full text-center tracking-[0.6em] text-2xl font-bold num-font py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <p class="text-[11px] text-center text-slate-400 mt-1.5 font-medium">صلاحية الرمز 5 دقائق فقط</p>
            </div>

            <button type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>تأكيد السحب وتسليم النقد للعميل</span>
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('agent.withdraw.form') }}" class="text-xs text-slate-400 hover:text-slate-700 font-medium transition">
                إلغاء العملية والعودة &larr;
            </a>
        </div>
    </div>

</div>
@endsection
