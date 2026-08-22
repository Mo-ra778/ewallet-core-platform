@extends('layouts.agent')

@section('title', 'مركز الإشعارات والتنبيهات')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Notifications Header Card -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">مركز الإشعارات والتنبيهات المباشرة</h2>
                <p class="text-xs text-slate-400 mt-0.5">تنبيهات العمليات النقدية، التغذيات الإدارية، والرسائل الرقابية</p>
            </div>
        </div>

        @if($unreadCount > 0)
        <form action="{{ route('agent.notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-xs">
                تحديد الكل كمقروء
            </button>
        </form>
        @endif
    </div>

    <!-- Notifications Stream List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden divide-y divide-slate-100">
        @forelse($notifications as $notif)
        <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-slate-50/60 {{ !$notif->is_read ? 'bg-teal-50/20' : '' }}">
            <div class="flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ $notif->type === 'alert' ? 'bg-amber-50 text-amber-700' : ($notif->type === 'transaction' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
                    @if($notif->type === 'alert')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    @elseif($notif->type === 'transaction')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 49.188 49.188 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.77Z"/></svg>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-bold text-slate-900">{{ $notif->title }}</h4>
                        @if(!$notif->is_read)
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[10px] text-slate-400 num-font block pt-1">{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('Y-m-d H:i') }})</span>
                </div>
            </div>

            @if(!$notif->is_read)
            <form action="{{ route('agent.notifications.read', $notif->id) }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit" class="text-[11px] text-teal-700 hover:text-teal-900 font-semibold p-1.5 rounded-lg hover:bg-teal-50 transition" title="تحديد كمقروء">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="p-12 text-center text-slate-400 text-xs">
            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
            لا توجد إشعارات جديدة في صندوق الوكيل حالياً.
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-soft">
        {{ $notifications->links() }}
    </div>
    @endif

</div>
@endsection
