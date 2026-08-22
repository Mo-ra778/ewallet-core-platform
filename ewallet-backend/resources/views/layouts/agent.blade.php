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
            <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-xl text-xs flex items-center justify-between shadow-soft">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Clean Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-4 text-center text-xs text-slate-400 mt-auto">
        <p>نظام محفظتي للخدمات النقدية والتحويلات — بوابة الوكلاء المعتمدين © 2026</p>
    </footer>

</body>
</html>
