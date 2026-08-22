@extends('layouts.admin')

@section('title', 'إدارة المستخدمين والعملاء')
@section('page_title', 'دليل حسابات العملاء والمحافظ')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Status Segmented Control Tabs -->
        <div class="flex items-center gap-1.5 flex-wrap w-full sm:w-auto">
            <a href="{{ route('admin.users') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !$status ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                جميع الحسابات
            </a>
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-amber-50 text-amber-800 border border-amber-200/80 shadow-xs' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50/50' }}">
                <span>بانتظار الموافقة</span>
                @if($pendingCount > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-[10px] bg-amber-200/80 px-1.5 py-0.2 rounded-full font-extrabold">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users', ['status' => 'active']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'active' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/50' }}">
                مفعّل ونشط
            </a>
            <a href="{{ route('admin.users', ['status' => 'suspended']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'suspended' ? 'bg-rose-50 text-rose-800 border border-rose-200/80 shadow-xs' : 'text-slate-600 hover:text-rose-800 hover:bg-rose-50/50' }}">
                حسابات معلقة
            </a>
        </div>

        <!-- Search Box -->
        <form method="GET" action="{{ route('admin.users') }}" class="w-full sm:w-72">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالاسم، رقم الهاتف أو البريد..." 
                       class="w-full pl-3 pr-9 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
            </div>
        </form>

    </div>

    <!-- Users Ledger Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">بيانات العميل</th>
                        <th class="py-4 px-6">رقم الهاتف</th>
                        <th class="py-4 px-6">أرصدة العملات في المحفظة</th>
                        <th class="py-4 px-6">حالة الحساب</th>
                        <th class="py-4 px-6">تاريخ التسجيل</th>
                        <th class="py-4 px-6 text-center">إجراءات الرقابة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">{{ $user->full_name }}</div>
                            <div class="text-[11px] text-slate-400 font-medium">{{ $user->email ?? 'لا يوجد بريد' }}</div>
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-800 num-font" dir="ltr">
                            {{ $user->phone }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-lg bg-teal-50 text-teal-800 border border-teal-200/60 font-mono text-[11px] font-bold">
                                    {{ number_format($user->getCurrencyBalance('YER'), 0) }} <span class="text-[9px] font-sans">ر.ي</span>
                                </span>
                                @if($user->getCurrencyBalance('SAR') > 0)
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-mono text-[11px] font-bold">
                                    {{ number_format($user->getCurrencyBalance('SAR'), 2) }} <span class="text-[9px] font-sans">SAR</span>
                                </span>
                                @endif
                                @if($user->getCurrencyBalance('USD') > 0)
                                <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200/60 font-mono text-[11px] font-bold">
                                    {{ number_format($user->getCurrencyBalance('USD'), 2) }} <span class="text-[9px] font-sans">$</span>
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    مفعّل
                                </span>
                            @elseif($user->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    قيد المراجعة
                                </span>
                            @elseif($user->status === 'suspended')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    معلّق
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    مرفوض
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 num-font text-slate-400">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="text-teal-700 hover:bg-teal-50 font-semibold text-[11px] px-2.5 py-1.5 rounded-xl transition border border-teal-200/80 shadow-xs">
                                    كشف الحساب
                                </a>

                                @if($user->status === 'pending')
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] px-2.5 py-1.5 rounded-xl transition shadow-xs">
                                            موافقة
                                        </button>
                                    </form>
                                @elseif($user->status === 'active')
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" onclick="return confirm('تأكيد تعليق حساب العميل؟')" class="text-rose-600 hover:bg-rose-50 font-semibold text-[11px] px-2.5 py-1.5 rounded-xl transition border border-rose-200/80">
                                            تعليق
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">لا يوجد مستخدمون مطابقون لمعايير البحث الحالية.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
