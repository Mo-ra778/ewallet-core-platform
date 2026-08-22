<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الوكيل المعتمد — محفظتي للخدمات النقدية</title>
    
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
                </svg>
            </div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">محطة الوكيل المعتمد</h1>
            <p class="text-xs text-slate-500">خدمات الإيداع والسحب النقدي المباشر</p>
        </div>

        <!-- Pure Light Auth Card -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
            @if($errors->any() && !$errors->has('phone') && !$errors->has('password'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs px-3.5 py-2.5 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('agent.login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">رقم هاتف الوكيل <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', '777000111') }}" required autofocus dir="ltr"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none transition text-right font-mono @error('phone') border-rose-400 ring-2 ring-rose-500/20 bg-rose-50/30 @else border-slate-200 focus:ring-2 focus:ring-teal-700/20 focus:border-teal-700 @enderror">
                    @error('phone')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">كلمة المرور <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" value="agent123" required
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
                    فتح المحطة وتسجيل الدخول
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('admin.login.form') }}" class="text-xs text-slate-500 hover:text-teal-700 font-medium transition flex items-center justify-center gap-1.5">
                <span>بوابة الإدارة المركزية</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
        </div>
    </div>

</body>
</html>
