<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المشرف — محفظتي للأعمال</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'IBM Plex Sans Arabic', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-full flex items-center justify-center p-4 antialiased selection:bg-teal-700 selection:text-white">

    <div class="max-w-sm w-full space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-teal-700 text-white flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">محفظتي للأعمال</h1>
            <p class="text-xs text-slate-500">بوابة الإدارة المركزية والرقابة المالية</p>
        </div>

        <!-- Pure Light Auth Card -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
            @if($errors->any() && !$errors->has('username') && !$errors->has('password'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs px-3.5 py-2.5 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">اسم المستخدم للمشرف <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username', 'admin') }}" required autofocus
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition @error('username') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200 focus:ring-2 focus:ring-teal-700/20 focus:border-teal-700 @enderror">
                    @error('username')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">كلمة المرور <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" value="admin123" required
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition @error('password') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200 focus:ring-2 focus:ring-teal-700/20 focus:border-teal-700 @enderror">
                    @error('password')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition text-xs mt-2">
                    تسجيل الدخول للنظام
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('agent.login.form') }}" class="text-xs text-slate-500 hover:text-teal-700 font-medium transition flex items-center justify-center gap-1.5">
                <span>الانتقال لمحطة الوكلاء المعتمدين</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
        </div>
    </div>

</body>
</html>
