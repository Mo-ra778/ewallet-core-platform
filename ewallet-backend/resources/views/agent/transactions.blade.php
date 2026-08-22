@extends('layouts.agent')

@section('title', 'سجل عمليات المركز')

@section('content')
<div class="space-y-6">

    <!-- Filters & Ledger Toolbar -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-slate-400 ml-2">تصفية حسب العملة:</span>
            <a href="{{ route('agent.transactions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !$currency ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                الكل
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'SAR']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currency === 'SAR' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                SAR (سعودي)
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'YER']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currency === 'YER' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/50' }}">
                YER (يمني)
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'USD']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currency === 'USD' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                USD (دولار)
            </a>
        </div>
        <span class="text-xs font-mono text-slate-400 font-semibold">{{ $transactions->total() }} حركة مسجلة</span>
    </div>

    <!-- Agent General Ledger Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-900">سجل العمليات المالية المنفذة في هذا المركز</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">تفاصيل عمليات الإيداع والتسليم النقدي للعملاء</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">نوع الحركة</th>
                        <th class="py-4 px-6">المبلغ والعملة</th>
                        <th class="py-4 px-6">بيانات العميل</th>
                        <th class="py-4 px-6">البيان والوصف</th>
                        <th class="py-4 px-6">الحالة</th>
                        <th class="py-4 px-6">التاريخ والتوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    إيداع نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    سحب نقدي
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->user)
                                <div class="font-bold text-slate-900">{{ $tx->user->full_name }}</div>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                مكتمل
                            </span>
                        </td>
                        <td class="py-4 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">لا توجد عمليات منفذة في السجل حتى الآن.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
