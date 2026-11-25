<x-layouts.bk title="Laporan Kesiswaan" page="dashboard" mode="dashboard" portal="kesiswaan" :report-count="$summary['unread']">
<main x-data="kesiswaanTable(false)" data-report-list @bk-search.window="query = $event.detail.query; status = 'belum_dibaca'; selected = []" class="bk-reports-page p-4 lg:p-8 w-full space-y-6">
@if(session('status'))
<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-semibold text-emerald-800 flex items-center gap-2">
<span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
<span>{{ session('status') }}</span>
</div>
@endif
<header class="staff-page-intro">
    <h1>Antrean Laporan</h1>
    <p>Tinjau laporan siswa dalam cakupan akses kesiswaan.</p>
</header>
<x-bk.report-metrics :unread="$summary['unread']" :urgent="$summary['danger']" :overdue="$summary['overdue']" />
<x-kesiswaan.reports-table :reports="$reports" :types="$types" :summary="$summary" />
</main></x-layouts.bk>
