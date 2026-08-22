@extends('layouts.admin')

@section('title', 'لوحة الرقابة والمؤشرات المالية')
@section('page_title', 'نظرة عامة على السيولة والعمليات')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Circulation Balance -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">إجمالي السيولة المتداولة</span>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">{{ number_format($totalSystemBalance ?? $totalSystemCirculation, 2) }}</span>
                <span class="text-xs font-semibold text-slate-400 font-sans">ر.ي</span>
            </div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-teal-700 font-semibold">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                <span>رصيد محفظات العملاء والوكلاء</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">الحسابات النشطة والمفعلة</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">{{ $activeUsers ?? $activeUsersCount }}</span>
                <span class="text-xs font-semibold text-slate-400 font-sans">عميل نشط</span>
            </div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                <span>من إجمالي {{ $totalUsers }} حساب مسجل</span>
            </div>
        </div>

        <!-- Pending Approval Queue -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">طلبات التسجيل المعلقة</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold {{ $pendingUsersCount > 0 ? 'text-amber-600' : 'text-slate-900' }} num-font tracking-tight">{{ $pendingUsersCount }}</span>
                <span class="text-xs font-semibold text-slate-400 font-sans">طلب بانتظار الاعتماد</span>
            </div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[11px] {{ $pendingUsersCount > 0 ? 'text-amber-700 font-bold' : 'text-slate-500' }}">
                <span>{{ $pendingUsersCount > 0 ? 'يتطلب فحص وموافقة الإدارة' : 'لا توجد طلبات معلقة' }}</span>
            </div>
        </div>

        <!-- Total Agents Network -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">شبكة الوكلاء المعتمدين</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900 num-font tracking-tight">{{ $totalAgents }}</span>
                <span class="text-xs font-semibold text-slate-400 font-sans">مركز صرافة</span>
            </div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-purple-700 font-semibold">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                <span>نقاط سحب وإيداع نقدي</span>
            </div>
        </div>

    </div>

    <!-- Pending Verification Queue Table -->
    @if(isset($pendingUsers) && $pendingUsers->count() > 0)
    <div class="bg-white rounded-2xl border border-amber-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-amber-100 flex items-center justify-between bg-amber-50/40">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">طابور طلبات التسجيل المعلقة (Verification Queue)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">يتطلب النظام موافقة الإدارة قبل تمكين العميل من تنفيذ أي حركة مالية</p>
                </div>
            </div>
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="text-xs font-bold text-amber-800 hover:text-amber-900 flex items-center gap-1 transition">
                <span>عرض كافة الطلبات</span>
                <span>&larr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">اسم العميل الكامل</th>
                        <th class="py-3.5 px-6">رقم الجوال والبريد</th>
                        <th class="py-3.5 px-6">تاريخ التسجيل</th>
                        <th class="py-3.5 px-6 text-center">إجراءات الاعتماد الفوري</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pendingUsers as $user)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <td class="py-3.5 px-6">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-slate-900 hover:text-teal-700 transition">
                                {{ $user->full_name }}
                            </a>
                        </td>
                        <td class="py-3.5 px-6">
                            <div class="font-medium text-slate-800 num-font" dir="ltr">{{ $user->phone }}</div>
                            <div class="text-[10px] text-slate-400">{{ $user->email ?? 'بدون بريد إلكتروني' }}</div>
                        </td>
                        <td class="py-3.5 px-6 num-font text-slate-500">
                            {{ $user->created_at->translatedFormat('Y-m-d H:i') }}
                        </td>
                        <td class="py-3.5 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] px-3.5 py-1.5 rounded-xl transition shadow-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        <span>اعتماد وتفعيل</span>
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد رفض طلب تسجيل العميل؟')">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 font-semibold text-[11px] px-3.5 py-1.5 rounded-xl transition">
                                        رفض الطلب
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Live Financial Operations Stream -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">سجل المعاملات اللحظية المباشرة (Live Stream)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">سجل الحركات المصرفية المنفذة في النظام في الوقت الحقيقي</p>
                </div>
            </div>
            <a href="{{ route('admin.transactions') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 flex items-center gap-1 transition">
                <span>دفتر الأستاذ العام</span>
                <span>&larr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">نوع العملية</th>
                        <th class="py-3.5 px-6">المبلغ والعملة</th>
                        <th class="py-3.5 px-6">الطرف الأول (العميل)</th>
                        <th class="py-3.5 px-6">الطرف المقابل</th>
                        <th class="py-3.5 px-6">البيان الرقابي</th>
                        <th class="py-3.5 px-6">التوقيت</th>
                        <th class="py-3.5 px-6 text-center">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-6">
                            @if($tx->type === 'deposit')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    إيداع نقدي
                                </span>
                            @elseif($tx->type === 'withdraw')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    سحب نقدي
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    تحويل رصيد
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 font-bold text-slate-900 num-font text-sm">
                            {{ number_format($tx->amount, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">{{ $tx->currency ?? 'SAR' }}</span>
                        </td>
                        <td class="py-3.5 px-6">
                            @if($tx->user)
                                <a href="{{ route('admin.users.show', $tx->user->id) }}" class="font-bold text-slate-900 hover:text-teal-700 transition">
                                    {{ $tx->user->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">{{ $tx->user->phone }}</div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6">
                            @if($tx->agent)
                                <div class="font-bold text-slate-800">{{ $tx->agent->full_name }}</div>
                                <div class="text-[10px] text-slate-400 num-font" dir="ltr">وكيل: {{ $tx->agent->phone }}</div>
                            @elseif($tx->admin)
                                <div class="font-semibold text-purple-800">إشراف: {{ $tx->admin->username }}</div>
                            @else
                                <span class="text-slate-400">تحويل داخلي</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="py-3.5 px-6 num-font text-slate-400">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-3.5 px-6 text-center">
                            <button onclick='showTxDetails({
                                id: "{{ $tx->id }}",
                                type_label: "{{ $tx->type === "deposit" ? "إيداع نقدي" : ($tx->type === "withdraw" ? "سحب نقدي" : "تحويل رصيد") }}",
                                amount: "{{ number_format($tx->amount, 2) }}",
                                currency: "{{ $tx->currency ?? 'SAR' }}",
                                user_name: "{{ $tx->user ? $tx->user->full_name : '' }}",
                                counterparty: "{{ $tx->agent ? $tx->agent->full_name : ($tx->admin ? $tx->admin->username : 'داخلي') }}",
                                description: "{{ addslashes($tx->description) }}",
                                created_at: "{{ $tx->created_at->format('Y-m-d H:i:s') }}"
                            })' class="text-slate-400 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100 transition" title="عرض السند">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">لا توجد عمليات مالية مسجلة في السجل اللحظي بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
