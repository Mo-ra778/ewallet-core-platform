@extends('layouts.admin')

@section('title', 'مركز بث التنبيهات والإشعارات')
@section('page_title', 'إرسال التنبيهات المباشرة وسجل الإشعارات')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Broadcast Form (1 Column) -->
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-soft space-y-5">
            <div class="pb-4 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-900">بث إشعار فوري جديد</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">إرسال تنبيهات لحظية تظهر داخل تطبيق المستخدمين</p>
            </div>

            <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">الجهة المستهدفة</label>
                    <select name="user_id" class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="">-- بث عام لكافة المستخدمين (Broadcast All) --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">نوع التنبيه</label>
                    <select name="type" class="w-full px-3.5 py-2 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                        <option value="alert">تنبيه هام (Alert)</option>
                        <option value="message">رسالة إدارية (Message)</option>
                        <option value="transaction">إشعار حركة (Transaction)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">عنوان الإشعار</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="مثال: تحديث أمني / إيداع رصيد تشجيعي" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">نص ومحتوى الإشعار</label>
                    <textarea name="message" rows="4" required placeholder="اكتب نص الرسالة التي ستصل للعميل في التطبيق..." 
                              class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200/80 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-700/20 focus:border-brand-700 transition"></textarea>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition shadow-xs mt-2 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    <span>إرسال وبث الإشعار فوراً</span>
                </button>
            </form>
        </div>

        <!-- Notification History Log Table (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">سجل الإشعارات والتنبيهات المرسلة</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">تتبع وصول الإشعارات وحالة القراءة</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="py-4 px-6">العنوان والتصنيف</th>
                            <th class="py-4 px-6">المحتوى والتفاصيل</th>
                            <th class="py-4 px-6">المستلم</th>
                            <th class="py-4 px-6">حالة القراءة</th>
                            <th class="py-4 px-6">التوقيت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($notifications as $notif)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $notif->title }}</div>
                                <span class="inline-flex items-center text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full mt-1">
                                    {{ $notif->type }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 max-w-xs truncate">{{ $notif->message }}</td>
                            <td class="py-4 px-6">
                                <span class="text-slate-800 font-medium">
                                    {{ $notif->recipient ? $notif->recipient->full_name : ($notif->recipient_type === 'user' ? 'عميل' : 'وكيل') }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($notif->is_read)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        مقروء
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        غير مقروء
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 num-font text-slate-400">{{ $notif->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">لا توجد إشعارات مسجلة في السجل.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
