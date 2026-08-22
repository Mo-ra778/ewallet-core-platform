<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-slate-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'الرقابة المالية') — الإدارة المركزية</title>
    
    <!-- Modern Typography: IBM Plex Sans Arabic for Arabic UI + Plus Jakarta Sans for Financial Numerals -->
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
                        'dropdown': '0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04)',
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
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>
</head>
<body class="text-slate-800 min-h-full flex antialiased selection:bg-brand-600 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

    <!-- Ultra-Modern Sticky Sidebar -->
    <aside id="main-sidebar" class="fixed lg:sticky top-0 right-0 h-screen w-72 bg-white border-l border-slate-200/80 flex flex-col justify-between z-50 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out shadow-sm lg:shadow-none">
        
        <div class="flex flex-col h-full overflow-hidden">
            <!-- Brand & Live System Status -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-800 to-brand-600 text-white flex items-center justify-center shadow-md shadow-brand-700/20 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m-19.5 0v10.5m19.5-10.5v10.5m0 0a60.068 60.068 0 0 0-15.797-2.101c-.727-.198-1.453.342-1.453 1.096V18.75m17.25-14.25v.75a.75.75 0 0 0 .75.75H21.75m-19.5 0h19.5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-sm font-bold text-slate-900 tracking-tight">محفظتي للأعمال</h1>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                حي
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium block">لوحة الإدارة والرقابة المركزية</span>
                    </div>
                </div>

                <!-- Close mobile drawer button -->
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links Organized by Sections -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
                
                <!-- Section 1: Operations & Core Metrics -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">الرقابة والسيولة</span>
                    
                    <a href="{{ route('admin.dashboard') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.dashboard'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                        <span>لوحة الرقابة والمؤشرات</span>
                    </a>

                    <a href="{{ route('admin.transactions') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.transactions') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.transactions'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.transactions') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                        <span>دفتر الأستاذ العام (Audit)</span>
                    </a>

                    <a href="{{ route('admin.balance.form') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.balance.*') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.balance.*'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.balance.*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                        <span>التسويات والتغذية المباشرة</span>
                    </a>
                </div>

                <!-- Section 2: Accounts & Entities -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">الحسابات والوكلاء</span>

                    <a href="{{ route('admin.users') }}" class="group relative flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.users*'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.users*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                            <span>حسابات المستخدمين</span>
                        </div>
                        @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="num-font text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300/60 animate-bounce">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.agents') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.agents') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.agents'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.agents') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                        <span>شبكة الوكلاء المعتمدين</span>
                    </a>
                </div>

                <!-- Section 3: Communications & Alerts -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">التواصل والأمان</span>

                    <a href="{{ route('admin.notifications') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.notifications') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.notifications'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.notifications') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        <span>مركز بث التنبيهات والإشعارات</span>
                    </a>
                </div>

                <!-- Section 4: System Control & Exchange Rates -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">التهيئة والسياسات</span>

                    <a href="{{ route('admin.settings') }}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.settings*') ? 'bg-brand-50 text-brand-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        @if(request()->routeIs('admin.settings*'))
                            <span class="absolute right-0 top-2 bottom-2 w-1 bg-brand-600 rounded-l-full"></span>
                        @endif
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.settings*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <span>إعدادات الصرف والعمولات</span>
                    </a>
                </div>

            </div>

            <!-- Admin Profile Bottom Card -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="min-w-0 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            {{ mb_substr(session('admin_username', 'A'), 0, 1) }}
                        </div>
                        <div class="truncate">
                            <span class="text-xs font-bold text-slate-900 block truncate">{{ session('admin_username', 'مدير النظام') }}</span>
                            <span class="text-[10px] text-brand-700 font-semibold block">مشرف عام النظام</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="تسجيل الخروج" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </aside>

    <!-- Main Content Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Seamless Glassmorphic Sticky Header -->
        <header class="h-20 bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between px-6 lg:px-10 transition-all">
            
            <div class="flex items-center gap-4">
                <!-- Mobile Toggle Hamburger -->
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl border border-slate-200 bg-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>

                <div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                        <span>الرئيسية</span>
                        <span>/</span>
                        <span class="text-brand-700 font-semibold">@yield('page_title', 'لوحة التحكم')</span>
                    </div>
                    <h2 class="text-sm lg:text-base font-bold text-slate-900">@yield('title', 'الرقابة المالية')</h2>
                </div>
            </div>
            
            <!-- Quick Actions & Global Indicators -->
            <div class="flex items-center gap-3">
                <!-- Live Server Ping Badge -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/80 text-[11px] font-medium text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>الخادم: متصل ومستقر</span>
                </div>

                <!-- Live Arabic Date -->
                <span class="hidden md:inline-block text-[11px] text-slate-500 num-font border border-slate-200/80 px-3 py-1.5 rounded-lg bg-white shadow-xs">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>

                <!-- Quick Jump to Cashier Terminal -->
                <a href="{{ route('agent.login.form') }}" target="_blank" class="text-xs font-semibold text-brand-800 bg-brand-50 hover:bg-brand-100 border border-brand-200/80 px-3.5 py-2 rounded-xl transition flex items-center gap-2 shadow-xs">
                    <span>محطة الوكيل</span>
                    <svg class="w-3.5 h-3.5 text-brand-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            </div>

        </header>

        <!-- Main Body Workspace -->
        <main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto space-y-6">
            
            <!-- Global Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-soft animate-fadeIn">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50/90 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-xs space-y-2 shadow-soft">
                    <div class="font-bold flex items-center gap-2">
                        <div class="w-7 h-7 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        </div>
                        <span>تنبيه بوجود أخطاء في المدخلات:</span>
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

    </div>

    <!-- Quick Details Modal Container (Reusable across views) -->
    <div id="tx-details-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden animate-scaleIn">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">سند وتفاصيل المعاملة المالية</h3>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="modal-content" class="p-6 space-y-4 text-xs">
                <!-- Injected via JavaScript -->
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 text-left">
                <button onclick="closeModal()" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold px-4 py-2 rounded-xl text-xs transition">إغلاق</button>
            </div>
        </div>
    </div>

    <!-- Interactive Drawer & Modal Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }

        function showTxDetails(data) {
            const modal = document.getElementById('tx-details-modal');
            const content = document.getElementById('modal-content');
            
            content.innerHTML = `
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block">معرف الحركة (UUID)</span>
                        <span class="num-font text-xs text-slate-700 font-bold break-all">${data.id}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block">التوقيت والتاريخ</span>
                        <span class="num-font text-xs text-slate-700 font-semibold">${data.created_at}</span>
                    </div>
                </div>

                <div class="space-y-2 pt-1">
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">نوع الحركة:</span>
                        <span class="font-bold text-slate-900">${data.type_label}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">المبلغ والعملة:</span>
                        <span class="text-sm font-bold text-brand-700 num-font">${data.amount} ${data.currency}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">الطرف الأول (العميل):</span>
                        <span class="font-semibold text-slate-800">${data.user_name || '—'}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">الطرف المقابل (الوكيل / المشرف):</span>
                        <span class="font-semibold text-slate-800">${data.counterparty || 'مباشر'}</span>
                    </div>
                    <div class="flex flex-col gap-1 py-1.5">
                        <span class="text-slate-500 font-medium">البيان والسبب الرقابي:</span>
                        <span class="text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100">${data.description}</span>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('tx-details-modal').classList.add('hidden');
        }
    </script>

</body>
</html>
