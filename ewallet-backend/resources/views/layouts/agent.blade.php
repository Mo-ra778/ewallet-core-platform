<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بوابة الوكيل المعتمد') — محفظتي للخدمات النقدية</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"IBM Plex Sans Arabic"', 'sans-serif'],
                        mono: ['"Plus Jakarta Sans"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#F0FDFA',
                            100: '#CCFBF1',
                            500: '#14B8A6',
                            600: '#0D9488',
                            700: '#0F766E',
                            800: '#115E59',
                            900: '#134E4A',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 10px -2px rgba(15, 23, 42, 0.05), 0 1px 3px -1px rgba(15, 23, 42, 0.03)',
                        'card': '0 4px 20px -4px rgba(15, 23, 42, 0.06)',
                        'xs': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    <style>
        .num-font { font-family: 'Plus Jakarta Sans', monospace; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="min-h-full font-sans text-slate-900 bg-[#F8FAFC] antialiased flex flex-col selection:bg-brand-700 selection:text-white">

    <!-- Sticky Cashier Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            
            <!-- Center Identity & Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-700 text-white flex items-center justify-center font-bold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75-.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xs font-bold text-slate-900 leading-tight">بوابة الوكيل المعتمد</h1>
                    <p class="text-[11px] text-slate-400 font-medium truncate max-w-[140px] sm:max-w-xs">{{ session('agent_name') }}</p>
                </div>
            </div>

            <!-- Multi-Currency Live Liquidity Badges in Header -->
            @if(isset($agent))
            <div class="hidden md:flex items-center gap-2">
                <div class="bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400">يمني:</span>
                    <span class="num-font font-bold text-slate-900">{{ number_format($agent->getCurrencyBalance('YER'), 0) }}</span>
                    <span class="text-[10px] font-bold text-teal-700">ر.ي</span>
                </div>
                <div class="bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400">سعودي:</span>
                    <span class="num-font font-bold text-slate-900">{{ number_format($agent->getCurrencyBalance('SAR'), 2) }}</span>
                    <span class="text-[10px] font-bold text-emerald-700">SAR</span>
                </div>
                <div class="bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400">دولار:</span>
                    <span class="num-font font-bold text-slate-900">{{ number_format($agent->getCurrencyBalance('USD'), 2) }}</span>
                    <span class="text-[10px] font-bold text-blue-700">$</span>
                </div>
            </div>
            @endif

            <!-- Navigation Tabs & Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <nav class="flex items-center gap-1 bg-slate-100/80 p-1 rounded-xl">
                    <a href="{{ route('agent.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('agent.dashboard') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        الرئيسية
                    </a>
                    <a href="{{ route('agent.deposit.form') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('agent.deposit*') ? 'bg-white text-teal-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        إيداع كاش
                    </a>
                    <a href="{{ route('agent.withdraw.form') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('agent.withdraw*') ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        سحب كاش
                    </a>
                    <a href="{{ route('agent.remittance.form') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('agent.remittance*') ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        صرف حوالة
                    </a>
                    <a href="{{ route('agent.transactions') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->routeIs('agent.transactions') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        السجل
                    </a>
                </nav>


                @php
                    $agentUnreadNotifs = \App\Models\Notification::where('recipient_id', session('agent_id'))->where('recipient_type', 'agent')->where('is_read', false)->count();
                @endphp
                <a href="{{ route('agent.notifications') }}" class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition border border-slate-200/60 {{ request()->routeIs('agent.notifications*') ? 'bg-slate-100 text-teal-700 font-bold' : '' }}" title="مركز الإشعارات">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    @if($agentUnreadNotifs > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center animate-pulse">
                            {{ $agentUnreadNotifs }}
                        </span>
                    @endif
                </a>

                <form action="{{ route('agent.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition border border-slate-200/60" title="تسجيل الخروج">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- Main Body Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <!-- Global Flash Alerts -->
        @if(session('success'))
            <div id="flash-success-banner" class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-4 rounded-2xl text-xs flex items-center justify-between shadow-soft animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-emerald-950 text-sm block">اكتملت العملية بنجاح!</span>
                        <span class="text-emerald-800 font-medium text-xs">{{ session('success') }}</span>
                    </div>
                </div>
                <button onclick="document.getElementById('flash-success-banner').remove()" class="text-emerald-500 hover:text-emerald-800 p-1.5 rounded-lg hover:bg-emerald-100/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-900 px-5 py-4 rounded-2xl text-xs space-y-2 shadow-soft animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    </div>
                    <span class="font-bold text-rose-950 text-sm">تنبيه: تعذر إتمام العملية</span>
                </div>
                @if(session('error'))
                    <p class="pr-11 text-rose-800 font-medium">{{ session('error') }}</p>
                @endif
                @if($errors->any())
                    <ul class="list-disc list-inside pr-11 text-rose-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Floating Live Toast Notification (Auto Dismiss) -->
    @if(session('success'))
    <div id="live-toast" class="fixed top-5 left-5 z-50 max-w-sm w-full bg-white border border-emerald-200/90 rounded-2xl shadow-2xl p-4 flex items-start gap-3 transform transition-all duration-300 translate-y-0 opacity-100">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-slate-900">إشعار عملية مالية فورية</h4>
                <span class="text-[10px] text-slate-400 font-medium">الآن</span>
            </div>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ session('success') }}</p>
        </div>
        <button onclick="dismissToast()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <!-- Interactive Receipt Modal (Appears when session contains receipt) -->
    @if(session('receipt'))
    @php $rcpt = session('receipt'); @endphp
    <div id="receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden animate-scaleIn print:m-0 print:border-none print:shadow-none">
            <!-- Receipt Header -->
            <div class="bg-gradient-to-tr from-teal-800 to-teal-600 text-white p-6 text-center relative">
                <button onclick="closeReceiptModal()" class="absolute top-4 left-4 text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition print:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <h3 class="text-base font-bold">{{ $rcpt['title'] ?? 'إيصال وسند معاملة مالية' }}</h3>
                <p class="text-xs text-teal-100 mt-1">محفظتي للخدمات النقدية — شبكة الوكلاء المعتمدين</p>
                <div class="mt-4 bg-white/15 backdrop-blur-xs rounded-2xl py-3 px-4 inline-block border border-white/20">
                    <span class="num-font text-2xl font-black text-white">{{ number_format($rcpt['amount'], 2) }}</span>
                    <span class="text-sm font-bold text-teal-200 mr-1">{{ $rcpt['currency'] }}</span>
                </div>
            </div>

            <!-- Receipt Body Breakdown -->
            <div class="p-6 space-y-3.5 text-xs">
                @if(isset($rcpt['user_name']))
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">العميل / المستفيد:</span>
                    <span class="font-bold text-slate-900">{{ $rcpt['user_name'] }}</span>
                </div>
                @endif

                @if(isset($rcpt['user_phone']))
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">رقم هاتف العميل:</span>
                    <span class="num-font font-bold text-slate-800">{{ $rcpt['user_phone'] }}</span>
                </div>
                @endif

                @if(isset($rcpt['recipient_name']))
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">المستلم:</span>
                    <span class="font-bold text-slate-900">{{ $rcpt['recipient_name'] }}</span>
                </div>
                @endif

                @if(isset($rcpt['sender_name']))
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">المرسل:</span>
                    <span class="font-bold text-slate-900">{{ $rcpt['sender_name'] }}</span>
                </div>
                @endif

                @if(isset($rcpt['remittance_code']))
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">رقم الحوالة:</span>
                    <span class="num-font font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">{{ $rcpt['remittance_code'] }}</span>
                </div>
                @endif

                @if(isset($rcpt['agent_commission']) && $rcpt['agent_commission'] > 0)
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-emerald-700 font-medium">أرباح عمولة الوكيل:</span>
                    <span class="num-font font-bold text-emerald-700">+{{ number_format($rcpt['agent_commission'], 2) }} {{ $rcpt['currency'] }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">رقم المرجع المحاسبي:</span>
                    <span id="rcpt-ref" class="num-font font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">{{ $rcpt['reference'] }}</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">مركز الوكيل المنفذ:</span>
                    <span class="font-bold text-slate-800">{{ $rcpt['agent_name'] ?? session('agent_name') }}</span>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-500 font-medium">تاريخ ووقت التنفيذ:</span>
                    <span class="num-font text-slate-600">{{ $rcpt['date'] ?? now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <!-- Receipt Actions -->
            <div class="p-5 bg-slate-50 border-t border-slate-100 flex items-center gap-3 print:hidden">
                <button onclick="window.print()" class="flex-1 bg-teal-700 hover:bg-teal-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
                    <span>طباعة السند</span>
                </button>
                <button onclick="copyReference()" class="bg-white hover:bg-slate-100 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-xs border border-slate-200 transition">
                    نسخ المرجع
                </button>
                <button onclick="closeReceiptModal()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-xs transition">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Clean Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-4 text-center text-xs text-slate-400 mt-auto">
        <p>نظام محفظتي للخدمات النقدية والتحويلات — بوابة الوكلاء المعتمدين © 2026</p>
    </footer>

    <!-- Toast & Modal Interaction Script -->
    <script>
        function showAppToast(title, message, type = 'warning') {
            let container = document.getElementById('global-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'global-toast-container';
                container.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-md px-4 pointer-events-none';
                document.body.appendChild(container);
            }

            const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
            const bgMap = {
                'warning': 'bg-amber-50 border-amber-200/90 text-amber-950',
                'error': 'bg-rose-50 border-rose-200/90 text-rose-950',
                'success': 'bg-emerald-50 border-emerald-200/90 text-emerald-950',
                'info': 'bg-blue-50 border-blue-200/90 text-blue-950',
            };
            const iconMap = {
                'warning': '<div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-xs"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg></div>',
                'error': '<div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-xs"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></div>',
                'success': '<div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-xs"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></div>',
                'info': '<div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-xs"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg></div>',
            };

            const card = document.createElement('div');
            card.id = toastId;
            card.className = `pointer-events-auto border shadow-xl rounded-2xl p-4 flex items-center justify-between gap-3 transition-all duration-300 transform translate-y-0 opacity-100 ${bgMap[type] || bgMap['warning']}`;
            card.innerHTML = `
                <div class="flex items-center gap-3">
                    ${iconMap[type] || iconMap['warning']}
                    <div>
                        <span class="font-bold text-xs block leading-tight">${title}</span>
                        <span class="text-[11px] opacity-90 leading-tight">${message}</span>
                    </div>
                </div>
                <button onclick="document.getElementById('${toastId}').remove()" class="p-1 rounded-lg hover:bg-black/5 transition text-current opacity-60 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            `;

            container.appendChild(card);
            setTimeout(() => {
                const el = document.getElementById(toastId);
                if (el) {
                    el.classList.add('opacity-0', '-translate-y-2');
                    setTimeout(() => el.remove(), 300);
                }
            }, 4500);
        }

        function dismissToast() {
            const toast = document.getElementById('live-toast');
            if (toast) {
                toast.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => toast.remove(), 300);
            }
        }

        function closeReceiptModal() {
            const modal = document.getElementById('receipt-modal');
            if (modal) modal.remove();
        }

        function copyReference() {
            const refElem = document.getElementById('rcpt-ref');
            if (refElem) {
                navigator.clipboard.writeText(refElem.innerText).then(() => {
                    showAppToast('تم النسخ بنجاح', 'تم نسخ رقم المرجع المحاسبي: ' + refElem.innerText, 'success');
                });
            }
        }

        function showConfirmDialog({ title = 'تأكيد العملية', message = 'هل أنت متأكد من تنفيذ هذا الإجراء؟', confirmText = 'نعم، تأكيد', confirmType = 'danger', onConfirm = null }) {
            let modal = document.getElementById('global-agent-confirm-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'global-agent-confirm-modal';
                modal.className = 'fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4';
                document.body.appendChild(modal);
            }
            const isDanger = confirmType === 'danger';
            const headerBg = isDanger ? 'bg-rose-600' : 'bg-slate-900';
            const btnBg = isDanger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-slate-900 hover:bg-slate-800';

            modal.innerHTML = `
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all animate-in fade-in zoom-in duration-150">
                    <div class="${headerBg} text-white px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">⚠️</span>
                            <h3 class="text-xs font-bold">${title}</h3>
                        </div>
                        <button type="button" onclick="document.getElementById('global-agent-confirm-modal').classList.add('hidden')" class="text-white/70 hover:text-white text-lg font-bold p-1 rounded-lg">
                            &times;
                        </button>
                    </div>
                    <div class="p-5 text-xs text-slate-700 leading-relaxed font-medium">
                        ${message}
                    </div>
                    <div class="bg-slate-50 px-5 py-3.5 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('global-agent-confirm-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition">
                            إلغاء
                        </button>
                        <button type="button" id="global-agent-confirm-btn" class="px-4 py-2 ${btnBg} text-white text-xs font-bold rounded-xl transition shadow-sm">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            document.getElementById('global-agent-confirm-btn').onclick = function() {
                modal.classList.add('hidden');
                if (typeof onConfirm === 'function') onConfirm();
            };
        }

        // Auto dismiss live toast after 6 seconds
        setTimeout(dismissToast, 6000);
    </script>

</body>
</html>
