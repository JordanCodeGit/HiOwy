<x-layouts.bk title="Statistik" page="statistics" mode="dashboard" :report-count="$unreadCount ?? 0">
@php
    $topCatItem = collect($stats['by_category'])->first();
    $topCategory = $topCatItem['label'] ?? '-';
    $topCategoryPct = $topCatItem['pct'] ?? 0;
@endphp
<main class="relative w-full min-h-screen bg-background">
<div class="flex flex-col w-full px-8 py-8 gap-8">
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
<div class="flex flex-col gap-1">

<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Statistik &amp; Analitik Kasus</h1>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
Data agregat {{ $schoolConfig['school_name'] ?? 'SMP Telkom Purwokerto' }} tanpa isi cerita maupun identitas. Kelompok berjumlah 1–4 otomatis disamarkan sebagai &ldquo;kurang dari 5&rdquo; demi privasi pelapor.
</p>
</div>
<div class="flex flex-wrap items-center gap-2.5">
<form method="GET" action="{{ route('bk.statistics') }}" class="flex items-center gap-2">
<select name="month" onchange="this.form.submit()" aria-label="Filter bulan statistik" class="px-3.5 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant/40 text-xs font-semibold text-on-surface cursor-pointer">
<option value="">Semua Bulan</option>
@foreach($stats['available_months'] as $mKey => $mLabel)
<option value="{{ $mKey }}" @selected($selectedMonth === $mKey)>{{ $mLabel }}</option>
@endforeach
</select>
</form>
<a href="{{ route('bk.statistics.csv', ['month' => $selectedMonth]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant/40 hover:bg-surface-container-low text-xs font-semibold text-on-surface shadow-xs transition-all">
<span class="material-symbols-outlined text-[17px] text-emerald-700">table_view</span>
<span>Unduh CSV</span>
</a>
<a href="{{ route('bk.statistics.pdf', ['month' => $selectedMonth]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary hover:bg-primary-container text-on-primary text-xs font-semibold shadow-xs transition-all">
<span class="material-symbols-outlined text-[17px]">picture_as_pdf</span>
<span>Unduh PDF</span>
</a>
</div>
</div>

<div class="staff-metrics metric-featured metric-statistics grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total Laporan Masuk</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">folder_shared</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight capitalize">{{ $stats['total_display'] }}</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Periode: {{ $selectedMonth ? ($stats['available_months'][$selectedMonth] ?? $selectedMonth) : 'Akumulasi Seluruh Bulan' }}</span>
</div>

<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Tingkat Penyelesaian</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-xl">verified</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $stats['rate'] }}%</span>
<span class="font-label-sm text-label-sm text-on-tertiary-container bg-surface-container-low px-2 py-0.5 rounded-full font-semibold">{{ $stats['completed_display'] }} Selesai</span>
</div>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-on-tertiary-container h-full rounded-full" style="width: {{ $stats['rate'] }}%;"></div>
</div>
</div>

<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Antrean Aktif</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">pending_actions</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight capitalize">{{ $stats['active_display'] }}</span>
<span class="font-label-sm text-label-sm text-secondary bg-surface-container-high px-2 py-0.5 rounded-full font-semibold">{{ $stats['unread_display'] }} Belum Dibaca</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Rata-rata waktu dibaca: {{ $stats['avg_read_label'] }}</span>
</div>

<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Kategori Dominan</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">category</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-2">
<span class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">{{ $topCategoryPct }}%</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $topCategory }}</span>
</div>
</div>

@if(!empty($stats['months']))
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between">
<div>
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Ringkasan Tren Bulanan</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Jumlah laporan masuk per bulan (kelompok 1–4 disamarkan)</span>
</div>
<span class="material-symbols-outlined text-outline text-xl">calendar_month</span>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
@foreach($stats['months'] as $mBucket)
<div class="p-3.5 rounded-xl bg-surface-container-low/50 border border-surface-container flex flex-col gap-1">
<span class="text-xs font-semibold text-on-surface-variant">{{ $mBucket['label'] }}</span>
<span class="text-base font-bold text-secondary">{{ $mBucket['display'] }}</span>
</div>
@endforeach
</div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-5">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Distribusi Jenis Kejadian</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Proporsi laporan berdasarkan kategori kejadian</span>
</div>
<span class="material-symbols-outlined text-outline text-xl">pie_chart</span>
</div>
<div class="flex flex-col gap-3">
@forelse($stats['by_category'] as $row)
<div class="flex flex-col gap-1.5 p-3 rounded-xl bg-surface-container-low/40">
<div class="flex items-center justify-between text-xs font-semibold text-on-surface">
<span>{{ $row['label'] }}</span>
<span>{{ $row['display'] }} ({{ $row['pct'] }}%)</span>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-secondary h-full rounded-full transition-all" style="width: {{ $row['pct'] }}%;"></div>
</div>
</div>
@empty
<p class="text-sm text-on-surface-variant py-6 text-center">Belum ada laporan untuk dianalisis.</p>
@endforelse
</div>
</div>

<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-5">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Distribusi Status Tindak Lanjut</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Progres penanganan perkara oleh tim BK</span>
</div>
<div class="px-3 py-1 rounded-full bg-surface-container-low font-label-sm text-label-sm text-secondary font-semibold border border-surface-container">Total {{ $stats['total_display'] }} Kasus</div>
</div>
<div class="flex flex-col justify-between gap-3.5 my-auto">
@foreach([
    'belum_dibaca' => ['label' => 'Belum Dibaca', 'bar' => 'bg-amber-500'],
    'dibaca' => ['label' => 'Dibaca / Telaah Awal', 'bar' => 'bg-sky-600'],
    'ditindaklanjuti' => ['label' => 'Sedang Ditindaklanjuti', 'bar' => 'bg-secondary'],
    'selesai' => ['label' => 'Selesai & Teratasi', 'bar' => 'bg-on-tertiary-container'],
    'iseng' => ['label' => 'Iseng / Laporan Palsu (Arsip)', 'bar' => 'bg-outline'],
] as $statusKey => $meta)
@php($bucket = $stats['by_status'][$statusKey] ?? ['display' => '0', 'pct' => 0])
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md font-semibold text-on-surface">{{ $meta['label'] }}</span>
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-bold text-on-surface leading-none">{{ $bucket['display'] }}</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-on-surface-variant">{{ $bucket['pct'] }}%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="{{ $meta['bar'] }} h-full rounded-full transition-all" style="width: {{ $bucket['pct'] }}%;"></div>
</div>
</div>
@endforeach
</div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between">
<h2 class="font-title-md text-title-md text-on-surface font-bold">Menurut Peran Pelapor</h2>
<span class="material-symbols-outlined text-outline text-xl">group</span>
</div>
<div class="flex flex-col gap-2.5">
@forelse($stats['by_role'] as $row)
<div class="flex items-center justify-between p-2.5 rounded-lg bg-surface-container-low/50 text-xs">
<span class="font-semibold text-on-surface">{{ $row['label'] }}</span>
<span class="font-bold text-secondary">{{ $row['display'] }} ({{ $row['pct'] }}%)</span>
</div>
@empty
<p class="text-xs text-on-surface-variant">Belum ada data peran.</p>
@endforelse
</div>
</div>

<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between">
<h2 class="font-title-md text-title-md text-on-surface font-bold">Menurut Tempat Kejadian</h2>
<span class="material-symbols-outlined text-outline text-xl">pin_drop</span>
</div>
<div class="flex flex-col gap-2.5">
@forelse($stats['by_location'] as $row)
<div class="flex items-center justify-between p-2.5 rounded-lg bg-surface-container-low/50 text-xs">
<span class="font-semibold text-on-surface">{{ $row['label'] }}</span>
<span class="font-bold text-secondary">{{ $row['display'] }} ({{ $row['pct'] }}%)</span>
</div>
@empty
<p class="text-xs text-on-surface-variant">Belum ada data lokasi.</p>
@endforelse
</div>
</div>

<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between">
<h2 class="font-title-md text-title-md text-on-surface font-bold">Menurut Tingkat Urgensi</h2>
<span class="material-symbols-outlined text-outline text-xl">priority_high</span>
</div>
<div class="flex flex-col gap-2.5">
@foreach($stats['by_urgency'] as $row)
<div class="flex items-center justify-between p-2.5 rounded-lg bg-surface-container-low/50 text-xs">
<span class="font-semibold text-on-surface">{{ $row['label'] }}</span>
<span class="font-bold text-secondary">{{ $row['display'] }} ({{ $row['pct'] }}%)</span>
</div>
@endforeach
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>
