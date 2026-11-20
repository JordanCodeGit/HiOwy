<x-layouts.bk title="Statistik Kesiswaan" page="statistics" portal="kesiswaan" :report-count="$summary['unread']">
    <main class="max-w-[1580px] mx-auto w-full p-4 lg:p-8 space-y-8">
        <header><h1 class="text-3xl font-bold tracking-tight">Statistik &amp; Analitik</h1><p class="mt-2 text-sm text-on-surface-variant">Ringkasan agregat dari jenis yang dapat Anda lihat.</p><p class="mt-2 text-xs text-slate-500">Data agregat keseluruhan (termasuk arsip)</p></header>
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            @foreach([['Total Laporan', $statistics['total'], 'description'], ['Laporan Aktif', $statistics['active'], 'pending_actions'], ['Selesai Ditangani', $statistics['completed'], 'task_alt'], ['Tingkat Penyelesaian', $statistics['rate'].'%', 'monitoring']] as [$label, $value, $icon])
            <div class="bk-hover-card rounded-2xl bg-white border border-slate-200 p-6 shadow-xs"><div class="flex justify-between items-center gap-3"><span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</span><span class="material-symbols-outlined rounded-xl bg-blue-50 text-secondary p-2">{{ $icon }}</span></div><p class="mt-4 text-3xl font-bold">{{ $value }}</p></div>
            @endforeach
        </section>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="lg:col-span-7 bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs"><h2 class="text-lg font-bold">Tren Laporan Masuk</h2><p class="mt-1 text-sm text-slate-500">Jumlah laporan per bulan</p>
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-6 h-64 border-b border-slate-200 px-6" role="img" aria-label="Grafik tren laporan per bulan">
                    @forelse($statistics['months'] as $month => $count)<div class="flex flex-col justify-end items-center gap-2 h-full"><span class="text-sm font-bold text-secondary">{{ $count }}</span><div class="w-full max-w-28 min-h-1 rounded-t-xl bg-secondary hover:bg-primary transition-colors" style="height: {{ $count / max(1, max($statistics['months'])) * 80 }}%"></div><span class="text-xs text-slate-500 mb-3">{{ $month }}</span></div>@empty<p class="col-span-full self-center text-center text-sm text-slate-500">Belum ada data laporan masuk.</p>@endforelse
                </div>
            </section>
            <section class="lg:col-span-5 bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs"><h2 class="text-lg font-bold">Distribusi Jenis Kejadian</h2><div class="mt-6 space-y-5">@forelse($statistics['types'] as $type => $count)<div><div class="flex justify-between gap-4 text-sm"><span>{{ $types[$type] ?? ucfirst($type) }}</span><span class="font-semibold">{{ $count }} <span class="text-xs text-slate-400">({{ round($count / max(1, $statistics['total']) * 100) }}%)</span></span></div><div class="mt-2 h-2.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-secondary" style="width: {{ $count / max(1, $statistics['total']) * 100 }}%"></div></div></div>@empty<p class="text-sm text-slate-500">Belum ada data.</p>@endforelse</div></section>
        </div>
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs"><h2 class="text-lg font-bold mb-5">Status Penanganan</h2><div class="grid grid-cols-2 lg:grid-cols-5 gap-4">@foreach(['belum_dibaca' => 'Belum Dibaca', 'dibaca' => 'Dibaca', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai', 'iseng' => 'Iseng'] as $key => $label)<div class="bk-hover-card rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $statistics['statuses'][$key] ?? 0 }}</p></div>@endforeach</div></section>
    </main>
</x-layouts.bk>
