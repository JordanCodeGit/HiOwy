@props(['page', 'portal' => 'bk', 'reportCount' => 14])

<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" aria-hidden="true"></div>
<aside id="bk-sidebar" class="bk-sidebar fixed inset-y-0 left-0 z-50 flex w-64 flex-col justify-between border-r border-slate-200/80 bg-white select-none lg:z-30" :class="{ 'is-open': sidebarOpen }" :inert="!sidebarOpen && mobileViewport">
    <div class="flex min-h-0 flex-col">
        <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100 px-6 py-5">
            <a href="{{ route($portal.'.dashboard') }}" aria-label="HiOwy! — Dashboard {{ $portal === 'bk' ? 'BK' : 'Kesiswaan' }}"><x-brand /></a>
            <button type="button" class="lg:hidden text-slate-500" @click="sidebarOpen = false" aria-label="Tutup navigasi"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="overflow-y-auto px-3 py-4">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Navigasi Utama</div>
            <nav class="flex flex-col gap-1" aria-label="Navigasi {{ $portal === 'bk' ? 'BK' : 'Kesiswaan' }}">
                @foreach ([['dashboard', 'inbox', 'Laporan'], ['manage', 'tune', 'Kelola'], ['learn', 'auto_stories', 'Halaman Pelajari'], ['statistics', 'bar_chart', 'Statistik'], ['archive', 'archive', 'Arsip'], ['access-log', 'history', 'Log Akses']] as [$key, $icon, $label])
                    @continue($portal === 'kesiswaan' && in_array($key, ['manage', 'learn'], true))
                    @php($active = $page === $key || ($key === 'dashboard' && $page === 'report'))
                    <a href="{{ route($portal.'.'.$key) }}" @if($active) aria-current="page" @endif @class(['bk-nav-link flex items-center justify-between px-3 py-2.5 text-sm transition-all', 'rounded-xl bg-secondary font-semibold text-white shadow-xs' => $active, 'rounded-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $active])>
                        <span class="flex items-center gap-3"><span @class(['material-symbols-outlined text-[20px]', 'text-white fill-1' => $active, 'text-slate-400' => ! $active]) aria-hidden="true">{{ $icon }}</span><span>{{ $label }}</span></span>
                        @if($key === 'dashboard')<span class="rounded-full bg-white/20 px-2 py-0.5 text-[11px] font-bold">{{ $reportCount }}</span>@endif
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
    <div class="shrink-0 border-t border-slate-100 p-4">
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition-all hover:bg-rose-50 hover:text-rose-600"><span class="material-symbols-outlined text-[19px]" aria-hidden="true">logout</span>Keluar Pratinjau</a>
    </div>
</aside>
