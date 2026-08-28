@extends('layouts.agent')

@section('title', 'إيداع نقدي للعميل')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Cash-In Terminal Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">محطة الإيداع النقدي (Cash-In Terminal)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">تغذية رصيد محفظة العميل فوراً بالعملة المحددة</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                تنفيذ فوري
            </span>
        </div>

        <!-- Multi-Currency Available Liquidity Box -->
        <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl space-y-2">
            <span class="text-xs font-bold text-slate-500 block">رصيد العهدة المتاح لك للتسليم والإيداع:</span>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">يمني (YER)</span>
                    <strong id="agent-bal-yer" class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('YER'), 0) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">سعودي (SAR)</span>
                    <strong id="agent-bal-sar" class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('SAR'), 2) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">دولار (USD)</span>
                    <strong id="agent-bal-usd" class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('USD'), 2) }}</strong>
                </div>
                <div class="bg-white p-2 rounded-lg border border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 block font-medium">يورو (EUR)</span>
                    <strong id="agent-bal-eur" class="num-font text-slate-900 text-xs">{{ number_format($agent->getCurrencyBalance('EUR'), 2) }}</strong>
                </div>
            </div>
        </div>

        <form id="deposit-form" action="{{ route('agent.deposit') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف العميل المسجل بالمحفظة <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="text" id="phone-input" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                           autocomplete="off"
                           class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition text-right @error('phone') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                    <div id="lookup-spinner" class="hidden absolute left-3 top-2.5 text-teal-600">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>

                <!-- Live User Info Feedback Card -->
                <div id="user-info-card" class="hidden mt-2 p-2.5 rounded-xl text-xs transition-all duration-200 border"></div>

                @error('phone')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Amount and Currency Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المراد إيداعه <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="1" id="amount-input" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2 text-base font-extrabold num-font bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition @error('amount') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                    @error('amount')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">العملة <span class="text-rose-500">*</span></label>
                    <select id="currency-select" name="currency" required class="w-full px-3 py-2.5 text-xs font-bold bg-slate-50 border rounded-xl text-slate-800 focus:bg-white focus:outline-none transition @error('currency') border-rose-400 ring-2 ring-rose-500/20 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                        <option value="YER" {{ old('currency', 'YER') === 'YER' ? 'selected' : '' }}>YER - يمني</option>
                        <option value="SAR" {{ old('currency') === 'SAR' ? 'selected' : '' }}>SAR - سعودي</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - دولار</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                    </select>
                    @error('currency')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">ملاحظة أو رقم سند الاستلام النقدي (اختياري)</label>
                <input type="text" id="notes-input" name="notes" value="{{ old('notes') }}" placeholder="مثال: سند قبض كاش رقم 450"
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
            </div>

            <!-- Open Confirmation Modal Button -->
            <button type="button" id="open-modal-btn"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>مراجعة وتأكيد الإيداع للعميل</span>
            </button>
        </form>
    </div>

</div>

<!-- ========================================== -->
<!-- Professional FinTech Confirmation Modal -->
<!-- ========================================== -->
<div id="confirm-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all animate-in fade-in zoom-in duration-150">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-teal-700 to-teal-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-teal-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">تأكيد عملية الإيداع النقدي</h3>
                    <p class="text-[11px] text-teal-100/80">يرجى مراجعة تفاصيل المستفيد والمبلغ قبل التنفيذ</p>
                </div>
            </div>
            <button type="button" onclick="closeConfirmModal()" class="text-white/70 hover:text-white text-lg font-bold p-1 rounded-lg hover:bg-white/10 transition">
                &times;
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-6 space-y-4">
            
            <!-- Customer Identity Card -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-xs font-semibold text-slate-500">اسم صاحب الحساب (المستفيد):</span>
                    <span id="modal-user-name" class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span id="modal-user-name-text">جاري الفحص...</span>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">رقم هاتف العميل:</span>
                    <span id="modal-user-phone" class="text-xs font-bold num-font text-slate-800" dir="ltr">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">حالة الحساب:</span>
                    <span id="modal-user-status" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        نشط ومفعل
                    </span>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="bg-teal-50/60 border border-teal-200/80 rounded-xl p-4 space-y-3">
                <div class="text-center pb-2 border-b border-teal-200/60">
                    <span class="text-[11px] font-bold text-teal-800 block mb-1">المبلغ الصافي المراد إيداعه في محفظة العميل:</span>
                    <div class="text-2xl font-black text-teal-900 num-font flex items-center justify-center gap-1.5">
                        <span id="modal-amount">0.00</span>
                        <span id="modal-currency" class="text-sm font-bold text-teal-700">YER</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[11px] text-teal-900/80 pt-1">
                    <div class="flex justify-between">
                        <span>رصيد عهدتك المتاح:</span>
                        <strong id="modal-agent-bal" class="num-font text-slate-900">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>نوع العملية:</span>
                        <strong class="text-teal-900">إيداع نقدي فوري</strong>
                    </div>
                </div>
            </div>

            <!-- Notes if any -->
            <div id="modal-notes-container" class="hidden bg-slate-50 p-2.5 rounded-lg border border-slate-200/60 text-xs flex items-center justify-between">
                <span class="text-slate-500">ملاحظة / السند:</span>
                <span id="modal-notes-text" class="font-semibold text-slate-800"></span>
            </div>

            <!-- Warning Notice -->
            <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 text-amber-800 p-2.5 rounded-xl text-[11px]">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <span>سيتم خصم المبلغ من رصيدك مباشرة وإضافته لرصيد العميل بشكل نهائي.</span>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition">
                إلغاء وتعديل
            </button>
            <button type="button" id="final-submit-btn" onclick="submitDepositForm()" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <span>تأكيد وخصم المبلغ الآن</span>
            </button>
        </div>

    </div>
</div>

<script>
    const phoneInput = document.getElementById('phone-input');
    const amountInput = document.getElementById('amount-input');
    const currencySelect = document.getElementById('currency-select');
    const notesInput = document.getElementById('notes-input');
    const userInfoCard = document.getElementById('user-info-card');
    const lookupSpinner = document.getElementById('lookup-spinner');
    const confirmModal = document.getElementById('confirm-modal');
    const depositForm = document.getElementById('deposit-form');

    let currentCustomerData = null;
    let lookupTimeout = null;

    // Live Phone Lookup with Debounce
    phoneInput.addEventListener('input', function () {
        const phone = this.value.trim();
        clearTimeout(lookupTimeout);

        if (phone.length < 3) {
            userInfoCard.classList.add('hidden');
            currentCustomerData = null;
            return;
        }

        lookupTimeout = setTimeout(() => {
            fetchCustomerData(phone);
        }, 350);
    });

    async function fetchCustomerData(phone) {
        lookupSpinner.classList.remove('hidden');
        try {
            const res = await fetch(`{{ route('agent.lookup.user') }}?phone=${encodeURIComponent(phone)}`);
            const json = await res.json();
            lookupSpinner.classList.add('hidden');

            if (json.success && json.data) {
                currentCustomerData = json.data;
                renderUserInfoBadge(json.data);
            } else {
                currentCustomerData = null;
                renderUserNotFound(json.message || 'العميل غير مسجل');
            }
        } catch (e) {
            lookupSpinner.classList.add('hidden');
            console.error('Error fetching user:', e);
        }
    }

    function renderUserInfoBadge(data) {
        userInfoCard.classList.remove('hidden');
        if (data.is_active) {
            userInfoCard.className = 'mt-2 p-2.5 rounded-xl text-xs transition-all duration-200 border bg-emerald-50/80 border-emerald-200 text-emerald-900 flex items-center justify-between';
            userInfoCard.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">👤</div>
                    <div>
                        <span class="font-bold block">${data.full_name}</span>
                        <span class="text-[10px] text-emerald-700">رقم الهاتف: <span dir="ltr">${data.phone}</span></span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200/80 text-emerald-900">
                    حساب نشط ✅
                </span>
            `;
        } else {
            userInfoCard.className = 'mt-2 p-2.5 rounded-xl text-xs transition-all duration-200 border bg-rose-50 border-rose-200 text-rose-900 flex items-center justify-between';
            userInfoCard.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-rose-600 font-bold">⚠️</span>
                    <span>${data.full_name} - ${data.status_label}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 text-rose-900">
                    غير متاح للإيداع
                </span>
            `;
        }
    }

    function renderUserNotFound(msg) {
        userInfoCard.classList.remove('hidden');
        userInfoCard.className = 'mt-2 p-2.5 rounded-xl text-xs transition-all duration-200 border bg-amber-50 border-amber-200 text-amber-900 flex items-center gap-2';
        userInfoCard.innerHTML = `
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
            <span>${msg}</span>
        `;
    }

    // Modal Trigger
    document.getElementById('open-modal-btn').addEventListener('click', async function () {
        const phone = phoneInput.value.trim();
        const amount = parseFloat(amountInput.value);
        const currency = currencySelect.value;
        const notes = notesInput.value.trim();

        if (!phone) {
            phoneInput.focus();
            phoneInput.classList.add('ring-2', 'ring-teal-500', 'border-teal-500');
            setTimeout(() => phoneInput.classList.remove('ring-2', 'ring-teal-500', 'border-teal-500'), 2500);
            showAppToast('تنبيه الإيداع النقدي', 'يرجى إدخال رقم هاتف العميل المسجل أولاً للمتابعة.', 'warning');
            return;
        }

        if (isNaN(amount) || amount <= 0) {
            amountInput.focus();
            amountInput.classList.add('ring-2', 'ring-rose-500', 'border-rose-500');
            setTimeout(() => amountInput.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500'), 2500);
            showAppToast('تنبيه المبلغ', 'يرجى إدخال مبلغ صحيح أكبر من الصفر.', 'warning');
            return;
        }

        // Populate Modal Fields
        document.getElementById('modal-user-phone').textContent = phone;
        document.getElementById('modal-amount').textContent = Number(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('modal-currency').textContent = currency;

        // Populate Agent Balance for Selected Currency
        let agentBal = '0.00';
        if (currency === 'YER') agentBal = document.getElementById('agent-bal-yer').innerText;
        else if (currency === 'SAR') agentBal = document.getElementById('agent-bal-sar').innerText;
        else if (currency === 'USD') agentBal = document.getElementById('agent-bal-usd').innerText;
        else if (currency === 'EUR') agentBal = document.getElementById('agent-bal-eur').innerText;
        document.getElementById('modal-agent-bal').textContent = agentBal + ' ' + currency;

        if (notes) {
            document.getElementById('modal-notes-container').classList.remove('hidden');
            document.getElementById('modal-notes-text').textContent = notes;
        } else {
            document.getElementById('modal-notes-container').classList.add('hidden');
        }

        // Fetch User details if not already loaded
        if (!currentCustomerData || currentCustomerData.phone !== phone) {
            document.getElementById('modal-user-name-text').textContent = 'جاري التحقق من هوية العميل...';
            try {
                const res = await fetch(`{{ route('agent.lookup.user') }}?phone=${encodeURIComponent(phone)}`);
                const json = await res.json();
                if (json.success && json.data) {
                    currentCustomerData = json.data;
                    document.getElementById('modal-user-name-text').textContent = json.data.full_name;
                    document.getElementById('modal-user-status').textContent = json.data.status_label;
                    document.getElementById('modal-user-status').className = json.data.is_active ?
                        'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800' :
                        'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800';
                } else {
                    document.getElementById('modal-user-name-text').textContent = 'عميل غير مسجل';
                    document.getElementById('modal-user-status').textContent = 'غير موجود';
                    document.getElementById('modal-user-status').className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800';
                }
            } catch (e) {
                document.getElementById('modal-user-name-text').textContent = 'غير معروف';
            }
        } else {
            document.getElementById('modal-user-name-text').textContent = currentCustomerData.full_name;
            document.getElementById('modal-user-status').textContent = currentCustomerData.status_label;
            document.getElementById('modal-user-status').className = currentCustomerData.is_active ?
                'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800' :
                'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800';
        }

        // Show Modal
        confirmModal.classList.remove('hidden');
    });

    function closeConfirmModal() {
        confirmModal.classList.add('hidden');
    }

    function submitDepositForm() {
        const submitBtn = document.getElementById('final-submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>جاري المعالجة...</span>
        `;
        depositForm.submit();
    }

    // Close on Escape or click outside
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirmModal();
    });
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeConfirmModal();
    });

    // Check if phone was filled initially on reload
    if (phoneInput.value.trim().length >= 3) {
        fetchCustomerData(phoneInput.value.trim());
    }
</script>
@endsection
