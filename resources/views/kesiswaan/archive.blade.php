<x-layouts.bk title="Arsip Kesiswaan" page="archive" portal="kesiswaan" :report-count="$summary['unread']">
    <main x-data="kesiswaanTable(true)" class="bk-reports-page w-full p-4 lg:p-8 space-y-6">
        <header><h1 class="text-3xl font-bold tracking-tight">Arsip Laporan</h1><p class="mt-2 text-sm text-on-surface-variant">Riwayat laporan yang telah diarsipkan dalam cakupan akses Anda.</p></header>
        <div class="grid gap-5 sm:grid-cols-3">
            @foreach(['Total Arsip' => $reports->count(), 'Selesai Ditangani' => $reports->where('status', 'selesai')->count(), 'Laporan Iseng' => $reports->where('status', 'iseng')->count()] as $label => $count)
            <div class="bk-hover-card rounded-xl border border-slate-200 bg-white p-6 shadow-xs"><p class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold">{{ $count }}</p></div>
            @endforeach
        </div>
        <x-kesiswaan.reports-table :reports="$reports" :types="$types" :summary="$summary" :archive="true" />
    </main>
</x-layouts.bk>
