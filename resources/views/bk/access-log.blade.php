<x-layouts.bk title="Log Akses" page="access-log" mode="">
<main x-data="bkTable('access-log')" class="relative  w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="p-8 max-w-[1440px] mx-auto w-full source-space-y-6">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<div class="flex items-center gap-2 mb-1.5">
<span class="w-2 h-2 rounded-full bg-secondary animate-pulse">
</span>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">Kepatuhan Regulasi &amp; Privasi Siswa</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Log Akses &amp; Audit Keamanan</h1>
</div>
<div class="flex items-center gap-3">
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Entri (Bulan Ini)</span>
<span class="material-symbols-outlined text-secondary text-xl">receipt_long</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">1,842</span>
<div class="flex items-center gap-1.5 text-on-tertiary-container mt-0.5">
<span class="material-symbols-outlined text-sm">security_update_good</span>
<span class="font-body-sm text-body-sm font-medium">100% Tercatat Otomatis</span>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Akses Kasus Sensitif</span>
<span class="material-symbols-outlined text-secondary text-xl">visibility</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">128</span>
<div class="flex items-center gap-1.5 text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-sm">badge</span>
<span class="font-body-sm text-body-sm">Khusus Konselor Terverifikasi</span>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Otorisasi Gagal</span>
<span class="material-symbols-outlined text-on-surface-variant text-xl">shield_person</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">0</span>
<div class="flex items-center gap-1.5 text-on-tertiary-container mt-0.5">
<span class="material-symbols-outlined text-sm">check_circle</span>
<span class="font-body-sm text-body-sm font-medium">Tidak Ada Anomali Sesi</span>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Sinkronisasi Backup</span>
<span class="material-symbols-outlined text-secondary text-xl">cloud_sync</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">12 Menit Lalu</span>
<div class="flex items-center gap-1.5 text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-sm">schedule</span>
<span class="font-body-sm text-body-sm font-mono">03:00 WIB Tiap Malam</span>
</div>
</div>
</div>
</div>
<div class="bg-surface-container-lowest p-4 rounded-xl shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
<div class="relative flex-1 max-w-md">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input x-model="query" @input="selected = []" aria-label="Cari data tabel" class="w-full pl-9 pr-4 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" id="auditSearch" placeholder="Cari pengguna, IP, aksi, atau kode tiket..." type="text">
</div>
<div class="relative min-w-[180px]">
<select  class="w-full appearance-none bg-surface-container-low py-2 pl-3.5 pr-8 rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="roleFilter" @change="filterChanged($event)">
<option value="all">Semua Peran Staf</option>
<option value="konselor">Konselor Inti</option>
<option value="kesiswaan">Tim Kesiswaan</option>
<option value="kepsek">Kepala Sekolah</option>
<option value="admin">Admin IT &amp; Otomasi</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">expand_more</span>
</div>
<div class="relative min-w-[160px]">
<select  class="w-full appearance-none bg-surface-container-low py-2 pl-3.5 pr-8 rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="timeFilter" @change="filterChanged($event)">
<option value="today">Hari Ini</option>
<option selected="" value="7d">7 Hari Terakhir</option>
<option value="30d">30 Hari Terakhir</option>
<option value="semester">Semester Ini</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">calendar_view_day</span>
</div>
</div>
<div class="flex items-center gap-2">
<button @click="unavailable('Export audit log')"  class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-container text-on-primary rounded-lg font-label-lg text-label-lg shadow-xs hover:opacity-90 active:scale-[0.99] transition-all cursor-pointer whitespace-nowrap" id="exportBtn" type="button">
<span class="material-symbols-outlined text-lg">download</span>
<span>Ekspor Audit Log (CSV)</span>
</button>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse" id="auditTable">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<th class="py-3.5 px-6 font-semibold w-[220px]" scope="col">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">schedule</span>
<span>Waktu &amp; Tanggal</span>
</div>
</th>
<th class="py-3.5 px-6 font-semibold w-[280px]" scope="col">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">person</span>
<span>Pengguna &amp; Peran</span>
</div>
</th>
<th class="py-3.5 px-6 font-semibold min-w-[240px]" scope="col">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">bolt</span>
<span>Aksi Sistem</span>
</div>
</th>
<th class="py-3.5 px-6 font-semibold w-[180px]" scope="col">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">confirmation_number</span>
<span>Laporan Terkait</span>
</div>
</th>
<th class="py-3.5 px-6 font-semibold w-[240px]" scope="col">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">terminal</span>
<span>Alamat IP &amp; Perangkat</span>
</div>
</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low font-body-md text-body-md text-on-surface" id="tableBody">
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="0" x-show="rowVisible(0)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">24 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">10:28:14 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-secondary">psychology</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="font-label-sm text-label-sm text-secondary">Konselor Inti</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-secondary">
</span>
                  Membuka Detail Laporan
                </span>
</td>
<td class="py-4 px-6 align-middle">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md">
<span class="material-symbols-outlined text-sm text-on-surface-variant">tag</span>
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#SR-2024-8042</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.42</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Chrome 130 • macOS Tahoe</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="1" x-show="rowVisible(1)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">24 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">10:14:02 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-secondary">psychology</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="font-label-sm text-label-sm text-secondary">Konselor Inti</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container">
</span>
                  Memperbarui Status Kasus
                </span>
</td>
<td class="py-4 px-6 align-middle">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md">
<span class="material-symbols-outlined text-sm text-on-surface-variant">tag</span>
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#SR-2024-8041</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.42</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Chrome 130 • macOS Tahoe</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="2" x-show="rowVisible(2)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">24 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">09:47:33 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-on-surface-variant">groups</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Budi Setiawan, S.Pd.</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Tim Kesiswaan</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-outline">
</span>
                  Meneruskan Ringkasan Netral
                </span>
</td>
<td class="py-4 px-6 align-middle">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md">
<span class="material-symbols-outlined text-sm text-on-surface-variant">tag</span>
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#SR-2024-8039</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.18</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Edge 129 • Windows 11</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="3" x-show="rowVisible(3)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">24 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">08:15:10 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-secondary">psychology</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="font-label-sm text-label-sm text-secondary">Konselor Inti</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-secondary">
</span>
                  Mengubah Label Urgensi
                </span>
</td>
<td class="py-4 px-6 align-middle">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md">
<span class="material-symbols-outlined text-sm text-on-surface-variant">tag</span>
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#SR-2024-8035</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.42</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Chrome 130 • macOS Tahoe</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="4" x-show="rowVisible(4)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">24 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">07:55:40 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-secondary">psychology</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="font-label-sm text-label-sm text-secondary">Konselor Inti</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm">
<span class="material-symbols-outlined text-sm text-on-tertiary-container">verified_user</span>
                  Login Terverifikasi 2FA
                </span>
</td>
<td class="py-4 px-6 align-middle">
<span class="font-mono text-xs text-outline pl-3 font-semibold">-</span>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.42</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Chrome 130 • macOS Tahoe</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="5" x-show="rowVisible(5)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">23 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">23:00:01 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-outline">smart_toy</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">SysAdmin Gateway</span>
<span class="font-label-sm text-label-sm text-outline">Otomasi Sistem</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">
<span class="material-symbols-outlined text-sm text-secondary">encrypted</span>
                  Rotasi Kunci Sesi Harian
                </span>
</td>
<td class="py-4 px-6 align-middle">
<span class="font-mono text-xs text-outline pl-3 font-semibold">-</span>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">10.0.0.1 (Internal)</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Daemon Cron v2.4</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="6" x-show="rowVisible(6)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">23 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">15:42:19 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-on-surface-variant">school</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Dr. H. Rusdianto, M.M.</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Kepala Sekolah</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-outline">
</span>
                  Melihat Agregat Statistik Sekolah
                </span>
</td>
<td class="py-4 px-6 align-middle">
<span class="font-mono text-xs text-outline pl-3 font-semibold">-</span>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.5</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Safari 17 • iPadOS 17.6</span>
</div>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="7" x-show="rowVisible(7)">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">23 Okt 2024</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">11:05:44 WIB</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-base text-secondary">psychology</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="font-label-sm text-label-sm text-secondary">Konselor Inti</span>
</div>
</div>
</td>
<td class="py-4 px-6 align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-secondary">
</span>
                  Membuka Detail Laporan
                </span>
</td>
<td class="py-4 px-6 align-middle">
<div class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md">
<span class="material-symbols-outlined text-sm text-on-surface-variant">tag</span>
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#SR-2024-8022</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col font-mono text-xs">
<span class="text-on-surface font-semibold">192.168.10.42</span>
<span class="text-on-surface-variant font-body-sm text-body-sm text-[11px] truncate">Chrome 130 • macOS Tahoe</span>
</div>
</td>
</tr>
<tr x-cloak x-show="visibleCount === 0">
<td colspan="10" class="p-8 text-center text-sm text-slate-500">Tidak ada data contoh yang cocok dengan pilihan ini.</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-base text-outline">storage</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">
<span x-text="'Menampilkan ' + visibleCount + ' dari ' + rows.length + ' data contoh'">
</span>
</span>
</div>
<div class="flex items-center gap-1">
<button  aria-label="Halaman Sebelumnya" class="p-2 rounded-lg text-outline hover:bg-surface-container-high transition-colors disabled:opacity-40" disabled="" type="button" @click="unavailable('Halaman data berikutnya')">
<span class="material-symbols-outlined text-lg">chevron_left</span>
</button>
<div class="flex items-center gap-1 px-1">
<button  class="w-8 h-8 rounded-lg bg-primary-container text-on-primary font-label-sm text-label-sm font-semibold flex items-center justify-center" type="button" @click="unavailable('Halaman data berikutnya')">1</button>
<button  class="w-8 h-8 rounded-lg text-on-surface hover:bg-surface-container-high font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">2</button>
<button  class="w-8 h-8 rounded-lg text-on-surface hover:bg-surface-container-high font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">3</button>
<span class="px-1 text-outline font-mono text-xs">...</span>
<button  class="w-8 h-8 rounded-lg text-on-surface hover:bg-surface-container-high font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">231</button>
</div>
<button  aria-label="Halaman Berikutnya" class="p-2 rounded-lg text-on-surface hover:bg-surface-container-high transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">
<span class="material-symbols-outlined text-lg">chevron_right</span>
</button>
</div>
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>

