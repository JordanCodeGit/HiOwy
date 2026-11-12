<x-layouts.bk title="Kelola Sekolah" page="manage" mode="">
<main x-data="bkManage({
    categories: @js($categories),
    locations: @js($locations),
    staffMembers: @js($staffMembers),
    qrTotalScans: @js($qrTotalScans),
    reportUrl: @js($reportUrl),
    qrScanUrl: @js($qrScanUrl)
})" @keydown.escape.window="closeModal()" class="w-full bg-surface flex-1 px-8 py-6">
<div class="flex flex-col w-full">
<div class="flex flex-col gap-space-md mb-8">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
<div class="flex flex-col">
<h1 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">Jenis, Lokasi, Staff, QR</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Konfigurasi jenis aduan, titik lokasi kejadian sekolah, dan tautan kode QR pelaporan resmi.
        </p>
</div>
<div class="flex items-center gap-3 bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs self-start lg:self-center">
<span class="material-symbols-outlined text-secondary text-[22px]">account_balance</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Unit SEKOLAH</span>
<span class="font-title-md text-title-md text-on-surface font-semibold">{{ $schoolName }}</span>
</div>
</div>
</div>
<div role="tablist" aria-label="Kelola sekolah" class="flex items-center gap-2 p-1.5 bg-surface-container-low rounded-xl w-fit max-w-full overflow-x-auto mt-2">
<button @click="setTab('jenis')" :aria-selected="tab === 'jenis'" role="tab" :class="{ 'bk-tab-active': tab === 'jenis' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-semibold transition-all text-on-surface-variant cursor-pointer" id="tab-btn-jenis" type="button">
<span class="material-symbols-outlined text-[19px]">category</span>
<span>Jenis Kejadian</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary font-label-sm text-label-sm" id="badge-total-jenis" x-text="categories.length">{{ $categoriesCount }}</span>
</button>
<button @click="setTab('lokasi')" :aria-selected="tab === 'lokasi'" role="tab" :class="{ 'bk-tab-active': tab === 'lokasi' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all cursor-pointer" id="tab-btn-lokasi" type="button">
<span class="material-symbols-outlined text-[19px]">pin_drop</span>
<span>Lokasi Kejadian</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-outline font-label-sm text-label-sm" id="badge-total-lokasi" x-text="locations.length">{{ $locationsCount }}</span>
</button>
<button @click="setTab('qr')" :aria-selected="tab === 'qr'" role="tab" :class="{ 'bk-tab-active': tab === 'qr' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all cursor-pointer" id="tab-btn-qr" type="button">
<span class="material-symbols-outlined text-[19px]">qr_code_2</span>
<span>QR Sekolah</span>
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>
</button>
<button @click="setTab('staf')" :aria-selected="tab === 'staf'" role="tab" :class="{ 'bk-tab-active': tab === 'staf' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all cursor-pointer" id="tab-btn-staf" type="button">
<span class="material-symbols-outlined text-[19px]">person_add</span>
<span>Undang Staf</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary font-label-sm text-label-sm" id="badge-total-staf" x-text="staffMembers.length">{{ $staffCount }}</span>
</button>
</div>
</div>

{{-- =========================================================================
     TAB 1: JENIS KEJADIAN
     ========================================================================= --}}
<section x-cloak x-show="tab === 'jenis'" x-transition.opacity class="flex flex-col gap-6 flex" id="panel-jenis">
<div class="flex flex-col gap-6 w-full">
<div class="w-full flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50">
<div class="flex items-center gap-3">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-semibold text-on-surface">Daftar Pilihan Aduan Siswa</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm" x-text="categories.length + ' Jenis Terdaftar'">{{ $categoriesCount }} Jenis Terdaftar</span>
</div>
</div>
<button class="bg-primary text-on-primary px-4 py-2 rounded-xl text-label-md font-label-md font-semibold flex items-center gap-2 hover:bg-inverse-surface cursor-pointer shadow-xs transition-all" type="button" @click="openCategory()">
<span class="material-symbols-outlined text-[18px]">add</span>
<span>Tambah Kategori</span>
</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<th class="py-3.5 px-6 w-20 text-center">Urutan</th>
<th class="py-3.5 px-6">Nama Jenis Kejadian</th>
<th class="py-3.5 px-6 w-44">Status</th>
<th class="py-3.5 px-6 w-36 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="kategori-table-body">
<template x-for="(item, index) in categories" :key="item.id">
<tr :draggable="!item.permanent" @dragstart="startDrag($event, 'categories', item.id)" @dragover.prevent="dragOver('categories', item.id)" @drop.prevent="dropRow('categories', item.id)" @dragend="endDrag()" :class="{ 'bk-row-dragging': dragging?.id === item.id, 'bk-row-drop': dropTarget === item.id }" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6 text-center">
<div class="inline-flex items-center gap-1 cursor-grab bg-surface-container px-2.5 py-1 rounded-lg">
<span class="font-label-md text-label-md font-bold text-on-surface" x-text="index + 1">
</span>
<span x-show="item.permanent" class="material-symbols-outlined text-[16px] text-outline" title="Kategori tetap (Wajib sistem)">lock</span>
<div x-show="!item.permanent" class="flex flex-col">
<button class="text-outline hover:text-on-surface leading-none cursor-pointer" title="Naikkan urutan" type="button" @click="moveRow('categories', item.id, -1)" :disabled="item.permanent || index === 0" aria-label="Pindahkan ke atas">
<span class="material-symbols-outlined text-[14px]">expand_less</span>
</button>
<button class="text-outline hover:text-secondary leading-none cursor-pointer" title="Turunkan urutan" type="button" @click="moveRow('categories', item.id, 1)" :disabled="item.permanent || index >= categories.length - 1" aria-label="Pindahkan ke bawah">
<span class="material-symbols-outlined text-[14px]">expand_more</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-6">
<span class="font-title-md text-title-md text-on-surface font-semibold block row-label-text" x-text="item.label">
</span>
<span class="text-xs text-on-surface-variant flex items-center gap-1 mt-0.5" x-text="item.visible_to_kesiswaan ? 'Terlihat Tim Kesiswaan' : 'Khusus Konselor BK'"></span>
</td>
<td class="py-4 px-6">
<button type="button" @click="toggleCategory(item)" :disabled="item.permanent" class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold cursor-pointer transition-colors" :class="item.active ? 'text-emerald-700' : 'text-outline'" :title="item.permanent ? 'Kategori sistem tetap aktif' : 'Klik untuk mengubah status'">
<span class="w-2 h-2 rounded-full" :class="item.active ? 'bg-emerald-600' : 'bg-outline'">
</span>
<span x-text="item.active ? 'Aktif' : 'Nonaktif'">
</span>
</button>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" @click="openCategory(item)">
<span class="material-symbols-outlined text-[16px]">edit</span>
<span>Ubah</span>
</button>
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" :disabled="item.permanent" @click="deleteCategory(item)" :class="{ 'opacity-40 cursor-not-allowed': item.permanent }">
<span class="material-symbols-outlined text-[16px] staff-desktop-only">delete</span>
<span class="staff-desktop-only">Hapus</span>
</button>
</div>
</td>
</tr>
</template>
</tbody>
</table>
</div>
</div>
</div>
</div>
</section>

{{-- =========================================================================
     TAB 2: LOKASI KEJADIAN
     ========================================================================= --}}
<section x-cloak x-show="tab === 'lokasi'" x-transition.opacity class="flex flex-col gap-6" id="panel-lokasi">
<div class="w-full flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col w-full">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-semibold text-on-surface">Daftar Titik Lokasi Kejadian</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm" x-text="locations.length + ' Lokasi Terdaftar'">{{ $locationsCount }} Lokasi Terdaftar</span>
</div>
<button class="px-3.5 py-2 rounded-xl bg-secondary-fixed text-on-secondary-fixed-variant hover:bg-secondary-fixed-dim transition-all font-title-md text-title-md font-semibold flex items-center gap-1.5 cursor-pointer" type="button" @click="openLocation()">
<span class="material-symbols-outlined text-[18px]">add_location_alt</span>
<span>Tambah Titik Lokasi</span>
</button>
</div>
<div class="overflow-x-auto w-full">
<table class="w-full text-left">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<th class="py-3.5 px-6 w-20 text-center">Urutan</th>
<th class="py-3.5 px-6">Nama Titik Lokasi</th>
<th class="py-3.5 px-6 w-36">Status</th>
<th class="py-3.5 px-6 w-36 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="location-table-body">
<template x-for="(item, index) in locations" :key="item.id">
<tr :draggable="!item.permanent" @dragstart="startDrag($event, 'locations', item.id)" @dragover.prevent="dragOver('locations', item.id)" @drop.prevent="dropRow('locations', item.id)" @dragend="endDrag()" :class="{ 'bk-row-dragging': dragging?.id === item.id, 'bk-row-drop': dropTarget === item.id }" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6 text-center">
<div class="inline-flex items-center gap-1 cursor-grab bg-surface-container px-2.5 py-1 rounded-lg">
<span class="font-label-md text-label-md font-bold text-on-surface" x-text="index + 1">
</span>
<span x-show="item.permanent" class="material-symbols-outlined text-[16px] text-outline" title="Lokasi tetap">lock</span>
<div x-show="!item.permanent" class="flex flex-col">
<button class="text-outline hover:text-secondary leading-none cursor-pointer" type="button" @click="moveRow('locations', item.id, -1)" :disabled="item.permanent || index === 0" aria-label="Pindahkan ke atas">
<span class="material-symbols-outlined text-[14px]">expand_less</span>
</button>
<button class="text-outline hover:text-secondary leading-none cursor-pointer" type="button" @click="moveRow('locations', item.id, 1)" :disabled="item.permanent || index >= locations.length - 1" aria-label="Pindahkan ke bawah">
<span class="material-symbols-outlined text-[14px]">expand_more</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-6">
<span class="font-title-md text-title-md font-semibold text-on-surface block" x-text="item.label">
</span>
</td>
<td class="py-4 px-6">
<button type="button" @click="toggleLocation(item)" class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold cursor-pointer transition-colors" :class="item.active ? 'text-emerald-700' : 'text-outline'" title="Klik untuk mengubah status">
<span class="w-2 h-2 rounded-full" :class="item.active ? 'bg-emerald-600' : 'bg-outline'">
</span>
<span x-text="item.active ? 'Aktif' : 'Nonaktif'">
</span>
</button>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" @click="openLocation(item)">
<span class="material-symbols-outlined text-[16px]">edit</span>
<span>Ubah</span>
</button>
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" :disabled="item.permanent" @click="deleteLocation(item)">
<span class="material-symbols-outlined text-[16px] staff-desktop-only">delete</span>
<span class="staff-desktop-only">Hapus</span>
</button>
</div>
</td>
</tr>
</template>
</tbody>
</table>
</div>
</div>
</div>
</section>

{{-- =========================================================================
     TAB 3: QR SEKOLAH (1 QR CODE UNTUK SEMUA TITIK SEKOLAH)
     ========================================================================= --}}
<section x-cloak x-show="tab === 'qr'" x-transition.opacity class="flex flex-col gap-6" id="panel-qr">
<div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden flex flex-col lg:flex-row">
<div class="w-full lg:w-[380px] xl:w-[420px] shrink-0 p-8 flex flex-col justify-between items-center text-center border-b lg:border-b-0 lg:border-r border-surface-container/60 bg-surface-container-lowest">
<div class="w-full flex flex-col items-center">
<div class="w-full h-1.5 rounded-full bg-secondary mb-6">
</div>
<div class="flex items-center gap-3 mb-5 self-start text-left">
<div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[28px]">school</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Tautan Aduan Resmi</span>
<span class="font-title-lg text-title-lg font-bold text-on-surface">{{ $schoolName }}</span>
</div>
</div>
<div class="bk-hover-card p-5 bg-surface-container-lowest rounded-2xl border border-surface-container shadow-xs my-2 flex flex-col items-center justify-center min-w-[240px] min-h-[240px]" x-html="qrSvgHtml">
</div>
<div class="mt-4 p-4 bg-surface-container rounded-xl w-full flex flex-col items-center justify-center text-center">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Tautan Formulir Pengaduan Siswa</span>
<div class="flex items-center gap-2 mt-1.5 max-w-full">
<a :href="reportUrl" target="_blank" class="font-title-md text-title-md font-semibold text-secondary hover:underline truncate max-w-[270px]" x-text="reportUrl">
  {{ $reportUrl }}
</a>
<button class="text-secondary hover:text-on-surface transition-colors p-1.5 rounded-lg hover:bg-surface-container-high cursor-pointer shrink-0" title="Salin tautan formulir" type="button" @click="copyReportUrl()" aria-label="Salin tautan formulir">
<span class="material-symbols-outlined text-[19px]">content_copy</span>
</button>
</div>
<span class="font-body-xs text-body-xs text-on-surface-variant mt-1">
  Siswa dapat langsung mengakses formulir melalui tautan di atas atau memindai kode QR.
</span>
</div>
</div>
</div>
<div class="p-8 flex flex-col justify-between flex-1 gap-6 bg-surface-container-low/30">
<div class="flex flex-col gap-4">
<div class="flex items-center gap-2.5 text-secondary">
<span class="material-symbols-outlined text-[24px]">print</span>
<span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Panduan Pencetakan &amp; Publikasi</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Aset Cetak &amp; Distribusi Pengumuman</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-1.5 leading-relaxed">
          Gunakan materi resmi format A4 untuk disosialisasikan dan ditempelkan di majalah dinding (mading), papan pengumuman ruang BK, perpustakaan, lobi sekolah, dan setiap ruang kelas.
        </p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
<div class="p-3.5 bg-surface-container-lowest rounded-xl border border-surface-container flex items-start gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[18px]">verified</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md font-semibold text-on-surface">Resolusi Siap Cetak</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Format vector 300 DPI tidak pecah saat dicetak poster besar.</span>
</div>
</div>
<div class="p-3.5 bg-surface-container-lowest rounded-xl border border-surface-container flex items-start gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[18px]">campaign</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md font-semibold text-on-surface">Kanal Aman &amp; Terlindungi</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Mengarahkan siswa langsung ke formulir terenkripsi BK.</span>
</div>
</div>
</div>

{{-- METRIK: TOTAL AKSES PEMINDAIAN QR SEKOLAH (AKTIF DENGAN SIMULASI & AUTO-REFRESH) --}}
<div class="p-4 bg-surface-container-lowest rounded-xl border border-surface-container flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs mt-1">
<div class="flex items-center gap-3.5">
<div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[26px]">qr_code_scanner</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Total Akses Pemindaian QR</span>
<button type="button" @click="fetchQrStats()" title="Segarkan jumlah pemindaian" class="text-outline hover:text-secondary p-0.5 rounded cursor-pointer transition-colors">
<span class="material-symbols-outlined text-[16px]">refresh</span>
</button>
</div>
<div class="flex items-baseline gap-1.5 mt-0.5">
<span class="font-headline-md text-headline-md font-bold text-on-surface" x-text="qrTotalScans.toLocaleString('id-ID')">{{ number_format($qrTotalScans) }}</span>
<span class="font-body-md text-body-md text-on-surface-variant font-medium">kali dipindai siswa</span>
</div>
</div>
</div>
<div class="flex items-center gap-2.5">
<button type="button" @click="testScanQr()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold border border-outline-variant/30 transition-all cursor-pointer shadow-2xs" title="Buka tautan pindai untuk simulasi penambahan pemindaian">
<span class="material-symbols-outlined text-[16px] text-secondary">open_in_new</span>
<span>Uji Pindai (Test +1)</span>
</button>
<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
<span>Kanal Aktif &amp; Siap Dipindai</span>
</span>
</div>
</div>

</div>
<div class="pt-4 border-t border-surface-container/60 flex flex-col sm:flex-row items-center gap-3">
<button class="w-full sm:w-auto flex-1 px-5 py-3.5 bg-primary hover:bg-inverse-surface text-on-primary rounded-xl font-title-md text-title-md font-semibold transition-all shadow-xs flex items-center justify-center gap-2.5 cursor-pointer" type="button" @click="openPosterA4()">
<span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
<span>Unduh Poster Panduan PDF (A4)</span>
</button>
<button class="w-full sm:w-auto flex-1 px-5 py-3.5 bg-secondary-fixed text-on-secondary-fixed-variant hover:bg-secondary-fixed-dim rounded-xl font-title-md text-title-md font-semibold transition-all flex items-center justify-center gap-2.5 cursor-pointer" type="button" @click="downloadQrPng()">
<span class="material-symbols-outlined text-[20px]">image</span>
<span>Unduh Gambar QR (PNG)</span>
</button>
</div>
</div>
</div>
</section>

{{-- =========================================================================
     TAB 4: UNDANG STAF & HAK AKSES
     ========================================================================= --}}
<section x-cloak x-show="tab === 'staf'" x-transition.opacity class="flex flex-col gap-6" id="panel-staf">
<div class="w-full flex flex-col gap-5">
<div class="bg-surface-container-lowest rounded-2xl shadow-xs overflow-hidden flex flex-col w-full border border-surface-container/60">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50 border-b border-surface-container/60">
<div class="flex items-center gap-2.5">
<span class="text-base font-bold text-on-surface">Daftar Staf &amp; Hak Akses</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-xs font-semibold" id="staf-count-pill" x-text="staffMembers.length + ' Staf Terdaftar'">{{ $staffCount }} Staf Terdaftar</span>
</div>
<button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white hover:bg-slate-800 text-sm font-semibold transition-all shadow-xs hover:shadow-sm active:scale-[0.99] cursor-pointer" type="button" @click="openInvite()">
<span class="material-symbols-outlined text-[18px]">person_add</span>
<span>Undang Staf Baru</span>
</button>
</div>
<div class="overflow-x-auto w-full">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider border-b border-surface-container/60">
<th class="py-4 px-6 font-semibold">Nama &amp; Email</th>
<th class="py-4 px-6 font-semibold">Peran &amp; Hak Akses</th>
<th class="py-4 px-6 font-semibold">Tanggal Diundang</th>
<th class="py-4 px-6 font-semibold w-48">Status Undangan / Akun</th>
<th class="py-4 px-6 font-semibold text-right w-44">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="staf-table-body">
<template x-for="item in staffMembers" :key="item.id">
<tr class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-3.5">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center font-bold text-on-surface-variant text-sm shrink-0 shadow-xs" x-text="item.initials || 'ST'"></div>
<div class="flex flex-col min-w-0">
<span class="text-sm font-semibold text-on-surface leading-snug" x-text="item.name"></span>
<span class="text-xs text-on-surface-variant" x-text="item.email"></span>
</div>
</div>
</td>
<td class="py-4 px-6">
<div class="flex flex-col gap-1 items-start">
<template x-if="item.role === 'kesiswaan'">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed/50 text-xs font-semibold text-secondary">
<span class="material-symbols-outlined text-[15px]">security</span>
<span>Tim Kesiswaan</span>
</span>
</template>
<template x-if="item.role === 'walikelas'">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-xs font-semibold text-on-surface-variant">
<span class="material-symbols-outlined text-[15px]">school</span>
<span x-text="'Wali Kelas ' + (item.class_name || 'Binaan')"></span>
</span>
</template>
</div>
</td>
<td class="py-4 px-6">
<div class="flex flex-col">
<span class="text-sm font-medium text-on-surface" x-text="item.invited_fmt || 'Hari Ini'"></span>
<span class="text-xs text-outline" x-text="item.status === 'aktif' ? 'Terverifikasi' : 'Email Terkirim'"></span>
</div>
</td>
<td class="py-4 px-6">
<template x-if="item.status === 'aktif'">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold w-fit">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
<span>Aktif</span>
</span>
</template>
<template x-if="item.status === 'pending'">
<span class="inline-flex items-center gap-1.5 text-secondary text-xs font-semibold px-3 py-1 rounded-full bg-secondary-fixed/50 w-fit">
<span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
<span>Menunggu Penerimaan</span>
</span>
</template>
<template x-if="item.status === 'nonaktif'">
<span class="inline-flex items-center gap-1.5 text-outline text-xs font-semibold px-3 py-1 rounded-full bg-surface-container w-fit">
<span class="w-2 h-2 rounded-full bg-outline"></span>
<span>Nonaktif</span>
</span>
</template>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button x-show="item.status === 'pending'" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-secondary-fixed text-secondary text-xs font-semibold transition-colors cursor-pointer" type="button" @click="resendInvite(item)" title="Kirim ulang email undangan">
<span class="material-symbols-outlined text-[16px]">forward_to_inbox</span>
<span>Kirim Ulang</span>
</button>
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary text-xs font-semibold transition-colors cursor-pointer" type="button" @click="openEditStaff(item)">
<span class="material-symbols-outlined text-[16px]">tune</span>
<span>Ubah</span>
</button>
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error text-xs font-semibold transition-colors cursor-pointer" type="button" @click="openDeleteStaff(item)">
<span class="material-symbols-outlined text-[16px]">delete</span>
<span>Hapus</span>
</button>
</div>
</td>
</tr>
</template>
</tbody>
</table>
</div>
</div>
</div>
</section>

{{-- =========================================================================
     MODAL: TAMBAH / UBAH KATEGORI ADUAN
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-kategori'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto transition-opacity duration-200" id="modal-kategori">
<div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
<div class="flex gap-3 items-center">
<div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]" id="modal-kategori-icon" x-text="editing ? 'edit' : 'add_circle'">add_circle</span>
</div>
<div class="flex flex-col">
<h3 x-text="editing ? 'Ubah Kategori' : 'Tambah Kategori Baru'" class="font-headline-sm text-headline-sm font-bold text-on-surface" id="modal-kategori-title">Tambah Kategori Baru</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant" id="modal-kategori-subtitle">Konfigurasikan label jenis aduan yang akan muncul di alur pelaporan siswa.</p>
</div>
</div>
<button class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="p-6 flex flex-col gap-4">
<div class="flex flex-col gap-1.5">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md font-semibold text-on-surface" for="modal-input-label">
  Nama Jenis Kejadian (Label Tampilan Siswa) <span class="text-error">*</span>
</label>
<span x-text="categoryLabel.length + '/80'" class="font-label-sm text-label-sm text-outline" id="modal-char-count">0/80</span>
</div>
<input x-model="categoryLabel" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="modal-input-label" required maxlength="80" placeholder="Contoh: Pemalakan atau Intimidasi Kelompok" type="text">
<span class="font-body-sm text-body-sm text-on-surface-variant">Label ini langsung ditampilkan pada daftar opsi saat siswa melapor.</span>
</div>
<div class="p-3.5 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-center justify-between gap-4">
<div class="flex flex-col">
<span class="font-title-md text-title-md font-semibold text-on-surface">Status Kategori</span>
<span x-text="categoryActive ? 'Aktif (Ditampilkan ke Siswa)' : 'Disembunyikan / Nonaktif sementara'" class="font-body-sm text-body-sm text-on-surface-variant" id="modal-status-text">Aktif (Ditampilkan ke Siswa)</span>
</div>
<label class="relative inline-flex items-center cursor-pointer shrink-0">
<input x-model="categoryActive" :disabled="categoryPermanent" class="sr-only peer" id="modal-toggle-status" type="checkbox">
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
<div class="p-3.5 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-center justify-between gap-4">
<div class="flex flex-col">
<span class="font-title-md text-title-md font-semibold text-on-surface">Terlihat Tim Kesiswaan</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Izinkan staf Kesiswaan memonitor jenis kasus ini.</span>
</div>
<label class="relative inline-flex items-center cursor-pointer shrink-0">
<input x-model="categoryVisibleKesiswaan" class="sr-only peer" type="checkbox">
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container font-title-md text-title-md font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-primary text-on-primary font-title-md text-title-md font-semibold hover:bg-inverse-surface active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="saveCategory()">
<span class="material-symbols-outlined text-[19px]">check</span>
<span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
</button>
</div>
</div>
</div>
</template>

{{-- =========================================================================
     MODAL: TAMBAH / UBAH LOKASI KEJADIAN
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-labelledby="location-modal-title" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-tambah-lokasi'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4" id="modal-tambah-lokasi">
<div class="bg-surface-container-lowest rounded-2xl p-6 max-w-md w-full shadow-2xl flex flex-col gap-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2 text-secondary">
<span class="material-symbols-outlined text-[22px]" x-text="editing ? 'edit_location_alt' : 'add_location'">add_location</span>
<h4 id="location-modal-title" class="font-title-lg text-title-lg font-bold text-on-surface" x-text="editing ? 'Ubah Titik Lokasi' : 'Tambah Titik Lokasi Baru'">Tambah Titik Lokasi Baru</h4>
</div>
<button class="text-outline hover:text-on-surface cursor-pointer" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="flex flex-col gap-3">
<div class="flex flex-col gap-1">
<label for="new-loc-name" class="font-label-md text-label-md font-semibold text-on-surface">Nama Titik Lokasi <span class="text-error">*</span></label>
<input x-model="locationLabel" class="w-full px-3.5 py-2.5 bg-surface-container-low text-on-surface rounded-xl font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40" id="new-loc-name" required maxlength="80" placeholder="Contoh: Perpustakaan &amp; Ruang Baca" type="text">
</div>
</div>
<div class="flex items-center justify-end gap-2 pt-2">
<button class="px-4 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container font-title-md text-title-md font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">
  Batal
</button>
<button :disabled="saving" class="px-4 py-2.5 rounded-xl bg-primary text-on-primary font-title-md text-title-md font-semibold hover:bg-inverse-surface transition-colors shadow-xs cursor-pointer" type="button" @click="saveLocation()">
  <span x-text="saving ? 'Menyimpan...' : (editing ? 'Simpan Perubahan' : 'Tambah Lokasi')">Tambah Lokasi</span>
</button>
</div>
</div>
</div>
</template>

{{-- =========================================================================
     MODAL: UBAH DATA & HAK AKSES STAF
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-edit-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-edit-staf">
<div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
<div class="flex gap-3 items-center">
<div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">manage_accounts</span>
</div>
<div class="flex flex-col">
<h3 class="font-display text-base font-bold text-on-surface">Ubah Data &amp; Hak Akses Staf</h3>
<p class="text-xs text-on-surface-variant">Perbarui identitas, peran penugasan, dan status akses akun staf.</p>
</div>
</div>
<button class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="p-6 flex flex-col gap-4">
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface" for="edit-staf-nama">Nama Lengkap Staf <span class="text-error">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">badge</span>
<input x-model="staffName" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="edit-staf-nama" placeholder="Nama lengkap berserta gelar" type="text">
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface" for="edit-staf-email">Email Resmi Sekolah <span class="text-error">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">mail</span>
<input x-model="staffEmail" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="edit-staf-email" required placeholder="email.guru@sekolah.sch.id" type="email">
</div>
</div>
<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface">Peran &amp; Hak Akses <span class="text-error">*</span>
</label>
<div class="grid grid-cols-1 gap-2.5">
<label :class="{ 'bk-choice-active': staffRole === 'kesiswaan' }" class="flex items-start gap-3 p-3.5 rounded-xl border hover:bg-surface-container-low/40 cursor-pointer transition-all border-surface-container" id="edit-role-label-kesiswaan">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" id="edit-radio-kesiswaan" name="edit-role-choice" type="radio" value="kesiswaan">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Tim Kesiswaan</span>
<span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-secondary text-[11px] font-semibold">Eskalasi Kasus</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Akses statistik, tindak lanjut umum, dan monitoring laporan sekolah.</span>
</div>
</label>
<label :class="{ 'bk-choice-active': staffRole === 'walikelas' }" class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all border-secondary bg-surface-container-low/50" id="edit-role-label-walikelas">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" id="edit-radio-walikelas" name="edit-role-choice" type="radio" value="walikelas">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Wali Kelas</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-[11px] font-semibold">Siswa Binaan</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Akses khusus laporan ringkasan siswa kelas binaannya saja.</span>
</div>
</label>
</div>
</div>
<div x-cloak x-show="staffRole === 'walikelas'" x-transition.opacity class="flex flex-col gap-1.5" id="edit-wrapper-kelas-binaan">
<label class="text-xs font-semibold text-on-surface" for="edit-select-kelas-binaan">Pilih Kelas Binaan <span class="text-error">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
<select x-model="staffClass" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 appearance-none transition-all cursor-pointer" id="edit-select-kelas-binaan">
<option value="7A">Kelas 7A</option>
<option value="7B">Kelas 7B</option>
<option value="8A">Kelas 8A</option>
<option value="8B">Kelas 8B</option>
<option value="9A">Kelas 9A</option>
<option value="9B">Kelas 9B</option>
<option value="10A">Kelas 10A</option>
<option value="11A">Kelas 11A</option>
<option value="12A">Kelas 12A</option>
</select>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface" for="edit-staf-status">Status Akun / Undangan</label>
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
<select x-model="staffStatus" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 appearance-none transition-all cursor-pointer" id="edit-staf-status">
<option value="aktif">Aktif (Akses Terverifikasi)</option>
<option value="pending">Menunggu Penerimaan Undangan</option>
<option value="nonaktif">Nonaktif Sementara</option>
</select>
</div>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-slate-800 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="saveStaff()">
<span class="material-symbols-outlined text-[19px]">check</span>
<span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
</button>
</div>
</div>
</div>
</template>

{{-- =========================================================================
     MODAL: HAPUS / CABUT AKSES STAF
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-hapus-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-hapus-staf">
<div class="bg-surface-container-lowest rounded-2xl max-w-md w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="p-6 flex flex-col items-center text-center gap-4">
<div class="w-14 h-14 rounded-2xl bg-error-container text-error flex items-center justify-center shrink-0 shadow-xs">
<span class="material-symbols-outlined text-[32px]">warning</span>
</div>
<div class="flex flex-col gap-1.5">
<h3 class="font-display text-base font-bold text-on-surface">Hapus Akses Staf?</h3>
<p class="text-sm text-on-surface-variant leading-relaxed">Akses portal unit sekolah untuk <strong x-text="staffName" class="text-on-surface font-semibold" id="delete-staf-target-name">Staf</strong> akan dicabut secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
</div>
<div class="w-full p-3 bg-error-container/30 border border-error/20 rounded-xl text-left flex items-start gap-2.5">
<span class="material-symbols-outlined text-error text-[18px] shrink-0 mt-0.5">shield</span>
<span class="text-xs text-error leading-snug">Data laporan &amp; catatan riwayat tindak lanjut yang pernah ditangani staf ini tetap tersimpan aman di arsip sekolah.</span>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-red-700 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="confirmDeleteStaff()">
<span class="material-symbols-outlined text-[19px]">delete_forever</span>
<span x-text="saving ? 'Menghapus...' : 'Ya, Hapus Akses'">Ya, Hapus Akses</span>
</button>
</div>
</div>
</div>
</template>

{{-- =========================================================================
     MODAL: UNDANG STAF BARU
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-undang-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-undang-staf">
<div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
<div class="flex gap-3 items-center">
<div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">person_add</span>
</div>
<div class="flex flex-col">
<h3 class="font-display text-base font-bold text-on-surface">Undang Staf Unit Sekolah</h3>
<p class="text-xs text-on-surface-variant">Kirim undangan akses portal melalui email resmi guru atau tenaga pendidik.</p>
</div>
</div>
<button class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="p-6 flex flex-col gap-4">
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface" for="invite-nama-input">Nama Lengkap Staf <span class="text-outline text-[11px]">(Opsional)</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">badge</span>
<input x-model="staffName" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="invite-nama-input" placeholder="Nama lengkap berserta gelar" type="text">
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface" for="invite-email-input">Alamat Email Staf <span class="text-error">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">mail</span>
<input x-model="staffEmail" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="invite-email-input" required placeholder="nama.guru@sekolah.sch.id" type="email">
</div>
<span class="text-xs text-on-surface-variant">Pastikan menggunakan alamat email aktif milik guru atau tenaga kependidikan.</span>
</div>
<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface">Peran &amp; Hak Akses <span class="text-error">*</span>
</label>
<div class="grid grid-cols-1 gap-2.5">
<label :class="{ 'bk-choice-active': staffRole === 'kesiswaan' }" class="flex items-start gap-3 p-3.5 rounded-xl border border-secondary bg-surface-container-low/50 cursor-pointer transition-all" id="invite-role-label-kesiswaan">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" id="invite-radio-kesiswaan" name="invite-role-choice" type="radio" value="kesiswaan">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Tim Kesiswaan</span>
<span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-secondary text-[11px] font-semibold">Eskalasi Kasus</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Akses statistik, tindak lanjut umum, dan monitoring laporan sekolah.</span>
</div>
</label>
<label :class="{ 'bk-choice-active': staffRole === 'walikelas' }" class="flex items-start gap-3 p-3.5 rounded-xl border border-surface-container hover:bg-surface-container-low/40 cursor-pointer transition-all" id="invite-role-label-walikelas">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" id="invite-radio-walikelas" name="invite-role-choice" type="radio" value="walikelas">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Wali Kelas</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-[11px] font-semibold">Siswa Binaan</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Akses khusus laporan ringkasan siswa kelas binaannya saja.</span>
</div>
</label>
</div>
</div>
<div x-cloak x-show="staffRole === 'walikelas'" x-transition.opacity class="flex flex-col gap-1.5" id="invite-wrapper-kelas-binaan">
<label class="text-xs font-semibold text-on-surface" for="invite-select-kelas-binaan">Pilih Kelas Binaan <span class="text-error">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
<select x-model="staffClass" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 appearance-none transition-all cursor-pointer" id="invite-select-kelas-binaan">
<option value="7A">Kelas 7A</option>
<option value="7B">Kelas 7B</option>
<option value="8A">Kelas 8A</option>
<option value="8B">Kelas 8B</option>
<option value="9A">Kelas 9A</option>
<option value="9B">Kelas 9B</option>
<option value="10A">Kelas 10A</option>
<option value="11A">Kelas 11A</option>
<option value="12A">Kelas 12A</option>
</select>
</div>
</div>
<div class="p-3.5 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-start gap-3">
<span class="material-symbols-outlined text-secondary text-[20px] shrink-0 mt-0.5">info</span>
<p class="text-xs text-on-surface-variant">Tautan aktivasi berlaku selama 7 hari. Rekan staf akan diarahkan membuat kata sandi secara mandiri setelah memverifikasi email.</p>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-slate-800 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="submitInvite()">
<span class="material-symbols-outlined text-[19px]">send</span>
<span x-text="saving ? 'Mengirim...' : 'Kirim Undangan'">Kirim Undangan</span>
</button>
</div>
</div>
</div>
</template>

<p class="mt-4 text-xs text-outline" aria-live="polite" x-text="orderMessage">
</p>
</div>
</main>
</x-layouts.bk>
