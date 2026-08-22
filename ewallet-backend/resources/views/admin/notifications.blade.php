@extends('layouts.admin')

@section('title', 'مركز الإشعارات والتنبيهات')
@section('page_title', 'مركز بث الإشعارات والتنبيهات المباشرة')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Dispatch Form -->
        <div class="lg:col-span-1 bg-surface-card p-5 rounded-lg border border-surface-border h-fit space-y-4">
            <div class="pb-3 border-b border-surface-border flex items-center justify-between">
                <h3 class="text-xs font-bold text-ink-primary">بث إشعار فوري لمستخدم</h3>
                <span class="text-[10px] text-ink-muted font-mono">PUSH ALERT</span>
            </div>

            <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">المستخدم المستهدف</label>
                    <select name="user_id" required class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                        <option value="">-- اختر المستخدم --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">عنوان الإشعار</label>
                    <input type="text" name="title" required placeholder="مثال: تنبيه أمني، إشعار تحديث بيانات"
                           class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">نوع التنبيه</label>
                    <select name="type" class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                        <option value="alert">تنبيه إداري (Alert)</option>
                        <option value="message">رسالة عامة (Message)</option>
                        <option value="transaction">إشعار حركة مالية (Transaction)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink-primary mb-1">نص الرسالة</label>
                    <textarea name="message" rows="3" required placeholder="اكتب نص الإشعار هنا..."
                              class="w-full px-3 py-1.5 text-xs bg-surface-base border border-surface-border rounded text-ink-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition"></textarea>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-medium py-2 px-3 rounded text-xs transition mt-2">
                    إرسال الإشعار لتطبيق العميل
                </button>
            </form>
        </div>

        <!-- Notifications Log Table -->
        <div class="lg:col-span-2 bg-surface-card rounded-lg border border-surface-border overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-ink-primary">سجل الإشعارات المرسلة في النظام</h3>
                    <p class="text-[11px] text-ink-muted mt-0.5">تتبع التنبيهات، رسائل التحقق (OTP)، والإشعارات الإدارية</p>
                </div>
                <span class="text-xs font-mono text-ink-muted font-semibold">{{ $notifications->total() }} إشعار</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold border-b border-surface-border">
                        <tr>
                            <th class="py-3 px-4">نوع الإشعار</th>
                            <th class="py-3 px-4">العنوان والرسالة</th>
                            <th class="py-3 px-4">المستلم</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">التوقيت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-border">
                        @forelse($notifications as $n)
                        <tr class="hover:bg-surface-base/80 transition">
                            <td class="py-3 px-4">
                                @if($n->type === 'otp')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-fin-amberBg text-fin-amber border border-fin-amberBorder">
                                        رمز OTP
                                    </span>
                                @elseif($n->type === 'transaction')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-fin-tealBg text-fin-teal border border-fin-tealBorder">
                                        معاملة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-surface-subtle text-ink-primary border border-surface-border">
                                        تنبيه
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 max-w-sm">
                                <div class="font-semibold text-ink-primary">{{ $n->title }}</div>
                                <div class="text-ink-secondary text-[11px] mt-0.5 leading-relaxed">{{ $n->message }}</div>
                            </td>
                            <td class="py-3 px-4 text-ink-secondary font-medium">
                                {{ $n->recipient_type === 'user' ? 'عميل' : 'وكيل' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($n->is_read)
                                    <span class="text-ink-muted text-[11px]">مقروء</span>
                                @else
                                    <span class="text-fin-teal font-semibold text-[11px]">جديد</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 num-font text-ink-muted">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-ink-muted">لا توجد إشعارات مرسلة في النظام.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
            <div class="p-3.5 border-t border-surface-border">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
