@extends('layouts.admin')

@section('title', 'شبكة الوكلاء المعتمدين')
@section('page_title', 'إدارة شبكة الوكلاء ومراكز الصرافة')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Agent Formulation Card -->
        <div class="lg:col-span-1 bg-surface-card p-5 rounded-lg border border-surface-border h-fit space-y-4">
            <div class="pb-3 border-b border-surface-border flex items-center justify-between">
                <h3 class="text-xs font-bold text-ink-primary">تسجيل واعتماد وكيل جديد</h3>
                <span class="text-[10px] text-ink-muted font-mono">NEW AGENT</span>
            </div>

            <form action="{{ route('admin.agents.create') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">اسم المركز / المحل</label>
                    <input type="text" name="full_name" required placeholder="مثال: شركة البركة للصرافة"
                           class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">رقم هاتف الوكيل</label>
                    <input type="text" name="phone" required placeholder="77xxxxxxx" dir="ltr"
                           class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition text-right">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">كلمة المرور</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-ink-primary mb-1">الرصيد الافتتاحي</label>
                        <input type="number" step="0.01" min="0" name="initial_balance" value="0"
                               class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary num-font focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-primary mb-1">العملة</label>
                        <select name="currency" class="w-full px-2.5 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                            <option value="SAR">SAR - سعودي</option>
                            <option value="YER">YER - يمني</option>
                            <option value="USD">USD - دولار</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2 px-3 rounded text-xs transition mt-2">
                    إنشاء واعتماد حساب الوكيل
                </button>
            </form>
        </div>

        <!-- Agents Registry Ledger -->
        <div class="lg:col-span-2 bg-surface-card rounded-lg border border-surface-border overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-ink-primary">سجل الوكلاء المعتمدين في النظام</h3>
                    <p class="text-[11px] text-ink-muted mt-0.5">المراكز المصرح لها بتنفيذ عمليات الإيداع والسحب النقدي</p>
                </div>
                <span class="text-xs font-mono text-ink-muted font-semibold">{{ $agents->total() }} وكيل</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                        <tr>
                            <th class="py-3 px-4">اسم المركز / الوكيل</th>
                            <th class="py-3 px-4">رقم الهاتف</th>
                            <th class="py-3 px-4">الرصيد النقدي</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">تاريخ الاعتماد</th>
                            <th class="py-3 px-4 text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-border">
                        @forelse($agents as $ag)
                        <tr class="hover:bg-surface-base/80 transition">
                            <td class="py-3 px-4">
                                <div class="font-semibold text-ink-primary">{{ $ag->full_name }}</div>
                                <div class="text-[10px] text-ink-muted font-mono">{{ $ag->id }}</div>
                            </td>
                            <td class="py-3 px-4 num-font font-medium text-slate-800" dir="ltr">{{ $ag->phone }}</td>
                            <td class="py-3 px-4 font-bold text-ink-primary num-font text-sm">
                                {{ number_format($ag->balance, 2) }} <span class="text-[10px] font-normal text-ink-muted">ر.ي</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($ag->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                        <span class="w-1.5 h-1.5 rounded-full bg-fin-teal"></span>
                                        مفعّل
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-fin-crimsonBg text-fin-crimson border border-fin-crimsonBorder">
                                        <span class="w-1.5 h-1.5 rounded-full bg-fin-crimson"></span>
                                        معلّق
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 num-font text-ink-muted">{{ $ag->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($ag->status === 'active')
                                    <form action="{{ route('admin.agents.status', $ag->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" onclick="return confirm('تأكيد تعليق حساب هذا الوكيل؟')" class="text-fin-crimson hover:bg-fin-crimsonBg border border-surface-border hover:border-fin-crimsonBorder px-2.5 py-1 rounded text-xs font-medium transition">
                                            تعليق
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.agents.status', $ag->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="bg-slate-900 hover:bg-black text-white px-2.5 py-1 rounded text-xs font-medium transition">
                                            إعادة التفعيل
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-ink-muted">لا يوجد وكلاء معتمدين مسجلين في النظام.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agents->hasPages())
            <div class="p-3.5 border-t border-surface-border">
                {{ $agents->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
