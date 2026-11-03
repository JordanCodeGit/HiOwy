@props(['notifications'])
<div class="relative" id="notificationWrapper">
    <button type="button" @click="notificationsOpen = !notificationsOpen; profileOpen = false" :aria-expanded="notificationsOpen" aria-controls="notificationDropdown" aria-label="Notifikasi ringkasan kelas" class="relative w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 bg-slate-100 hover:bg-slate-200/70 transition-colors">
        <span class="material-symbols-outlined text-[20px]">notifications</span>
        @if(count($notifications))<span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-blue-500 ring-2 ring-white"></span>@endif
    </button>
    <div id="notificationDropdown" x-cloak x-show="notificationsOpen" @click.outside="notificationsOpen = false" x-transition.opacity class="fixed left-4 right-4 top-16 sm:absolute sm:left-auto sm:top-full sm:right-0 sm:mt-2.5 sm:w-[380px] rounded-2xl bg-white shadow-2xl border border-slate-200/80 z-50 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-wrap justify-between items-center gap-2"><div class="flex items-center gap-2"><h2 class="text-sm font-bold text-slate-900">Notifikasi</h2><span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-blue-700">{{ count($notifications) }} Baru</span></div><button type="button" @click="unavailable('Penandaan semua notifikasi dibaca')" class="text-[11px] font-semibold text-blue-600 hover:underline">Tandai Semua Dibaca</button></div>
        <p class="border-b border-slate-100 px-4 py-3 text-xs font-semibold text-slate-500">Ringkasan diteruskan · Kelas 8B</p>
        <div class="divide-y divide-slate-100 max-h-[min(360px,60dvh)] overflow-y-auto">
            @forelse($notifications as $notification)
            <a href="{{ route('walas.summary', $notification['id']) }}" class="flex items-start gap-3 p-3.5 hover:bg-blue-50/50 transition-colors"><span class="material-symbols-outlined shrink-0 rounded-lg border border-blue-100 bg-blue-50 p-2 text-[18px] text-blue-600">forward_to_inbox</span><div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wide text-blue-600">Ringkasan Baru</p><p class="mt-1 text-xs font-semibold text-slate-900">{{ $notification['counselor'] }}</p><p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-600">{{ $notification['content'] }}</p><p class="mt-2 text-[10px] text-slate-400">{{ $notification['time'] }}</p></div></a>
            @empty<p class="p-6 text-sm text-center text-slate-500">Belum ada notifikasi ringkasan.</p>@endforelse
        </div>
        <div class="p-3 border-t border-slate-100 bg-slate-50/70 text-center"><a href="{{ route('walas.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600">Lihat Semua Ringkasan<span class="material-symbols-outlined text-base">arrow_forward</span></a></div>
    </div>
</div>
