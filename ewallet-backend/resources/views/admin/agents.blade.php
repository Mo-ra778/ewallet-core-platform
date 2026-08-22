@extends('layouts.admin')

@section('title', 'إدارة شبكة الوكلاء المعتمدين')
@section('page_title', 'مراكز الصرافة ونقاط تقديم الخدمة')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Agents Master Table (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75-.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">سجل وكلاء الخدمة المعتمدين</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">مراكز السحب والإيداع النقدي المصرح لها</p>
                    </div>
                </div>
                <span class="num-font text-xs font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-200/60">{{ $agents->total() }} وكيل</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="py-4 px-6">بيانات المركز / الوكيل</th>
                            <th class="py-4 px-6">الهاتف</th>
                            <th class="py-4 px-6">رصيد العهدة</th>
                            <th class="py-4 px-6">الحالة</th>
                            <th class="py-4 px-6 text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($agents as $agent)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $agent->full_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono" dir="ltr">UUID: {{ substr($agent->id, 0, 8) }}...</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800 num-font" dir="ltr">
                                {{ $agent->phone }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 num-font text-sm">
                                {{ number_format($agent->balance, 2) }} <span class="text-xs font-bold text-teal-700 font-sans">ر.ي</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($agent->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        مفعّل
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        معلّق
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('admin.agents.status', $agent->id) }}" method="POST" class="inline">
                                    @csrf
                                    @if($agent->status === 'active')
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" onclick="return confirm('تأكيد تعليق مركز الوكيل؟')" 
                                                class="text-rose-600 hover:bg-rose-50 font-semibold text-[11px] px-3 py-1.5 rounded-xl transition border border-rose-200/80 shadow-xs">
                                            تعليق
                                        </button>
                                    @else
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" 
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] px-3 py-1.5 rounded-xl transition shadow-xs">
                                            إعادة تفعيل
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">لم يتم تسجيل أي وكلاء في النظام حتى الآن.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agents->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $agents->links() }}
            </div>
            @endif
        </div>

        <!-- Add New Agent Form (1 Column) -->
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft space-y-5">
            <div class="pb-4 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-900">تسجيل مركز وكيل جديد</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">إصدار ترخيص لنقطة خدمة وإيداع جديدة</p>
            </div>

            <form action="{{ route('admin.agents.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">اسم الوكيل / المركز الكامل</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="مثال: صرافة التضامن - فرع التحرير" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف الدخول للوكيل</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777999888" dir="ltr"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition text-right num-font">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">كلمة المرور الابتدائية</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">الرصيد الافتتاحي للعهدة (اختياري)</label>
                    <input type="number" step="0.01" min="0" name="initial_balance" value="{{ old('initial_balance', 0) }}" 
                           class="w-full px-3.5 py-2 text-sm font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 num-font focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition shadow-xs mt-2">
                    إنشاء واعتماد حساب الوكيل
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
