@extends('layouts.admin')

@section('title', 'إدارة حسابات المستخدمين')
@section('page_title', 'سجل الحسابات والرقابة على التسجيل')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar Card -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Status Filter Segmented Pills -->
        <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
            <span class="text-xs font-bold text-slate-400 ml-2">تصفية الحالة:</span>
            
            <a href="{{ route('admin.users') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !$status ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                الكل
            </a>

            <a href="{{ route('admin.users', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300/80 shadow-xs' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50/50' }}">
                <span>معلقة</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('admin.users', ['status' => 'active']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'active' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/50' }}">
                نشطة
            </a>

            <a href="{{ route('admin.users', ['status' => 'suspended']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'suspended' ? 'bg-rose-50 text-rose-800 border border-rose-200/80 shadow-xs' : 'text-slate-600 hover:text-rose-800 hover:bg-rose-50/50' }}">
                معلّقة
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.users') }}" class="w-full md:w-72">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالاسم أو الهاتف أو الإيميل..." 
                       class="w-full pl-3 pr-9 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition shadow-xs">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
            </div>
        </form>

    </div>

    <!-- Users Master Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-6">بيانات العميل</th>
                        <th class="py-4 px-6">رقم الجوال والبريد</th>
                        <th class="py-4 px-6">الرصيد المالي الحالي</th>
                        <th class="py-4 px-6">حالة الحساب</th>
                        <th class="py-4 px-6">تاريخ الانضمام</th>
                        <th class="py-4 px-6 text-center">التحكم والإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ mb_substr($u->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="font-bold text-slate-900 hover:text-teal-700 transition block">
                                        {{ $u->full_name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-mono" dir="ltr">ID: {{ substr($u->id, 0, 8) }}...</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-slate-800 num-font" dir="ltr">{{ $u->phone }}</div>
                            <div class="text-[10px] text-slate-400">{{ $u->email ?? '—' }}</div>
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($u->balance, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">ر.ي</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($u->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    مفعّل (Active)
                                </span>
                            @elseif($u->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    قيد المراجعة (Pending)
                                </span>
                            @elseif($u->status === 'suspended')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    معلّق (Suspended)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    مرفوض (Rejected)
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 num-font text-slate-400">
                            {{ $u->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.show', $u->id) }}" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" title="عرض كشف الحساب والملف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </a>

                                @if($u->status === 'pending')
                                    <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[10px] px-2.5 py-1 rounded-lg transition shadow-xs">
                                            موافقة
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد رفض الحساب؟')">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-rose-50 text-rose-700 border border-rose-200 font-semibold text-[10px] px-2.5 py-1 rounded-lg transition">
                                            رفض
                                        </button>
                                    </form>
                                @elseif($u->status === 'active')
                                    <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد تعليق الحساب مؤقتاً؟')">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" class="text-rose-600 hover:bg-rose-50 font-semibold text-[11px] px-2.5 py-1 rounded-lg transition border border-rose-200">
                                            تعليق
                                        </button>
                                    </form>
                                @elseif($u->status === 'suspended')
                                    <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[10px] px-2.5 py-1 rounded-lg transition shadow-xs">
                                            إعادة تفعيل
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">لا توجد حسابات مستخدمين مطابقة للبحث أو الفلتر المحدد.</td>
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
