<x-layouts.bk title="Arsip Kesiswaan" page="archive" portal="kesiswaan" :report-count="$summary['unread']">
    <main x-data="kesiswaanTable(true)" class="bk-reports-page w-full p-4 lg:p-8 space-y-6">
        <header><h1 class="text-3xl font-bold tracking-tight">Arsip Laporan</h1><p class="mt-2 text-sm text-on-surface-variant">Riwayat laporan yang telah diarsipkan dalam cakupan akses Anda.</p></header>
        <div class="staff-metrics metric-compact grid gap-4 sm:grid-cols-3">
            <x-bk.stat-card icon="inventory_2" label="Total Arsip" :value="$reports->count()" />
            <x-bk.stat-card icon="task_alt" label="Selesai Ditangani" :value="$reports->where('status', 'selesai')->count()" />
            <x-bk.stat-card icon="flag" label="Laporan Iseng" :value="$reports->where('status', 'iseng')->count()" />
        </div>
        <x-kesiswaan.reports-table :reports="$reports" :types="$types" :summary="$summary" :archive="true" />
    </main>
</x-layouts.bk>
