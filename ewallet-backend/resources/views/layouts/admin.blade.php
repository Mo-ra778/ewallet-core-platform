<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'الرقابة المالية') — الإدارة المركزية</title>
    
    <!-- Professional Typography: IBM Plex Sans Arabic for UI, Plus Jakarta Sans for Tabular Financial Numbers -->
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
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    </style>
</head>
<body class="bg-canvas text-ink-900 h-full flex antialiased selection:bg-fin-teal selection:text-white">

    <!-- Pure Light Corporate Sidebar -->
    <aside class="w-64 bg-surface border-l border-line flex flex-col justify-between flex-shrink-0 z-30">
        <div class="flex flex-col h-full">
            <!-- Institutional Identity Header -->
            <div class="h-16 px-5 flex items-center gap-3 border-b border-line">
                <div class="w-8 h-8 rounded-lg bg-fin-teal text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m-19.5 0v10.5m19.5-10.5v10.5m0 0a60.068 60.068 0 0 0-15.797-2.101c-.727-.198-1.453.342-1.453 1.096V18.75m17.25-14.25v.75a.75.75 0 0 0 .75.75H21.75m-19.5 0h19.5"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xs font-bold text-ink-900 tracking-tight truncate">محفظتي للأعمال</h1>
                    <span class="text-[10px] text-ink-500 font-medium block">الإدارة المركزية والرقابة</span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="p-3 flex-1 overflow-y-auto space-y-5">
                <div>
                    <span class="px-2 text-[10px] font-semibold text-ink-400 uppercase tracking-wider block mb-1.5">العمليات والرقابة</span>
                    <nav class="space-y-0.5 text-xs font-medium">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            <span>لوحة الرقابة والسيولة</span>
                        </a>

                        <a href="{{ route('admin.users') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                <span>حسابات المستخدمين</span>
                            </div>
                            @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="num-font text-[10px] font-bold px-1.5 py-0.2 rounded {{ request()->routeIs('admin.users*') ? 'bg-white/20 text-white' : 'bg-fin-amberBg text-fin-amber border border-fin-amberLine' }}">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.agents') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.agents') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.agents') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                            <span>شبكة الوكلاء المعتمدين</span>
                        </a>

                        <a href="{{ route('admin.balance.form') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.balance.*') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.balance.*') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                            <span>التسويات والتغذية المباشرة</span>
                        </a>

                        <a href="{{ route('admin.transactions') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.transactions') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.transactions') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                            <span>السجل المالي العام (Audit)</span>
                        </a>

                        <a href="{{ route('admin.notifications') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.notifications') ? 'bg-fin-teal text-white font-semibold shadow-sm' : 'text-ink-700 hover:bg-subtle hover:text-ink-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.notifications') ? 'text-white' : 'text-ink-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                            </svg>
                            <span>مركز التنبيهات والإشعارات</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Admin Profile Badge -->
            <div class="p-3 border-t border-line">
                <div class="flex items-center justify-between p-2 rounded-lg bg-subtle/70 border border-line">
                    <div class="min-w-0 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-md bg-white border border-line text-ink-900 flex items-center justify-center font-bold text-xs">
                            {{ mb_substr(session('admin_username', 'A'), 0, 1) }}
                        </div>
                        <div class="truncate">
                            <span class="text-xs font-semibold text-ink-900 block truncate">{{ session('admin_username', 'مدير النظام') }}</span>
                            <span class="text-[10px] text-fin-teal font-medium block">مشرف عام النظام</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="تسجيل الخروج" class="text-ink-400 hover:text-fin-ruby p-1.5 rounded transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Clean Light Top Bar -->
        <header class="h-16 bg-surface border-b border-line sticky top-0 z-20 flex items-center justify-between px-6">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-ink-900">@yield('page_title', 'لوحة التحكم')</span>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-[11px] text-ink-500 num-font border border-line px-2.5 py-1 rounded-md bg-subtle/50">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <a href="{{ route('agent.login.form') }}" target="_blank" class="text-xs font-semibold text-fin-teal hover:text-ink-900 border border-line hover:border-lineHover bg-surface px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    <span>محطة الوكيل</span>
                    <svg class="w-3.5 h-3.5 text-fin-teal" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            </div>
        </header>

        <!-- View Body -->
        <main class="flex-1 p-6 sm:p-8 max-w-7xl w-full mx-auto space-y-6">
            @if(session('success'))
                <div class="bg-fin-tealBg border border-fin-tealLine text-fin-teal px-4 py-3 rounded-lg text-xs font-medium flex items-center justify-between shadow-sm animate-fadeIn">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-fin-teal flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-fin-rubyBg border border-fin-rubyLine text-fin-ruby px-4 py-3 rounded-lg text-xs space-y-1 shadow-sm">
                    <div class="font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-fin-ruby flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        <span>تنبيه خطأ في المدخلات:</span>
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
    </div>

</body>
</html>
