@extends('layouts.agent')

@section('title', 'طلب سحب نقدي — الخطوة 1')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Step 1 Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-soft space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">طلب سحب نقدي (Cash-Out Step 1)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">توليد وإرسال رمز التحقق الآمن (OTP) لتطبيق العميل</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-300/80">
                الخطوة 1 من 2
            </span>
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs text-slate-600 space-y-1.5">
            <span class="font-bold text-slate-800 block">بروتوكول السحب الآمن بخطوتين:</span>
            <p class="text-[11px] text-slate-500 leading-relaxed">&bull; أدخل رقم هاتف العميل والمبلغ المطلوب تسليمه كاش.</p>
            <p class="text-[11px] text-slate-500 leading-relaxed">&bull; يقوم النظام بفحص رصيد العميل وتوليد رمز OTP صالح لمدة 5 دقائق يصله على تطبيقه.</p>
        </div>

        <form id="withdraw-form" action="{{ route('agent.withdraw.otp') }}" method="POST" class="space-y-4">
            @csrf

            <!-- User Phone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف العميل المسجل <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="text" id="phone-input" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 777111222" dir="ltr"
                           autocomplete="off"
                           class="w-full px-3.5 py-2.5 text-xs num-font bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition text-right @error('phone') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200/80 focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 @enderror">
                    <div id="lookup-spinner" class="hidden absolute left-3 top-2.5 text-amber-600">
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
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">المبلغ المراد سحبه <span class="text-rose-500">*</span></label>
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
                        <option value="SAR" {{ old('currency', 'SAR') === 'SAR' ? 'selected' : '' }}>SAR - سعودي</option>
                        <option value="YER" {{ old('currency') === 'YER' ? 'selected' : '' }}>YER - يمني</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - دولار</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                    </select>
                    @error('currency')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Open Modal Button -->
            <button type="button" id="open-modal-btn"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 px-4 rounded-xl text-xs transition shadow-md mt-2 flex items-center justify-center gap-2">
                <span>مراجعة الطلب وتوليد كود التحقق (OTP)</span>
                <span>&larr;</span>
            </button>
        </form>
    </div>

</div>

<!-- ========================================== -->
<!-- Confirmation Modal for Cash-Out Step 1 -->
<!-- ========================================== -->
<div id="confirm-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all animate-in fade-in zoom-in duration-150">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-700 to-amber-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">تأكيد طلب السحب النقدي (OTP)</h3>
                    <p class="text-[11px] text-amber-100/80">سيتم إرسال رمز الأمان لهاتف العميل لتأكيد التسليم</p>
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
                    <span class="text-xs font-semibold text-slate-500">اسم صاحب الحساب:</span>
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
            <div class="bg-amber-50/60 border border-amber-200/80 rounded-xl p-4 space-y-2 text-center">
                <span class="text-[11px] font-bold text-amber-900 block">المبلغ المطلوب سحبه وتسليمه نقداً:</span>
                <div class="text-2xl font-black text-amber-950 num-font flex items-center justify-center gap-1.5">
                    <span id="modal-amount">0.00</span>
                    <span id="modal-currency" class="text-sm font-bold text-amber-800">SAR</span>
                </div>
            </div>

            <!-- Protocol Notice -->
            <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl text-xs text-slate-600 space-y-1">
                <div class="flex items-center gap-1.5 text-slate-800 font-bold text-[11px]">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <span>الخطوة التالية بعد التأكيد:</span>
                </div>
                <p class="text-[11px] text-slate-500">سيصل العميل كود OTP في إشعارات التطبيق، ولن يتم تسليم المبلغ إلا بعد إدخال الكود في الخطوة 2.</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition">
                إلغاء وتعديل
            </button>
            <button type="button" id="final-submit-btn" onclick="submitWithdrawForm()" class="px-5 py-2.5 bg-amber-700 hover:bg-amber-800 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                <span>إرسال رمز الـ OTP للعميل</span>
            </button>
        </div>

    </div>
</div>

<script>
    const phoneInput = document.getElementById('phone-input');
    const amountInput = document.getElementById('amount-input');
    const currencySelect = document.getElementById('currency-select');
    const userInfoCard = document.getElementById('user-info-card');
    const lookupSpinner = document.getElementById('lookup-spinner');
    const confirmModal = document.getElementById('confirm-modal');
    const withdrawForm = document.getElementById('withdraw-form');

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
                    غير متاح للسحب
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

        if (!phone) {
            phoneInput.focus();
            phoneInput.classList.add('ring-2', 'ring-amber-500', 'border-amber-500');
            setTimeout(() => phoneInput.classList.remove('ring-2', 'ring-amber-500', 'border-amber-500'), 2500);
            showAppToast('تنبيه السحب النقدي', 'يرجى إدخال رقم هاتف العميل أولاً للمتابعة.', 'warning');
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

    function submitWithdrawForm() {
        const submitBtn = document.getElementById('final-submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>جاري الإرسال...</span>
        `;
        withdrawForm.submit();
    }

    // Close on Escape or click outside
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirmModal();
    });
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeConfirmModal();
    });

    if (phoneInput.value.trim().length >= 3) {
        fetchCustomerData(phoneInput.value.trim());
    }
</script>
@endsection
