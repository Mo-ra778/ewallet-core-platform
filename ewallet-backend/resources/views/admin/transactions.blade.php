@extends('layouts.admin')

@section('title', 'السجل المالي الرقابي')
@section('page_title', 'السجل المالي والرقابي العام (General Ledger & Audit Trail)')

@section('content')
    <div class="space-y-6">

        <!-- Filters & Search Toolbar -->
        <div
            class="bg-surface-card p-3.5 rounded-lg border border-surface-border flex flex-col md:flex-row items-center justify-between gap-3">
            <!-- Type Segmented Tabs -->
            <div class="flex flex-wrap items-center gap-1 w-full md:w-auto">
                <a href="{{ route('admin.transactions') }}"
                    class="px-3 py-1.5 rounded text-xs font-semibold transition {{ !$type && !$currency ? 'bg-slate-900 text-white' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                    كافة العمليات
                </a>
                <a href="{{ route('admin.transactions', ['type' => 'deposit', 'currency' => $currency]) }}"
                    class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $type === 'deposit' ? 'bg-fin-tealBg text-fin-teal border border-fin-tealBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                    إيداعات
                </a>
                <a href="{{ route('admin.transactions', ['type' => 'withdraw', 'currency' => $currency]) }}"
                    class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $type === 'withdraw' ? 'bg-fin-amberBg text-fin-amber border border-fin-amberBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                    سحوبات نقدية
                </a>
                <a href="{{ route('admin.transactions', ['type' => 'transfer', 'currency' => $currency]) }}"
                    class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $type === 'transfer' ? 'bg-slate-900 text-white font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                    تحويلات
                </a>
            </div>

            <!-- Currency & Search Filter Group -->
            <form method="GET" action="{{ route('admin.transactions') }}"
                class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif

                <select name="currency" onchange="this.form.submit()"
                    class="px-2.5 py-1.5 text-xs font-semibold bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                    <option value="">جميع العملات</option>
                    <option value="SAR" {{ $currency === 'SAR' ? 'selected' : '' }}>SAR (سعودي)</option>
                    <option value="YER" {{ $currency === 'YER' ? 'selected' : '' }}>YER (يمني)</option>
                    <option value="USD" {{ $currency === 'USD' ? 'selected' : '' }}>USD (دولار)</option>
                    <option value="EUR" {{ $currency === 'EUR' ? 'selected' : '' }}>EUR (يورو)</option>
                </select>

                <div class="relative flex-1 md:w-56">
                    <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالبيان أو الهاتف..."
                        class="w-full pl-3 pr-8 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                    <svg class="w-3.5 h-3.5 text-ink-muted absolute right-2.5 top-2.5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- General Ledger Table -->
        <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                        <tr>
                            <th class="py-3 px-4">رقم الحركة (UUID)</th>
                            <th class="py-3 px-4">نوع الحركة</th>
                            <th class="py-3 px-4">المبلغ والعملة</th>
                            <th class="py-3 px-4">الأطراف المشاركة</th>
                            <th class="py-3 px-4">البيان والتفاصيل</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">التوقيت الدقيق</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-border">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-surface-base/80 transition">
                                <td class="py-3 px-4 font-mono text-[10px] text-ink-muted">{{ substr($tx->id, 0, 8) }}...</td>
                                <td class="py-3 px-4">
                                    @if($tx->type === 'deposit')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                            إيداع
                                        </span>
                                    @elseif($tx->type === 'withdraw')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                            سحب نقدي
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-subtle text-ink-primary border border-surface-border">
                                            تحويل
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                                    {{ number_format($tx->amount, 2) }} <span
                                        class="text-xs font-semibold text-emerald-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($tx->user)
                                        <div class="font-medium text-ink-primary">{{ $tx->user->full_name }}</div>
                                        <div class="text-[10px] text-ink-muted num-font" dir="ltr">{{ $tx->user->phone }}</div>
                                    @endif
                                    @if($tx->agent)
                                        <div class="text-[11px] text-fin-teal font-medium mt-0.5">الوكيل:
                                            {{ $tx->agent->full_name }}</div>
                                    @endif
                                    @if($tx->admin)
                                        <div class="text-[11px] text-ink-muted font-medium mt-0.5">إشراف إداري:
                                            {{ $tx->admin->username }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-ink-secondary max-w-sm">{{ $tx->description }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                        مكتمل
                                    </span>
                                </td>
                                <td class="py-3 px-4 num-font text-ink-muted">{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-ink-muted">لم يتم العثور على أي عمليات مسجلة في
                                    السجل العام.</td>
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