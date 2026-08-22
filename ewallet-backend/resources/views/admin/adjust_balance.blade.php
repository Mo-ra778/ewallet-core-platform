@extends('layouts.admin')

@section('title', 'التسويات والتغذية المالية المباشرة')
@section('page_title', 'عمليات التغذية والخصم الإداري الرقابي')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Direct Adjustment Terminal Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">نموذج التسوية المالية المباشرة</h3>
                    <p class="text-xs text-slate-400 mt-0.5">تغذية رصيد أو خصم مباشر مع التوثيق الرقابي الإلزامي</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                إجراء محاسبي خاضع للتدقيق
            </span>
        </div>

        <form action="{{ route('admin.balance.adjust') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Target Entity Selector (User / Agent) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">نوع الحساب المستهدف</label>
                    <select name="target_type" id="target_type" onchange="toggleTargetList()" required
                            class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="user">عميل عادي (User)</option>
                        <option value="agent">وكيل معتمد (Agent)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">طبيعة العملية</label>
                    <select name="operation" required
                            class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="credit">إيداع / تغذية رصيد (+) Credit</option>
                        <option value="debit">خصم إداري من الرصيد (-) Debit</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic User / Agent Target Dropdown -->
            <div id="user_selector_wrapper">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">اختر العميل المستفيد</label>
                <select name="target_id" id="user_target_select" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                    <option value="">-- اختر من قائمة العملاء النشطين --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->phone }}) — الرصيد: {{ number_format($u->balance, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <div id="agent_selector_wrapper" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">اختر الوكيل المستهدف</label>
                <select id="agent_target_select" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                    <option value="">-- اختر من قائمة الوكلاء المعتمدين --</option>
                    @foreach($agents as $a)
                        <option value="{{ $a->id }}">{{ $a->full_name }} ({{ $a->phone }}) — العهدة: {{ number_format($a->balance, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المطلوب تسويته</label>
                    <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required placeholder="0.00" 
                           class="w-full px-3.5 py-2 text-base font-extrabold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 num-font focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">العملة</label>
                    <select name="currency" required class="w-full px-3 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="SAR">SAR - سعودي</option>
                        <option value="YER">YER - يمني</option>
                        <option value="USD">USD - دولار</option>
                        <option value="EUR">EUR - يورو</option>
                    </select>
                </div>
            </div>

            <!-- Audit Reason Note (Mandatory) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">البيان والسبب الرقابي للتسوية (إلزامي للتدقيق)</label>
                <input type="text" name="reason" value="{{ old('reason') }}" required placeholder="مثال: تسوية سند إيداع بنكي رقم 88402 / تغذية افتتاحية" 
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
            </div>

            <button type="submit" onclick="return confirm('تأكيد تنفيذ عملية التسوية المالية المباشرة وتسجيلها في دفتر الأستاذ العام؟')"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>تنفيذ التسوية وتحديث الرصيد فوراً</span>
            </button>
        </form>
    </div>

</div>

<script>
    function toggleTargetList() {
        const type = document.getElementById('target_type').value;
        const userWrapper = document.getElementById('user_selector_wrapper');
        const agentWrapper = document.getElementById('agent_selector_wrapper');
        const userSelect = document.getElementById('user_target_select');
        const agentSelect = document.getElementById('agent_target_select');

        if (type === 'user') {
            userWrapper.classList.remove('hidden');
            agentWrapper.classList.add('hidden');
            userSelect.name = 'target_id';
            agentSelect.name = '';
        } else {
            userWrapper.classList.add('hidden');
            agentWrapper.classList.remove('hidden');
            agentSelect.name = 'target_id';
            userSelect.name = '';
        }
    }
</script>
@endsection
