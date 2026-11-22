<x-layouts.bk title="Statistik Kesiswaan" page="statistics" portal="kesiswaan" :report-count="$summary['unread']">
    <main class="max-w-[1580px] mx-auto w-full p-4 lg:p-8 space-y-8">
        <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Statistik &amp; Analitik</h1>
                <p class="mt-2 text-sm text-on-surface-variant">Ringkasan agregat dari jenis kejadian yang diizinkan. Kelompok bernilai 1–4 disamarkan sebagai &ldquo;kurang dari 5&rdquo; demi privasi pelapor.</p>
            </div>
            <form method="GET" action="{{ route('kesiswaan.statistics') }}">
                <select name="month" onchange="this.form.submit()" aria-label="Filter bulan statistik" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 cursor-pointer">
                    <option value="">Semua Bulan</option>
                    @foreach($stats['available_months'] as $mKey => $mLabel)
                    <option value="{{ $mKey }}" @selected($selectedMonth === $mKey)>{{ $mLabel }}</option>
                    @endforeach
                </select>
            </form>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            @foreach([
                ['Total Laporan', $stats['total_display'], 'description'],
                ['Laporan Aktif', $stats['active_display'], 'pending_actions'],
                ['Tingkat Penyelesaian', $stats['rate'].'% ('.$stats['completed_display'].')', 'monitoring'],
                ['Rata-rata Waktu Dibaca', $stats['avg_read_label'], 'schedule']
            ] as [$label, $value, $icon])
            <div class="bk-hover-card rounded-2xl bg-white border border-slate-200 p-6 shadow-xs">
                <div class="flex justify-between items-center gap-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</span>
                    <span class="material-symbols-outlined rounded-xl bg-blue-50 text-secondary p-2">{{ $icon }}</span>
                </div>
                <p class="mt-4 text-2xl font-bold">{{ $value }}</p>
            </div>
            @endforeach
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="lg:col-span-6 bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="text-lg font-bold">Tren Laporan Bulanan</h2>
                <div class="mt-5 space-y-3">
                    @forelse($stats['months'] as $row)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 text-xs">
                        <span class="font-semibold text-slate-700">{{ $row['label'] }}</span>
                        <span class="font-bold text-secondary">{{ $row['display'] }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">Belum ada data laporan masuk.</p>
                    @endforelse
                </div>
            </section>
            <section class="lg:col-span-6 bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="text-lg font-bold">Distribusi Jenis Kejadian</h2>
                <div class="mt-5 space-y-4">
                    @forelse($stats['by_category'] as $row)
                    <div>
                        <div class="flex justify-between gap-4 text-sm">
                            <span>{{ $row['label'] }}</span>
                            <span class="font-semibold">{{ $row['display'] }} <span class="text-xs text-slate-400">({{ $row['pct'] }}%)</span></span>
                        </div>
                        <div class="mt-2 h-2.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-secondary" style="width: {{ $row['pct'] }}%"></div></div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">Belum ada data.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="text-base font-bold">Menurut Peran Pelapor</h2>
                @forelse($stats['by_role'] as $row)
                <div class="flex justify-between text-xs p-2.5 rounded-lg bg-slate-50"><span>{{ $row['label'] }}</span><strong>{{ $row['display'] }} ({{ $row['pct'] }}%)</strong></div>
                @empty
                <p class="text-xs text-slate-500">Belum ada data.</p>
                @endforelse
            </section>
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="text-base font-bold">Menurut Tempat Kejadian</h2>
                @forelse($stats['by_location'] as $row)
                <div class="flex justify-between text-xs p-2.5 rounded-lg bg-slate-50"><span>{{ $row['label'] }}</span><strong>{{ $row['display'] }} ({{ $row['pct'] }}%)</strong></div>
                @empty
                <p class="text-xs text-slate-500">Belum ada data.</p>
                @endforelse
            </section>
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="text-base font-bold">Menurut Tingkat Urgensi</h2>
                @foreach($stats['by_urgency'] as $row)
                <div class="flex justify-between text-xs p-2.5 rounded-lg bg-slate-50"><span>{{ $row['label'] }}</span><strong>{{ $row['display'] }} ({{ $row['pct'] }}%)</strong></div>
                @endforeach
            </section>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
            <h2 class="text-lg font-bold mb-5">Status Penanganan</h2>
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach(['belum_dibaca' => 'Belum Dibaca', 'dibaca' => 'Dibaca', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai', 'iseng' => 'Iseng'] as $key => $label)
                <div class="bk-hover-card rounded-xl bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-lg font-bold">{{ $stats['by_status'][$key]['display'] ?? '0' }}</p>
                </div>
                @endforeach
            </div>
        </section>
    </main>
</x-layouts.bk>
