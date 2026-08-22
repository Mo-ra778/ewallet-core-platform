@extends('layouts.admin')

@section('title', 'سجل المستخدمين والمحافظ')
@section('page_title', 'سجل حسابات ومحافظ العملاء')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar -->
    <div class="bg-surface-card p-3.5 rounded-lg border border-surface-border flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Status Filter Segmented Controls -->
        <div class="flex flex-wrap items-center gap-1 w-full md:w-auto">
            <a href="{{ route('admin.users') }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ !$status ? 'bg-slate-900 text-white' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                كافة الحسابات
            </a>
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $status === 'pending' ? 'bg-fin-amberBg text-fin-amber border border-fin-amberBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                طلبات معلقة
            </a>
            <a href="{{ route('admin.users', ['status' => 'active']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $status === 'active' ? 'bg-fin-tealBg text-fin-teal border border-fin-tealBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                حسابات نشطة
            </a>
            <a href="{{ route('admin.users', ['status' => 'suspended']) }}" class="px-3 py-1.5 rounded text-xs font-semibold transition {{ $status === 'suspended' ? 'bg-fin-crimsonBg text-fin-crimson border border-fin-crimsonBorder font-bold' : 'text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle' }}">
                حسابات معلّقة
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.users') }}" class="w-full md:w-64">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالاسم أو رقم الهاتف..."
                       class="w-full pl-3 pr-8 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary placeholder:text-ink-muted focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                <svg class="w-3.5 h-3.5 text-ink-muted absolute right-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </div>
        </form>
    </div>

    <!-- Users Ledger -->
    <div class="bg-surface-card rounded-lg border border-surface-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                    <tr>
                        <th class="py-3 px-4">اسم العميل</th>
                        <th class="py-3 px-4">رقم الهاتف</th>
                        <th class="py-3 px-4">الرصيد المتاح</th>
                        <th class="py-3 px-4">حالة الحساب</th>
                        <th class="py-3 px-4">تاريخ التسجيل</th>
                        <th class="py-3 px-4 text-center">الرقابة والتحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @forelse($users as $user)
                    <tr class="hover:bg-surface-base/80 transition">
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="font-semibold text-ink-primary hover:text-emerald-700 block">
                                {{ $user->full_name }}
                            </a>
                            <div class="text-[10px] text-ink-muted font-mono">{{ $user->email ?? '—' }}</div>
                        </td>
                        <td class="py-3 px-4 num-font font-medium text-slate-800" dir="ltr">{{ $user->phone }}</td>
                        <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                            {{ number_format($user->balance, 2) }} <span class="text-[10px] font-normal text-ink-muted">ر.ي</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                    <span class="w-1.5 h-1.5 rounded-full bg-fin-teal"></span>
                                    نشط ومفعّل
                                </span>
                            @elseif($user->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                    <span class="w-1.5 h-1.5 rounded-full bg-fin-amber"></span>
                                    قيد المراجعة
                                </span>
                            @elseif($user->status === 'suspended')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-fin-crimsonBg text-fin-crimson border border-fin-crimsonBorder">
                                    <span class="w-1.5 h-1.5 rounded-full bg-fin-crimson"></span>
                                    معلّق
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface-subtle text-ink-muted border border-surface-border">
                                    مرفوض
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 num-font text-ink-muted">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-medium text-ink-secondary hover:text-ink-primary bg-surface-card hover:bg-surface-subtle border border-surface-border px-2.5 py-1 rounded transition">
                                    كشف الحساب
                                </a>

                                @if($user->status === 'pending')
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-slate-900 hover:bg-black text-white px-2.5 py-1 rounded text-xs font-medium transition">
                                            موافقة
                                        </button>
                                    </form>
                                @elseif($user->status === 'active')
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" onclick="return confirm('تأكيد تعليق هذا الحساب؟')" class="text-fin-crimson hover:bg-fin-crimsonBg border border-surface-border hover:border-fin-crimsonBorder px-2.5 py-1 rounded text-xs font-medium transition">
                                            تعليق
                                        </button>
                                    </form>
                                @elseif($user->status === 'suspended')
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-slate-900 hover:bg-black text-white px-2.5 py-1 rounded text-xs font-medium transition">
                                            إلغاء التعليق
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-muted">لم يتم العثور على أي حسابات مطابقة لمعايير البحث.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-3.5 border-t border-surface-border">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
