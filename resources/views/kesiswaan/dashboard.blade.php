<x-layouts.bk title="Laporan Kesiswaan" page="dashboard" mode="dashboard" portal="kesiswaan" :report-count="$summary['unread']">
<main x-data="kesiswaanTable(false)" data-report-list @bk-search.window="query = $event.detail.query; status = 'belum_dibaca'; selected = []" class="bk-reports-page p-4 lg:p-8 w-full space-y-6">
<section class="grid grid-cols-1 md:grid-cols-3 gap-5">
<div class="bk-hover-card bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
<div>
<span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Belum Dibaca</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold text-slate-900 tracking-tight">{{ $summary['unread'] }}</span>
<span class="text-xs text-slate-500 font-medium">laporan baru</span>
</div>
<p class="text-[11px] text-slate-400 mt-1">Laporan dalam cakupan akses Anda</p>
</div>
<div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
<span class="material-symbols-outlined text-[24px]">mark_email_unread</span>
</div>
</div>
<div class="relative overflow-hidden bg-rose-50/70 rounded-xl p-5 border-2 border-rose-400 shadow-xs shadow-rose-200/50 flex items-center justify-between">
<div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-200/40 rounded-full blur-xl pointer-events-none">
</div>
<div>
<div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-xs">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-ping">
</span>
              Perhatian Utama
            </div>
<div class="text-xs font-semibold text-rose-900 mt-1.5">Danger Flag Belum Dibaca</div>
<div class="flex items-baseline gap-2 mt-0.5">
<span class="text-3xl font-bold text-rose-600 tracking-tight">{{ $summary['danger'] }}</span>
<span class="text-xs font-semibold text-rose-700">laporan darurat</span>
</div>
<p class="text-[11px] text-rose-700/90 font-medium mt-0.5">Indikasi bahaya fisik / intimidasi intens</p>
</div>
<div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-500/30">
<span class="material-symbols-outlined text-[26px] fill-1">e911_emergency</span>
</div>
</div>
<div class="bk-hover-card bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
<div>
<span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanpa Update &gt; 3 Hari</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold text-slate-900 tracking-tight">{{ $summary['overdue'] }}</span>
<span class="text-xs text-amber-700 font-medium">perlu tindak lanjut</span>
</div>
<p class="text-[11px] text-slate-400 mt-1">Acuan pratinjau: 24 Oktober 2024</p>
</div>
<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
<span class="material-symbols-outlined text-[24px]">history_toggle_off</span>
</div>
</div>
</section>
<x-kesiswaan.reports-table :reports="$reports" :types="$types" :summary="$summary" />
</main></x-layouts.bk>
