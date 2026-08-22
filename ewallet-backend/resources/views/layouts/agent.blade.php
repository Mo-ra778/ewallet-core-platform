<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'محطة الوكيل المعتمد') — محفظتي للخدمات النقدية</title>
    
    <!-- Professional Typography -->
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
                        canvas: '#F8FAFC',
                        surface: '#FFFFFF',
                        subtle: '#F1F5F9',
                        line: '#E2E8F0',
                        lineHover: '#CBD5E1',
                        ink: {
                            900: '#0F172A',
                            700: '#334155',
                            500: '#64748B',
                            400: '#94A3B8',
                        },
                        fin: {
                            teal: '#0F766E',
                            tealBg: '#F0FDFA',
                            tealLine: '#CCFBF1',
                            amber: '#B45309',
                            amberBg: '#FFFBEB',
                            amberLine: '#FEF3C7',
                            ruby: '#BE123C',
                            rubyBg: '#FFF1F2',
                            rubyLine: '#FFE4E6',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .num-font { font-family: 'Plus Jakarta Sans', monospace; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-canvas text-ink-900 min-h-full flex flex-col antialiased selection:bg-fin-teal selection:text-white">

    <!-- Top Pure Light Cashier Navbar -->
    <header class="bg-surface border-b border-line sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand Identity -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-fin-teal text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                    </div>
                    <div class="leading-tight">
                        <span class="text-xs font-bold text-ink-900 block">محطة الوكيل المعتمد</span>
                        <span class="text-[10px] text-ink-500 font-medium block">خدمات الإيداع والسحب النقدي</span>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <nav class="hidden md:flex items-center gap-1.5 text-xs font-medium">
                    <a href="{{ route('agent.dashboard') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('agent.dashboard') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-subtle' }}">
                        لوحة المؤشرات
                    </a>
                    <a href="{{ route('agent.deposit.form') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('agent.deposit.*') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:text-fin-teal hover:bg-subtle' }}">
                        إيداع نقدي (Cash-In)
                    </a>
                    <a href="{{ route('agent.withdraw.form') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('agent.withdraw.*') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:text-fin-amber hover:bg-subtle' }}">
                        سحب نقدي (Cash-Out OTP)
                    </a>
                    <a href="{{ route('agent.transactions') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('agent.transactions') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-subtle' }}">
                        سجل العمليات
                    </a>
                </nav>

                <!-- Liquidity Badge & Session -->
                <div class="flex items-center gap-3">
                    @if(isset($agent))
                    <div class="hidden sm:flex items-center gap-2 bg-subtle/80 border border-line px-3 py-1.5 rounded-lg text-xs">
                        <span class="text-[11px] text-ink-500 font-medium">الرصيد المتاح:</span>
                        <span class="font-bold text-ink-900 num-font text-sm">{{ number_format($agent->balance, 2) }}</span>
                        <span class="text-[10px] text-ink-500">ر.ي</span>
                    </div>
                    @endif

                    <form action="{{ route('agent.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="خروج" class="text-xs font-medium text-ink-500 hover:text-fin-ruby p-2 border border-line rounded-lg hover:bg-subtle transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                            <span class="hidden sm:inline">خروج</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 sm:p-8 space-y-6">
        @if(session('success'))
            <div class="bg-fin-tealBg border border-fin-tealLine text-fin-teal px-4 py-3 rounded-lg text-xs font-medium flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-fin-teal flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-fin-rubyBg border border-fin-rubyLine text-fin-ruby px-4 py-3 rounded-lg text-xs space-y-1 shadow-sm">
                <div class="font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-fin-ruby flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <span>تنبيه بالخطأ:</span>
                </div>
                <ul class="list-disc list-inside pr-6 text-ink-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Minimal Functional Footer -->
    <footer class="border-t border-line bg-surface py-3 text-center text-[11px] text-ink-400">
        نظام التحويلات والخدمات المصرفية المعتمدة &copy; {{ date('Y') }}
    </footer>

</body>
</html>
