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

        <!-- Transaction Breakdown Card -->
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
                <span class="text-base font-extrabold text-teal-700 num-font">{{ number_format($amount, 2) }} {{ $currency ?? 'YER' }}</span>
            </div>
            @if(isset($fee) && $fee > 0)
            <div class="flex justify-between items-center text-slate-500">
                <span>رسوم خدمة السحب (المقتطعة من العميل):</span>
                <span class="num-font font-bold text-slate-700">{{ number_format($fee, 2) }} {{ $currency }}</span>
            </div>
            <div class="flex justify-between items-center text-slate-500">
                <span>إجمالي الخصم من محفظة العميل:</span>
                <span class="num-font font-bold text-slate-900">{{ number_format($total_debit ?? ($amount + $fee), 2) }} {{ $currency }}</span>
            </div>
            @endif
            @if(isset($agent_commission) && $agent_commission > 0)
            <div class="flex justify-between items-center pt-2 border-t border-slate-200/60 text-emerald-800 bg-emerald-50/60 p-2.5 rounded-xl">
                <span class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    أرباح عمولتك كوكيل من هذه الحركة:
                </span>
                <span class="num-font font-extrabold text-sm text-emerald-700">+ {{ number_format($agent_commission, 2) }} {{ $currency }}</span>
            </div>
            @endif
        </div>

        <div class="bg-amber-50/80 border border-amber-200/80 text-amber-900 rounded-xl p-3.5 text-xs flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            </div>
            <div>
                <span class="font-bold block text-slate-900 text-xs">تم إرسال رمز التحقق الأمني (OTP) إلى هاتف العميل</span>
                <span class="text-[11px] text-slate-500">لأسباب أمنية، لا يظهر الرمز للوكيل. يرجى طلب الرمز من العميل بعد تسليمه النقدية لإتمام العملية.</span>
            </div>
        </div>

        <form action="{{ route('agent.withdraw.confirm') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <!-- OTP Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2 text-center">أدخل رمز التحقق (OTP) المكون من 6 أرقام <span class="text-rose-500">*</span></label>
                <input type="text" name="otp" maxlength="6" autofocus required placeholder="••••••"
                       class="w-full text-center tracking-[0.6em] text-2xl font-bold num-font py-3 bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition shadow-xs @error('otp') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                
                @error('otp')
                    <p class="text-[11px] text-rose-600 font-semibold mt-2 text-center flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
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
