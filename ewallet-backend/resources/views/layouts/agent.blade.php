<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-slate-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'محطة الوكيل المعتمد') — محفظتي للخدمات النقدية</title>
    
    <!-- Modern Typography: IBM Plex Sans Arabic + Plus Jakarta Sans for Financial Numerals -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'card': '0 0 0 1px rgba(226, 232, 240, 0.8), 0 1px 3px 0 rgba(0, 0, 0, 0.02)',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .num-font { font-family: 'Plus Jakarta Sans', monospace; font-variant-numeric: tabular-nums; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 6px; }
    </style>
</head>
<body class="text-slate-800 min-h-full flex flex-col antialiased selection:bg-brand-600 selection:text-white">

    <!-- Seamless Sticky Cashier Header -->
    <header class="bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center gap-4">
                
                <!-- Brand & Terminal Identity -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-800 to-brand-600 text-white flex items-center justify-center shadow-md shadow-brand-700/20 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-sm font-bold text-slate-900 tracking-tight">محطة الوكيل المعتمد</h1>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                متصل
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium block">خدمات الإيداع والتسليم النقدي الفوري</span>
                    </div>
                </div>

                <!-- Navigation Tabs with Soft Pill Indicators -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ route('agent.dashboard') }}" class="px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('agent.dashboard') ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        لوحة المؤشرات
                    </a>
                    <a href="{{ route('agent.deposit.form') }}" class="px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('agent.deposit.*') ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/60 shadow-xs' : 'text-slate-600 hover:text-brand-700 hover:bg-slate-50' }}">
                        إيداع نقدي (Cash-In)
                    </a>
                    <a href="{{ route('agent.withdraw.form') }}" class="px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('agent.withdraw.*') ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200/60 shadow-xs' : 'text-slate-600 hover:text-amber-700 hover:bg-slate-50' }}">
                        سحب نقدي (Cash-Out OTP)
                    </a>
                    <a href="{{ route('agent.transactions') }}" class="px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('agent.transactions') ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/60 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        سجل العمليات
                    </a>
                </nav>

                <!-- Agent Liquidity Card & Logout -->
                <div class="flex items-center gap-3">
                    @if(isset($agent))
                    <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200/80 px-4 py-2 rounded-xl text-xs shadow-xs">
                        <span class="text-slate-400 font-medium text-[11px]">الرصيد المتاح:</span>
                        <span class="font-bold text-slate-900 num-font text-sm">{{ number_format($agent->balance, 2) }}</span>
                        <span class="text-[10px] text-slate-400 font-bold">ر.ي</span>
                    </div>
                    @endif

                    <form action="{{ route('agent.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="خروج" class="text-xs font-semibold text-slate-500 hover:text-rose-600 p-2.5 border border-slate-200/80 rounded-xl hover:bg-rose-50 transition flex items-center gap-1.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                            <span class="hidden sm:inline">خروج</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 sm:p-8 lg:p-10 space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-semibold flex items-center gap-3 shadow-soft">
                <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50/90 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-xs space-y-2 shadow-soft">
                <div class="font-bold flex items-center gap-2">
                    <div class="w-7 h-7 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    </div>
                    <span>تنبيه بوجود أخطاء:</span>
                </div>
                <ul class="list-disc list-inside pr-9 text-slate-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Minimal Functional Footer -->
    <footer class="border-t border-slate-200/80 bg-white py-4 text-center text-xs text-slate-400">
        محفظتي للأعمال &bull; شبكة الخدمات المالية والمصرفية المعتمدة &copy; {{ date('Y') }}
    </footer>

</body>
</html>
