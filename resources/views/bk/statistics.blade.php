<x-layouts.bk title="Statistik" page="statistics" mode="dashboard" :report-count="$unreadCount ?? 0">
@php
    $topCategory = $byCategory->keys()->first() ?? '-';
    $topCategoryCount = $byCategory->first() ?? 0;
    $topCategoryPct = $total > 0 ? (int) round(($topCategoryCount / $total) * 100) : 0;
@endphp
<main class="relative w-full min-h-screen bg-background">
<div class="flex flex-col w-full px-8 py-8 gap-8 max-w-7xl mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div class="flex flex-col gap-1">
<div class="inline-flex items-center gap-2 mb-1">
<span class="w-2 h-2 rounded-full bg-secondary"></span>
<span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest">Dashboard Konseling Terpadu</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Statistik &amp; Analitik Kasus</h1>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
Data agregat pelaporan nyata SMP Telkom Purwokerto untuk evaluasi program pencegahan sekolah, tanpa menampilkan identitas siswa.
</p>
</div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total Laporan Masuk</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">folder_shared</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $total }}</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Akumulasi seluruh laporan</span>
</div>

<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Tingkat Penyelesaian</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-xl">verified</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $rate }}%</span>
<span class="font-label-sm text-label-sm text-on-tertiary-container bg-surface-container-low px-2 py-0.5 rounded-full font-semibold">{{ $completed }} Selesai</span>
</div>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-on-tertiary-container h-full rounded-full" style="width: {{ $rate }}%;"></div>
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
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $active }}</span>
<span class="font-label-sm text-label-sm text-secondary bg-surface-container-high px-2 py-0.5 rounded-full font-semibold">{{ $unreadCount }} Belum Dibaca</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Belum diarsipkan tim BK</span>
</div>

<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Kategori Dominan</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">record_voice_over</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight truncate">{{ $topCategory }}</span>
<span class="font-headline-sm text-headline-sm text-secondary font-bold">{{ $topCategoryPct }}%</span>
</div>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: {{ $topCategoryPct }}%;"></div>
</div>
</div>
</div>

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
@forelse($byCategory as $catName => $catCount)
@php($pct = $total > 0 ? (int) round(($catCount / $total) * 100) : 0)
<div class="flex flex-col gap-1.5 p-3 rounded-xl bg-surface-container-low/40">
<div class="flex items-center justify-between text-xs font-semibold text-on-surface">
<span>{{ $catName ?: 'Lainnya' }}</span>
<span>{{ $catCount }} ({{ $pct }}%)</span>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-secondary h-full rounded-full transition-all" style="width: {{ $pct }}%;"></div>
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
<div class="px-3 py-1 rounded-full bg-surface-container-low font-label-sm text-label-sm text-secondary font-semibold border border-surface-container">Total {{ $total }} Kasus</div>
</div>
<div class="flex flex-col justify-between gap-3.5 my-auto">
@foreach([
    'belum_dibaca' => ['label' => 'Belum Dibaca', 'bar' => 'bg-amber-500'],
    'dibaca' => ['label' => 'Dibaca / Telaah Awal', 'bar' => 'bg-sky-600'],
    'ditindaklanjuti' => ['label' => 'Sedang Ditindaklanjuti', 'bar' => 'bg-secondary'],
    'selesai' => ['label' => 'Selesai & Teratasi', 'bar' => 'bg-on-tertiary-container'],
    'iseng' => ['label' => 'Iseng / Laporan Palsu (Arsip)', 'bar' => 'bg-outline'],
] as $statusKey => $meta)
@php($cnt = $byStatus[$statusKey] ?? 0)
@php($pct = $total > 0 ? (int) round(($cnt / $total) * 100) : 0)
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md font-semibold text-on-surface">{{ $meta['label'] }}</span>
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">{{ $cnt }}</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-on-surface-variant">{{ $pct }}%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="{{ $meta['bar'] }} h-full rounded-full transition-all" style="width: {{ $pct }}%;"></div>
</div>
</div>
@endforeach
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>
