@extends('layouts.admin')

@section('title', 'التسويات والتغذية المباشرة')
@section('page_title', 'التسويات المالية والتغذية والخصم الإداري')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="bg-surface-card p-6 rounded-lg border border-surface-border space-y-5">
        <div class="pb-4 border-b border-surface-border">
            <h3 class="text-xs font-bold text-ink-primary">تنفيذ تسوية مالية مباشرة (Direct Financial Adjustment)</h3>
            <p class="text-[11px] text-ink-muted mt-0.5">تغذية رصيد أو خصم مباشر من حساب مستخدم أو وكيل مع التوثيق الرقابي</p>
        </div>

        <form action="{{ route('admin.balance.adjust') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Target Entity Selection Tabs -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1.5">الحساب المستهدف</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2.5 p-2.5 border border-surface-border rounded cursor-pointer hover:bg-surface-base transition has-[:checked]:border-slate-900 has-[:checked]:bg-surface-base">
                        <input type="radio" name="target_type" value="user" checked onchange="toggleTargetLists()" class="text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="text-xs font-semibold text-ink-primary block">محفظة عميل (User)</span>
                            <span class="text-[10px] text-ink-muted block">حسابات الأفراد المسجلين</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2.5 p-2.5 border border-surface-border rounded cursor-pointer hover:bg-surface-base transition has-[:checked]:border-slate-900 has-[:checked]:bg-surface-base">
                        <input type="radio" name="target_type" value="agent" onchange="toggleTargetLists()" class="text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="text-xs font-semibold text-ink-primary block">حساب وكيل (Agent)</span>
                            <span class="text-[10px] text-ink-muted block">مراكز الصرافة المعتمدة</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Target User Selector -->
            <div id="userSelectGroup">
                <label class="block text-xs font-semibold text-ink-primary mb-1">حدد العميل</label>
                <select name="target_id" id="userInput" class="w-full px-3 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                    <option value="">-- حدد العميل النشط --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->phone }}) — الرصيد: {{ number_format($u->balance, 2) }} ر.ي</option>
                    @endforeach
                </select>
            </div>

            <!-- Target Agent Selector -->
            <div id="agentSelectGroup" class="hidden">
                <label class="block text-xs font-semibold text-ink-primary mb-1">حدد الوكيل</label>
                <select name="target_id_agent" id="agentInput" class="w-full px-3 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition" disabled>
                    <option value="">-- حدد الوكيل المعتمد --</option>
                    @foreach($agents as $a)
                        <option value="{{ $a->id }}">{{ $a->full_name }} ({{ $a->phone }}) — الرصيد: {{ number_format($a->balance, 2) }} ر.ي</option>
                    @endforeach
                </select>
            </div>

            <!-- Operation Type -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1.5">نوع الحركة المالية</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2.5 p-2.5 border border-surface-border rounded cursor-pointer hover:bg-surface-base transition has-[:checked]:border-fin-teal has-[:checked]:bg-fin-tealBg">
                        <input type="radio" name="operation" value="credit" checked class="text-fin-teal focus:ring-fin-teal">
                        <div>
                            <span class="text-xs font-semibold text-fin-teal block">إضافة وتغذية رصيد (Credit)</span>
                            <span class="text-[10px] text-ink-muted block">زيادة الرصيد المتاح للطرف</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2.5 p-2.5 border border-surface-border rounded cursor-pointer hover:bg-surface-base transition has-[:checked]:border-fin-crimson has-[:checked]:bg-fin-crimsonBg">
                        <input type="radio" name="operation" value="debit" class="text-fin-crimson focus:ring-fin-crimson">
                        <div>
                            <span class="text-xs font-semibold text-fin-crimson block">خصم وتسوية رصيد (Debit)</span>
                            <span class="text-[10px] text-ink-muted block">خصم من رصيد الطرف</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-ink-primary mb-1">المبلغ</label>
                    <input type="number" step="0.01" min="1" name="amount" required placeholder="0.00"
                           class="w-full px-3 py-2 text-sm font-bold num-font bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">العملة</label>
                    <select name="currency" required class="w-full px-2.5 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                        <option value="SAR">SAR - سعودي</option>
                        <option value="YER">YER - يمني</option>
                        <option value="USD">USD - دولار</option>
                        <option value="EUR">EUR - يورو</option>
                    </select>
                </div>
            </div>

            <!-- Reason / Regulatory Note -->
            <div>
                <label class="block text-xs font-semibold text-ink-primary mb-1">السبب والملاحظة الرقابية</label>
                <textarea name="reason" rows="2" required placeholder="مثال: تسوية إدارية رقم #1042، تغذية مصرفية..."
                          class="w-full px-3 py-2 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition"></textarea>
            </div>

            <button type="submit" onclick="return confirm('تأكيد تنفيذ هذه الحركة المالية المباشرة وتسجيلها بالسجل الرقابي؟')"
                    class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2.5 px-4 rounded text-xs transition">
                تنفيذ وتأكيد العملية
            </button>
        </form>
    </div>

</div>

<script>
    function toggleTargetLists() {
        const type = document.querySelector('input[name="target_type"]:checked').value;
        const userGroup = document.getElementById('userSelectGroup');
        const agentGroup = document.getElementById('agentSelectGroup');
        const userInput = document.getElementById('userInput');
        const agentInput = document.getElementById('agentInput');

        if (type === 'user') {
            userGroup.classList.remove('hidden');
            agentGroup.classList.add('hidden');
            userInput.disabled = false;
            userInput.name = 'target_id';
            agentInput.disabled = true;
            agentInput.name = 'target_id_agent';
        } else {
            userGroup.classList.add('hidden');
            agentGroup.classList.remove('hidden');
            userInput.disabled = true;
            userInput.name = 'target_id_user';
            agentInput.disabled = false;
            agentInput.name = 'target_id';
        }
    }
</script>
@endsection
