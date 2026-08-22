@extends('layouts.agent')

@section('title', 'سجل عمليات المركز')

@section('content')
<div class="space-y-6">

    <!-- Filters & Ledger Toolbar -->
    <div class="bg-surface-card p-3.5 rounded-lg border border-surface-border flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-1">
            <span class="text-xs font-semibold text-ink-muted ml-2">تصفية حسب العملة:</span>
            <a href="{{ route('agent.transactions') }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ !$currency ? 'bg-slate-900 text-white' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                الكل
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'SAR']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $currency === 'SAR' ? 'bg-slate-900 text-white font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                SAR (سعودي)
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'YER']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $currency === 'YER' ? 'bg-fin-tealBg text-fin-teal border border-fin-tealBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                YER (يمني)
            </a>
            <a href="{{ route('agent.transactions', ['currency' => 'USD']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $currency === 'USD' ? 'bg-slate-900 text-white font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                USD (دولار)
            </a>
        </div>
        <span class="text-xs font-mono text-ink-muted font-semibold">{{ $transactions->total() }} حركة مسجلة</span>
    </div>

    <!-- Agent General Ledger Table -->
    <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-border">
            <h3 class="text-xs font-bold text-ink-primary">سجل العمليات المالية المنفذة في هذا المركز</h3>
            <p class="text-[11px] text-ink-muted mt-0.5">تفاصيل عمليات الإيداع والتسليم النقدي للعملاء</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                    <tr>
                        <th class="py-3 px-4">نوع الحركة</th>
                        <th class="py-3 px-4">المبلغ والعملة</th>
                        <th class="py-3 px-4">بيانات العميل</th>
                        <th class="py-3 px-4">البيان والوصف</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">التاريخ والتوقيت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-surface-base/80 transition">
                        <td class="py-3 px-4">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                    إيداع نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                    سحب نقدي
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-semibold text-emerald-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($tx->user)
                                <div class="font-medium text-ink-primary">{{ $tx->user->full_name }}</div>
                                <div class="text-[10px] text-ink-muted num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-ink-muted">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-ink-secondary max-w-sm">{{ $tx->description }}</td>
                        <td class="py-3 px-4">
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                مكتمل
                            </span>
                        </td>
                        <td class="py-3 px-4 num-font text-ink-muted">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-muted">لا توجد عمليات منفذة في السجل حتى الآن.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-3.5 border-t border-surface-border">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
