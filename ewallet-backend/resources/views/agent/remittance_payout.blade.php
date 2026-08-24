@extends('layouts.agent')

@section('title', 'صرف حوالة نقدية')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header & Step 1: Search Remittance Form -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">محطة صرف الحوالات النقدية (Cash Remittance Payout)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">صرف الحوالات وتسليم النقد للمستفيدين بالهوية والكود السري</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                تسليم فوري
            </span>
        </div>

        <!-- Search Form -->
        <form action="{{ route('agent.remittance.form') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم الحوالة (Remittance Code) <span class="text-rose-500">*</span></label>
                    <input type="text" name="remittance_code" value="{{ request('remittance_code') }}" required placeholder="مثال: REM12345678" dir="ltr"
                           class="w-full px-3.5 py-2.5 text-xs num-font font-bold uppercase bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition @error('remittance_code') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 @enderror">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">الكود السري للصرف (PIN) <span class="text-rose-500">*</span></label>
                    <input type="password" name="pin_code" value="{{ request('pin_code') }}" required placeholder="••••" maxlength="6" dir="ltr"
                           class="w-full px-3.5 py-2.5 text-xs num-font font-bold text-center tracking-widest bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition border-slate-200/80 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>
            </div>

            @error('remittance_code')
                <p class="text-[11px] text-rose-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror

            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <span>البحث عن الحوالة والتحقق</span>
            </button>
        </form>
    </div>

    @if($remittance)
    <!-- Step 2: Remittance Details & Identity Confirmation Form -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border-2 border-amber-300 shadow-md space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider bg-amber-50 px-2 py-0.5 rounded-md">حوالة جاهزة للصرف</span>
                <h4 class="text-base font-extrabold text-slate-900 mt-1">بيانات الحوالة النقدية #{{ $remittance->remittance_code }}</h4>
            </div>
            <div class="text-left">
                <span class="text-[10px] text-slate-400 block font-medium">مبلغ التسليم الصافي</span>
                <span class="text-xl font-black text-emerald-700 num-font">{{ number_format($remittance->amount, 2) }} {{ $remittance->currency }}</span>
            </div>
        </div>

        <!-- Remittance Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                <span class="text-[10px] text-slate-400 font-semibold block">اسم المستلم الرباعي:</span>
                <strong class="text-slate-900 text-sm block">{{ $remittance->recipient_name }}</strong>
                <span class="text-slate-500 num-font block mt-0.5">هاتف: {{ $remittance->recipient_phone }}</span>
            </div>

            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 space-y-1">
                <span class="text-[10px] text-slate-400 font-semibold block">بيانات المرسل:</span>
                <strong class="text-slate-900 text-sm block">{{ $remittance->sender_name }}</strong>
                <span class="text-slate-500 num-font block mt-0.5">هاتف: {{ $remittance->sender_phone }}</span>
            </div>
        </div>

        <!-- Commission Banner for Agent -->
        <div class="bg-emerald-50 border border-emerald-200/80 p-3.5 rounded-xl flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span class="text-emerald-900 font-bold">عمولة الوكيل المستحقة عند الصرف:</span>
            </div>
            <span class="text-emerald-700 font-extrabold num-font text-sm">+{{ number_format($remittance->agent_commission, 2) }} {{ $remittance->currency }}</span>
        </div>

        <!-- Payout Execution Form -->
        <form action="{{ route('agent.remittance.payout') }}" method="POST" class="space-y-4 pt-2">
            @csrf
            <input type="hidden" name="remittance_id" value="{{ $remittance->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">نوع وثيقة إثبات الهوية <span class="text-rose-500">*</span></label>
                    <select name="recipient_id_type" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        <option value="بطاقة شخصية / رقم قومي">بطاقة شخصية / رقم قومي</option>
                        <option value="جواز سفر">جواز سفر ساري</option>
                        <option value="رخصة قيادة">رخصة قيادة</option>
                        <option value="بطاقة عائلية">بطاقة عائلية</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم وثيقة الهوية <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient_id_number" required placeholder="أدخل رقم الهوية للمستلم"
                           class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" onclick="return confirm('هل تأكدت من مطابقة هوية المستلم وجاهز لتسليم المبلغ نقداً؟');"
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <span>تأكيد صرف الحوالة وتسليم النقد ({{ number_format($remittance->amount, 2) }} {{ $remittance->currency }})</span>
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection
