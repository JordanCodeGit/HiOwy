@props(['portal' => 'bk'])
<header class="sticky top-0 z-20 min-h-16 py-2 bg-white/95 backdrop-blur border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between">
<div class="flex items-center gap-3">
<button type="button" class="lg:hidden flex items-center" @click="sidebarOpen = true" aria-controls="bk-sidebar" :aria-expanded="sidebarOpen" aria-label="Buka navigasi">
<span class="material-symbols-outlined">menu</span>
</button>
<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100/90 text-slate-700 text-xs font-semibold border border-slate-200">
<span class="material-symbols-outlined text-[16px] text-slate-500">school</span>
<span>Tahun Ajaran 2024/2025</span>
</div>
</div>
<div class="flex items-center gap-2 sm:gap-4">
<div class="relative hidden xl:block w-64">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
<input x-model="quickSearch" @keydown.enter.prevent="searchReports($el.dataset.url)" data-url="{{ route($portal.'.dashboard') }}" aria-label="Cari laporan" class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition-all" placeholder="Pencarian cepat..." type="text">
</div>
@if($portal === 'bk')
<div class="relative" id="notificationWrapper">
<button @click="notificationsOpen = !notificationsOpen; profileOpen = false" :aria-expanded="notificationsOpen" aria-controls="notificationDropdown" aria-label="Notifikasi" class="relative w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 bg-slate-100 hover:bg-slate-200/70 transition-colors cursor-pointer" id="notifBellBtn" type="button">
<span class="material-symbols-outlined text-[20px]">notifications</span>
<span  class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white animate-pulse" x-show="!notificationsRead">
</span>
</button>
<div @click.outside="notificationsOpen = false" x-cloak x-show="notificationsOpen" x-transition.opacity class="absolute right-0 mt-2.5 w-[380px] bg-white rounded-2xl shadow-2xl border border-slate-200/80 z-50 overflow-hidden transition-all duration-200 origin-top-right " id="notificationDropdown">
<div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
<div class="flex items-center gap-2">
<h3 class="text-sm font-bold text-slate-900">Notifikasi</h3>
<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200" x-text="notificationsRead ? '0 Baru' : '3 Baru'">3 Baru</span>
</div>
<button @click="markNotificationsRead()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors" id="markAllReadBtn" type="button">
<span x-text="notificationsRead ? 'Semua Dibaca' : 'Tandai Semua Dibaca'">
</span>
</button>
</div>
<div class="px-4 pt-3 pb-2 flex items-center gap-1.5 border-b border-slate-100 bg-white">
<button  class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-900" type="button" @click="notificationFilter = 'all'" :aria-pressed="notificationFilter === 'all'">Semua (3)</button>
<button   class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-rose-700 hover:bg-rose-50 transition-colors flex items-center gap-1" type="button" @click="notificationFilter = 'urgent'" :aria-pressed="notificationFilter === 'urgent'">
<span class="w-1.5 h-1.5 rounded-full bg-rose-600">
</span> Darurat
      </button>
<button type="button" @click="notificationFilter = 'update'" :aria-pressed="notificationFilter === 'update'" class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 hover:text-slate-800 transition-colors">Update</button>
</div>
<div class="divide-y divide-slate-100 max-h-[360px] overflow-y-auto">
<div class="p-3.5 hover:bg-rose-50/30 transition-colors bg-rose-50/15 cursor-pointer flex items-start gap-3" x-show="notificationFilter === 'all' || notificationFilter === 'urgent'">
<div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5 border border-rose-200">
<span class="material-symbols-outlined text-[18px] fill-1">e911_emergency</span>
</div>
<div class="flex-1 min-w-0">
<div class="flex items-center justify-between gap-1">
<span class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Laporan Darurat Masuk!</span>
<span class="text-[10px] text-slate-400 whitespace-nowrap">5 menit lalu</span>
</div>
<p class="text-xs font-semibold text-slate-900 mt-0.5 line-clamp-2 leading-snug">
            #SR-2024-8042 - Bullying Fisik di Lorong Kelas 8
          </p>
<div class="mt-2 flex items-center gap-2">
<a  class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-bold bg-rose-600 text-white hover:bg-rose-700 transition-colors shadow-xs"  href="{{ route('bk.report') }}">
<span>Buka Kasus</span>
<span class="material-symbols-outlined text-[13px]">arrow_forward</span>
</a>
<span  class="w-2 h-2 rounded-full bg-rose-500" title="Belum dibaca" x-show="!notificationsRead">
</span>
</div>
</div>
</div>
<div class="p-3.5 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" x-show="notificationFilter === 'all' || notificationFilter === 'update'">
<div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5 border border-amber-200">
<span class="material-symbols-outlined text-[18px] fill-1">edit_note</span>
</div>
<div class="flex-1 min-w-0">
<div class="flex items-center justify-between gap-1">
<span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Pembaruan Kasus</span>
<span class="text-[10px] text-slate-400 whitespace-nowrap">1 jam lalu</span>
</div>
<p class="text-xs text-slate-800 mt-0.5 leading-snug">
<span class="font-semibold text-slate-900">#SR-2024-8036</span> - Catatan Wali Kelas ditambahkan: jadwal mediasi konseling disepakati.
          </p>
</div>
</div>
<div class="p-3.5 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" x-show="notificationFilter === 'all' || notificationFilter === 'update'">
<div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5 border border-blue-100">
<span class="material-symbols-outlined text-[18px]">verified_user</span>
</div>
<div class="flex-1 min-w-0">
<div class="flex items-center justify-between gap-1">
<span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Keamanan Akun</span>
<span class="text-[10px] text-slate-400 whitespace-nowrap">3 jam lalu</span>
</div>
<p class="text-xs text-slate-700 mt-0.5 leading-snug">
            Verifikasi 2FA berhasil dari sesi baru (Chrome macOS - Kantor BK).
          </p>
</div>
</div>
</div>
<div class="p-3 border-t border-slate-100 bg-slate-50/70 text-center">
<a class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors" href="{{ route($portal.'.access-log') }}">
<span>Lihat Semua Notifikasi di Log Akses</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</div>
</div>
</div>
@endif
<div class="h-5 w-[1px] bg-slate-200">
</div>
<div class="relative" id="profileWrapper">
<button @click="profileOpen = !profileOpen; notificationsOpen = false" :aria-expanded="profileOpen" aria-controls="profileDropdown" aria-label="Menu profil" class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100/80 transition-colors cursor-pointer focus:outline-none" id="profileMenuBtn" type="button">
<img alt="Sarah Jenkins" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" src="{{ asset('images/bk/counselor.jpg') }}">
<div class="hidden sm:flex flex-col text-left">
<span class="text-xs font-semibold text-slate-900 leading-tight">Sarah Jenkins, M.Pd.</span>
<span class="text-[10px] text-slate-500 font-medium leading-none mt-0.5">{{ $portal === 'bk' ? 'Guru BK / Konselor Inti' : 'Kesiswaan' }}</span>
</div>
<span class="material-symbols-outlined text-[18px] text-slate-400 transition-transform group-hover:text-slate-600">expand_more</span>
</button>
<div @click.outside="profileOpen = false" x-cloak x-show="profileOpen" x-transition.opacity class="absolute right-0 mt-2.5 bg-white rounded-2xl shadow-2xl border border-slate-200/80 z-50 overflow-hidden transition-all duration-200 origin-top-right w-[320px]" id="profileDropdown">
<div class="p-4 border-b border-slate-100 bg-slate-50/70 flex items-center gap-3">
<img alt="Sarah Jenkins" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-100 shrink-0" src="{{ asset('images/bk/counselor.jpg') }}">
<div class="flex flex-col min-w-0 flex-1">
<span class="text-xs font-bold text-slate-900 truncate">Sarah Jenkins, M.Pd.</span>
<span class="text-[11px] text-slate-500 truncate">sarah.jenkins@lincoln.sch.id</span>
<div class="mt-1">
<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">{{ $portal === 'bk' ? 'Guru BK / Konselor Inti' : 'Kesiswaan' }}</span>
</div>
</div>
</div>
<div class="p-2 source-space-y-1">
<a class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors group" href="{{ route($portal.'.profile') }}">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined text-[18px]">person</span>
</div>
<div class="flex flex-col">
<span class="font-semibold text-slate-800 group-hover:text-blue-700 leading-tight">Pengaturan Profil</span>
<span class="text-[10px] text-slate-400 mt-0.5">Data diri &amp; info sekolah</span>
</div>
</div>
<span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-slate-600">chevron_right</span>
</a>
<a class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors group" href="{{ route($portal.'.profile') }}#security">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
<span class="material-symbols-outlined text-[18px]">security</span>
</div>
<div class="flex flex-col">
<span class="font-semibold text-slate-800 group-hover:text-emerald-700 leading-tight">Keamanan &amp; 2FA</span>
<span class="text-[10px] text-slate-400 mt-0.5">Sandi &amp; keamanan akun</span>
</div>
</div>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600">
</span>2FA Aktif</span>
</a>
<a class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors group" href="{{ route($portal.'.access-log') }}">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined text-[18px]">history</span>
</div>
<div class="flex flex-col">
<span class="font-semibold text-slate-800 group-hover:text-blue-700 leading-tight">Log Aktivitas Akun</span>
<span class="text-[10px] text-slate-400 mt-0.5">Sesi masuk terakhir</span>
</div>
</div>
<span class="text-[10px] text-slate-400 font-medium">Aktif</span>
</a>
</div>
@if($portal === 'kesiswaan')
<div class="border-t border-slate-100 p-2"><a href="{{ route('public.learn', ['code' => 'smakita']) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-semibold text-slate-700 hover:bg-blue-50"><span class="material-symbols-outlined text-lg">auto_stories</span>Pelajari · Portal Publik</a></div>
@endif
<div class="border-t border-slate-100 p-2 bg-slate-50/50">
<a class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors" href="{{ route('login') }}">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[17px]">logout</span>
</div>
<span>Keluar Pratinjau</span>
</div>
<span class="material-symbols-outlined text-[16px] text-rose-400">arrow_forward</span>
</a>
</div>
</div>
</div>
</div>
</header>

