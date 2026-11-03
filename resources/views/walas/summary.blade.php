<x-layouts.bk title="Ringkasan Pendampingan Wali Kelas" page="summary" portal="walas" :report-count="$unreadCount" :notifications="$notifications">
<main  class="bg-background min-h-screen"><div class="flex flex-col w-full">
<div class="max-w-5xl mx-auto w-full px-4 lg:px-gutter-lg py-space-xl flex flex-col gap-space-lg">

<div class="flex flex-wrap items-center justify-between gap-space-md">
<a class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-on-surface transition-colors py-space-xs px-space-sm rounded-lg hover:bg-surface-container-low group" href="{{ route('walas.dashboard') }}">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:-translate-x-1">arrow_back</span>
<span class="font-label-lg text-label-lg">Kembali ke Ringkasan</span>
</a>
<div class="flex items-center gap-space-sm">

<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
          {{ $summary['status'] === 'read' ? 'Sudah Dibaca' : 'Belum Dibaca' }}
        </span>
</div>
</div>

<div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-xs flex flex-col gap-space-md">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md border-none">
<div class="flex flex-col gap-space-xs">
<div class="inline-flex items-center gap-space-xs text-secondary font-label-md text-label-md">
<span class="material-symbols-outlined text-[16px]">folder_shared</span>
<span class="">Dokumen Disposisi Konseling Kelas</span>
</div>
<h1 class="font-headline-md text-headline-md text-on-surface tracking-tight">Catatan Pendampingan Siswa</h1>
</div>
<div class="flex items-center gap-space-sm bg-surface-container-low px-space-md py-space-xs rounded-xl self-start md:self-auto">
<span class="material-symbols-outlined text-[18px] text-on-surface-variant">schedule</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Status Akses</span>
<span class="font-label-md text-label-md text-on-surface">{{ $summary['read_at'] ? 'Dibaca pada: '.$summary['read_at'] : 'Belum ditandai dibaca' }}</span>
</div>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-space-md pt-space-xs">
<div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
<div class="w-10 h-10 rounded-full bg-secondary text-on-secondary flex items-center justify-center font-title-md text-title-md shrink-0 shadow-xs">
{{ $summary['counselor'] === 'Sarah Jenkins, M.Pd.' ? 'SJ' : 'NH' }}
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Pengirim Ringkasan</span>
<span class="font-title-md text-title-md text-on-surface truncate">{{ $summary['counselor'] }}</span>
<div class="inline-flex items-center gap-1 text-secondary mt-0.5">
<span class="material-symbols-outlined text-[13px]">shield</span>
<span class="font-label-sm text-label-sm">{{ $summary['role'] }}</span>
</div>
</div>
</div>
<div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
<div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-on-surface shrink-0">
<span class="material-symbols-outlined text-[20px]">groups</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Target Pendampingan</span>
<span class="font-title-md text-title-md text-on-surface">Kelas 8B</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Semester Genap 2024/2025</span>
</div>
</div>
<div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
<div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-on-surface shrink-0">
<span class="material-symbols-outlined text-[20px]">forward_to_inbox</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Waktu Diteruskan</span>
<span class="font-title-md text-title-md text-on-surface">{{ $summary['time'] }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Waktu pada data contoh</span>
</div>
</div>
</div>
</div>



<div class="bg-surface-container-lowest rounded-2xl shadow-xs overflow-hidden">

<div class="p-space-lg bg-surface-container-low flex flex-wrap gap-3 items-center justify-between">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-[22px] text-secondary">psychology_alt</span>
<div>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant block">Dokumen Resmi BK</span>
<span class="font-title-lg text-title-lg text-on-surface">Teks Ringkasan Rekomendasi BK</span>
</div>
</div>
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px] text-secondary">lock</span>
<span class="">Akses Terbatas: Hanya Baca</span>
</div>
</div>
<div class="p-space-xl flex flex-col gap-space-xl">

<div class="relative bg-surface-container-lowest rounded-xl p-space-lg shadow-xs">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-secondary rounded-l-xl"></div>
<div class="pl-space-xs flex flex-col gap-space-xs">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-semibold">{{ $summary['id'] === 4 ? 'Tindak Lanjut Kehadiran & Partisipasi' : 'Fokus Observasi & Arahan Kelas' }}</span>
<p class="font-body-lg text-body-lg text-on-surface leading-relaxed">{{ $summary['detail'] }}</p>
</div>
</div>

<div class="bg-surface-container-low rounded-xl p-space-lg flex flex-col gap-space-md">
<div class="flex items-center gap-space-xs text-on-surface">
<span class="material-symbols-outlined text-[20px] text-secondary">visibility</span>
<h3 class="font-title-md text-title-md">Fokus Pengamatan Wali Kelas:</h3>
</div>
<ul class="flex flex-col gap-space-sm pl-space-xs">
<li class="flex items-start gap-space-sm">
<span class="w-6 h-6 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[14px]">check</span>
</span>
<span class="font-body-md text-body-md text-on-surface">Bangun atmosfer kelas yang suportif dan inklusif.</span>
</li>
<li class="flex items-start gap-space-sm">
<span class="w-6 h-6 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[14px]">check</span>
</span>
<span class="font-body-md text-body-md text-on-surface">Berikan respon netral dan apresiatif atas partisipasi harian.</span>
</li>
<li class="flex items-start gap-space-sm">
<span class="w-6 h-6 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[14px]">check</span>
</span>
<span class="font-body-md text-body-md text-on-surface">Hubungi Guru BK melalui ruang konsultasi jika membutuhkan panduan intervensi lebih lanjut.</span>
</li>
</ul>
</div>
</div>

<div class="p-space-lg bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-md">
<div class="flex items-center flex-wrap gap-space-sm w-full sm:w-auto">

<button type="button" @click="unavailable('Perubahan status baca ringkasan')" class="inline-flex items-center justify-center gap-space-xs px-space-lg py-2.5 rounded-xl bg-on-tertiary-container text-on-tertiary font-label-lg text-label-lg shadow-xs hover:opacity-95 transition-all"><span class="material-symbols-outlined text-[18px]">{{ $summary['status'] === 'read' ? 'check_circle' : 'mark_email_read' }}</span><span>{{ $summary['status'] === 'read' ? 'Sudah Ditandai Dibaca' : 'Tandai Sudah Dibaca' }}</span></button>

<button class="inline-flex items-center justify-center gap-space-xs px-space-md py-2.5 rounded-xl bg-surface-container-lowest text-on-surface hover:bg-surface-container font-label-lg text-label-lg transition-colors shadow-xs" id="downloadPdfBtn" @click="unavailable('Pembuatan PDF terenkripsi')" type="button">
<span class="material-symbols-outlined text-[18px]">download_for_offline</span>
<span class="">Simpan PDF Terenkripsi</span>
</button>
</div>
<a class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md py-space-xs px-space-sm rounded-lg hover:bg-surface-container transition-colors" href="{{ route('walas.dashboard') }}">
<span class="material-symbols-outlined text-[18px]">list_alt</span>
<span class="">Daftar Seluruh Ringkasan</span>
</a>
</div>
</div>

<div class="flex flex-col sm:flex-row items-center justify-between gap-space-xs text-center sm:text-left py-space-sm text-on-surface-variant font-body-sm text-body-sm">
<div class="flex items-center gap-space-xs">


</div>
<div class="text-on-surface-variant/80">
        ID Audit Sesi: <code class="font-mono text-label-sm">{{ $summary['id'] === 4 ? 'E2E-WK8B-20241019-V2 (contoh)' : 'Belum tersedia' }}</code>
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>
