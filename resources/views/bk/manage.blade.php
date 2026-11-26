<x-layouts.bk title="Kelola Sekolah" page="manage" mode="">
<main x-data="bkManage({
    categories: @js($categories),
    locations: @js($locations),
    staffMembers: @js($staffMembers),
    reportUrl: @js($reportUrl),
    qrScanUrl: @js($qrScanUrl),
    readingDayStart: @js(old('reading_day_start', $schoolConfig['reading_day_start'] ?? 'Senin')),
    readingDayEnd: @js(old('reading_day_end', $schoolConfig['reading_day_end'] ?? 'Jumat')),
    readingTimeStart: @js(old('reading_time_start', $schoolConfig['reading_time_start'] ?? '07:00')),
    readingTimeEnd: @js(old('reading_time_end', $schoolConfig['reading_time_end'] ?? '15:00')),
    schoolLogoUrl: @js($schoolConfig['school_logo_url'] ?? '')
})" @keydown.escape.window="closeModal()" class="w-full bg-surface flex-1 px-8 py-6">
<div class="flex flex-col w-full">
<div class="flex flex-col gap-space-md mb-8">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
<div class="flex flex-col">
<h1 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">Kelola Sekolah</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Konfigurasi jenis aduan, titik lokasi kejadian sekolah, tautan QR, staf, dan kebijakan sekolah.
        </p>
</div>
<div class="flex flex-wrap items-center gap-3 self-start lg:self-center">
<div class="flex items-center gap-3 bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs">
<span class="material-symbols-outlined text-secondary text-[22px]">account_balance</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Unit SEKOLAH</span>
<span class="font-title-md text-title-md text-on-surface font-semibold">{{ $schoolName }}</span>
</div>
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
<button @click="setTab('sekolah')" :aria-selected="tab === 'sekolah'" role="tab" :class="{ 'bk-tab-active': tab === 'sekolah' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all cursor-pointer" id="tab-btn-sekolah" type="button">
<span class="material-symbols-outlined text-[19px]">tune</span>
<span>Sekolah &amp; Kebijakan</span>
</button>
</div>
</div>

@if(session('frontend_notice'))
<div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3">
    <div class="flex items-center gap-2.5">
        <span class="material-symbols-outlined text-lg text-emerald-600">check_circle</span>
        <span>{{ session('frontend_notice') }}</span>
    </div>
    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><span class="material-symbols-outlined text-base">close</span></button>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
    <span class="material-symbols-outlined text-lg text-rose-600">error</span>
    <span>{{ $errors->first() }}</span>
</div>
@endif

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
<th class="py-3.5 px-6">Nama Jenis Kejadian</th>
<th class="py-3.5 px-6 w-40 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="kategori-table-body">
<template x-for="item in categories" :key="item.id">
<tr class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md text-on-surface font-semibold row-label-text" x-text="item.label"></span>
<span x-show="item.permanent" class="material-symbols-outlined text-[15px] text-outline" title="Kategori tetap (Wajib sistem)">lock</span>
</div>
<div class="flex flex-wrap items-center gap-2 mt-1.5">
<template x-if="!item.visible_to_kesiswaan">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold border border-slate-200">
<span class="material-symbols-outlined text-[13px]">lock</span>
<span>Privasi Khusus BK</span>
</span>
</template>
<template x-if="item.permendikbud_type">
<span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-medium border border-blue-100" x-text="item.permendikbud_type"></span>
</template>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" @click="openCategory(item)">
<span class="material-symbols-outlined text-[16px]">edit</span>
<span>Ubah</span>
</button>
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" :disabled="item.permanent" @click="openDeleteCategory(item)" :class="{ 'opacity-40 cursor-not-allowed': item.permanent }">
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
<th class="py-3.5 px-6">Nama Titik Lokasi</th>
<th class="py-3.5 px-6 w-36">Status</th>
<th class="py-3.5 px-6 w-40 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="location-table-body">
<template x-for="item in locations" :key="item.id">
<tr class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
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
<button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors cursor-pointer" type="button" :disabled="item.permanent" @click="openDeleteLocation(item)" :class="{ 'opacity-40 cursor-not-allowed': item.permanent }">
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
     TAB 3: QR SEKOLAH & POSTER PANDUAN PELAPORAN SISWA
     ========================================================================= --}}
<section x-cloak x-show="tab === 'qr'" x-transition.opacity class="flex flex-col gap-6" id="panel-qr">
<div class="school-qr-layout flex flex-col gap-5 lg:flex-row">
<div class="w-full lg:w-[360px] xl:w-[390px] shrink-0 p-7 flex flex-col justify-between items-center text-center border-b lg:border-b-0 lg:border-r border-surface-container/60 bg-surface-container-lowest">
<div class="w-full flex flex-col items-center">
<div class="w-full h-1.5 rounded-full bg-secondary mb-5">
</div>
<div class="flex items-center gap-3 mb-4 self-start text-left">
<div class="w-11 h-11 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary shrink-0 overflow-hidden">
@if(!empty($schoolConfig['school_logo_url']))
<img src="{{ $schoolConfig['school_logo_url'] }}" alt="{{ $schoolName }}" class="w-full h-full object-contain p-1">
@else
<span class="material-symbols-outlined text-[26px]">school</span>
@endif
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Tautan Aduan Resmi</span>
<span class="font-title-lg text-title-lg font-bold text-on-surface">{{ $schoolName }}</span>
</div>
</div>
<div id="qr-main-svg" class="bk-hover-card p-4 bg-surface-container-lowest rounded-2xl border border-surface-container shadow-xs my-2 flex flex-col items-center justify-center min-w-[232px] min-h-[232px]" x-html="qrSvgHtml">
</div>
<div class="mt-4 p-4 bg-surface-container rounded-xl w-full flex flex-col items-center justify-center text-center">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Tautan Formulir Pengaduan Siswa</span>
<div class="flex items-center gap-2 mt-1.5 max-w-full">
<a :href="reportUrl" target="_blank" class="font-title-md text-title-md font-semibold text-secondary hover:underline truncate max-w-[240px]" x-text="reportUrl">
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
<button class="w-full mt-4 px-5 py-3 bg-secondary-fixed text-on-secondary-fixed-variant hover:bg-secondary-fixed-dim rounded-xl font-title-md text-title-md font-semibold transition-all flex items-center justify-center gap-2 cursor-pointer" type="button" @click="downloadQrPng()">
<span class="material-symbols-outlined text-[20px]">image</span>
<span>Unduh Gambar QR (PNG)</span>
</button>
</div>
</div>
<div class="p-7 flex flex-col justify-between flex-1 gap-5 bg-surface-container-low/30">
<div class="flex flex-col gap-3">
<div class="flex flex-wrap items-center justify-between gap-2">
<div>
<div class="flex items-center gap-2 text-secondary">
<span class="material-symbols-outlined text-[20px]">campaign</span>
<span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Poster Panduan Pelaporan Siswa</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-0.5">Pratinjau Poster Siap Cetak (A4 Portrait)</h3>
</div>
<button class="px-4 py-2.5 bg-primary hover:bg-inverse-surface text-on-primary rounded-xl text-xs font-semibold transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="openPosterA4()">
<span class="material-symbols-outlined text-[18px]">print</span>
<span>Buka &amp; Cetak Poster PDF</span>
</button>
</div>

{{-- Inline Pratinjau Poster Panduan Pelaporan Siswa (A4 Portrait) --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden max-w-md mx-auto w-full">
<div class="h-2 w-full bg-gradient-to-r from-[#006398] to-sky-400"></div>
<div class="p-5 flex flex-col gap-3.5">
<div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-200">
<div class="flex items-center gap-2.5">
<div class="w-9 h-9 rounded-lg bg-sky-100 text-[#006398] flex items-center justify-center shrink-0 overflow-hidden">
@if(!empty($schoolConfig['school_logo_url']))
<img src="{{ $schoolConfig['school_logo_url'] }}" alt="{{ $schoolName }}" class="w-full h-full object-contain p-1">
@else
<span class="material-symbols-outlined text-[20px]">school</span>
@endif
</div>
<div>
<p class="text-xs font-extrabold text-slate-900 leading-tight">{{ $schoolName }}</p>
<p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Layanan Bimbingan &amp; Konseling (BK) @if(!empty($schoolConfig['emergency_contact'])) • Darurat: {{ $schoolConfig['emergency_contact'] }} @endif</p>
</div>
</div>
<span class="px-2.5 py-0.5 rounded-full bg-[#006398] text-white text-[10px] font-bold">HiOwy!</span>
</div>

<div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3">
<div class="flex-1 min-w-0">
<span class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[9px] font-bold uppercase tracking-wider mb-1">Kanal Aman &amp; Terlindungi</span>
<h4 class="text-sm font-extrabold text-slate-900 leading-snug">Punya Masalah di Sekolah? Kamu Tidak Sendirian.</h4>
<p class="text-[10px] text-slate-600 mt-1 leading-relaxed">Jam baca BK: {{ $schoolConfig['reading_hours'] }}.</p>
</div>
<img src="{{ asset('maskot_owy/maskot_6.png') }}" alt="Maskot Owy" class="w-14 h-14 object-contain shrink-0 drop-shadow-xs">
</div>

<div class="bg-gradient-to-b from-sky-50/80 to-slate-50 border-2 border-dashed border-sky-300 rounded-2xl p-4 flex flex-col items-center justify-center text-center">
<span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full bg-[#006398] text-white text-[10px] font-bold uppercase tracking-wide mb-2.5">
<span class="material-symbols-outlined text-[13px]">qr_code_scanner</span>
<span>Pindai Kode QR di Sini</span>
</span>
<div class="bg-white p-2.5 rounded-xl border border-sky-200 shadow-xs" x-html="posterQrSvgHtml"></div>
<p class="text-[10px] text-slate-600 mt-2.5 break-all">Arahkan kamera ponselmu atau buka:<br><strong class="text-[#006398]" x-text="reportUrl">{{ $reportUrl }}</strong></p>
</div>

<div class="grid grid-cols-3 gap-2">
<div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
<span class="w-5 h-5 rounded-full bg-[#006398] text-white text-[10px] font-extrabold inline-flex items-center justify-center mb-1">1</span>
<p class="text-[11px] font-bold text-slate-900 leading-tight">Pindai Kode QR</p>
<p class="text-[9.5px] text-slate-500 leading-tight mt-0.5">Gunakan kamera HP tanpa instal aplikasi.</p>
</div>
<div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
<span class="w-5 h-5 rounded-full bg-[#006398] text-white text-[10px] font-extrabold inline-flex items-center justify-center mb-1">2</span>
<p class="text-[11px] font-bold text-slate-900 leading-tight">Isi Form Pengaduan</p>
<p class="text-[9.5px] text-slate-500 leading-tight mt-0.5">Pilih kejadian &amp; lokasi, bisa anonim.</p>
</div>
<div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
<span class="w-5 h-5 rounded-full bg-[#006398] text-white text-[10px] font-extrabold inline-flex items-center justify-center mb-1">3</span>
<p class="text-[11px] font-bold text-slate-900 leading-tight">Didampingi Guru BK</p>
<p class="text-[9.5px] text-slate-500 leading-tight mt-0.5">Simpan kode tiket &amp; pantau respons BK.</p>
</div>
</div>

<div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center gap-2 text-[10px] text-emerald-900">
<span class="material-symbols-outlined text-emerald-600 text-[18px] shrink-0">verified_user</span>
<span><strong>Jaminan Kerahasiaan:</strong> Identitas dan cerita pelapor terenkripsi serta dilindungi sesuai kode etik konseling sekolah.</span>
</div>
</div>
</div>
</div>
<p class="text-xs text-on-surface-variant">
  Tempelkan poster A4 Portrait ini di mading, ruang kelas, papan pengumuman BK, perpustakaan, dan area strategis sekolah.
</p>
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
<template x-if="item.role === 'bk'">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-xs font-semibold text-blue-700 border border-blue-100">
<span class="material-symbols-outlined text-[15px]">psychology</span>
<span>Guru BK / Konselor</span>
</span>
</template>
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
<button x-show="item.status === 'aktif'" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-amber-50 text-amber-700 text-xs font-semibold transition-colors cursor-pointer" type="button" @click="resetStaffAccess(item)" title="Reset kata sandi dan 2FA staf ini">
<span class="material-symbols-outlined text-[16px]">lock_reset</span>
<span>Reset Akses</span>
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
     TAB 5: KONFIGURASI SEKOLAH & KEBIJAKAN
     ========================================================================= --}}
<section x-cloak x-show="tab === 'sekolah'" x-transition.opacity class="flex flex-col gap-6" id="panel-sekolah">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<form method="POST" action="{{ route('bk.manage.settings') }}" enctype="multipart/form-data" class="lg:col-span-8 bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden flex flex-col">
@csrf
<div class="px-6 py-4 bg-surface-container-low/50 border-b border-surface-container/60 flex items-center justify-between">
<div>
<h2 class="font-title-md text-title-md font-bold text-on-surface">Identitas Sekolah, Layanan &amp; Privasi</h2>
<p class="text-xs text-on-surface-variant">Pengaturan ini tampil pada portal pelapor, lacak aduan, dan poster QR sekolah.</p>
</div>
</div>
<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
<div class="flex flex-col gap-1.5">
<label for="cfg-school-name" class="text-xs font-semibold text-on-surface">Nama Sekolah</label>
<input id="cfg-school-name" name="school_name" type="text" required maxlength="120" value="{{ old('school_name', $schoolConfig['school_name']) }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-sm">
</div>
<div class="flex flex-col gap-1.5">
<label for="cfg-emergency" class="text-xs font-semibold text-on-surface">Kontak Darurat BK (Telepon / WhatsApp)</label>
<input id="cfg-emergency" name="emergency_contact" type="text" maxlength="64" placeholder="Contoh: 081234567890" value="{{ old('emergency_contact', $schoolConfig['emergency_contact']) }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-sm">
<span class="text-[11px] text-on-surface-variant">Ditautkan ke WhatsApp/Telepon BK pada halaman Lapor, Sukses Kirim, dan Lacak Aduan.</span>
</div>
@php($daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])
<div class="md:col-span-2 p-4 rounded-xl bg-surface-container-low/60 border border-surface-container flex flex-col gap-3">
<div class="flex flex-wrap items-center justify-between gap-2">
<span class="text-xs font-bold text-on-surface">Keterangan Jam Baca Laporan BK</span>
<span class="text-[11px] font-semibold text-secondary bg-secondary-fixed/60 px-2.5 py-0.5 rounded-full" x-text="'Tampil: ' + formattedReadingHours"></span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface">Rentang Hari Kerja Layanan BK</label>
<div class="grid grid-cols-11 items-center gap-2">
<select id="cfg-day-start" name="reading_day_start" x-model="readingDayStart" class="col-span-5 px-3 py-2.5 bg-white rounded-xl border border-outline-variant/40 text-sm">
@foreach($daysOfWeek as $day)
<option value="{{ $day }}">{{ $day }}</option>
@endforeach
</select>
<span class="col-span-1 text-center text-xs font-semibold text-outline">s.d.</span>
<select id="cfg-day-end" name="reading_day_end" x-model="readingDayEnd" class="col-span-5 px-3 py-2.5 bg-white rounded-xl border border-outline-variant/40 text-sm">
@foreach($daysOfWeek as $day)
<option value="{{ $day }}">{{ $day }}</option>
@endforeach
</select>
</div>
<span class="text-[11px] text-on-surface-variant">Pilih rentang hari aktif layanan BK di sekolah.</span>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-on-surface">Rentang Jam Kerja Sekolah (WIB)</label>
<div class="grid grid-cols-11 items-center gap-2">
<input id="cfg-time-start" name="reading_time_start" type="time" x-model="readingTimeStart" required class="col-span-5 px-3 py-2.5 bg-white rounded-xl border border-outline-variant/40 text-sm">
<span class="col-span-1 text-center text-xs font-semibold text-outline">s.d.</span>
<input id="cfg-time-end" name="reading_time_end" type="time" x-model="readingTimeEnd" required class="col-span-5 px-3 py-2.5 bg-white rounded-xl border border-outline-variant/40 text-sm">
</div>
<span class="text-[11px] text-on-surface-variant">Jam operasional selama hari sekolah (Waktu Indonesia Barat).</span>
</div>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label for="cfg-website" class="text-xs font-semibold text-on-surface">Tautan Website Resmi Sekolah</label>
<input id="cfg-website" name="school_website_url" type="url" maxlength="255" placeholder="https://smptelkompwt.sch.id" value="{{ old('school_website_url', $schoolConfig['school_website_url']) }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-sm">
<span class="text-[11px] text-on-surface-variant">Kosongkan bila tidak ingin menampilkan tautan di menu navigasi pelapor.</span>
</div>
<div class="flex flex-col gap-1.5">
<label for="cfg-story-max" class="text-xs font-semibold text-on-surface">Batas Karakter Kronologi</label>
<input id="cfg-story-max" name="story_max_length" type="number" min="200" max="10000" value="{{ old('story_max_length', $schoolConfig['story_max_length']) }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-sm">
</div>
<div class="flex flex-col gap-1.5">
<label for="cfg-retention-date" class="text-xs font-semibold text-on-surface">Tanggal Akhir Tahun Ajaran / Retensi Arsip</label>
<input id="cfg-retention-date" name="retention_date" type="date" value="{{ old('retention_date', $schoolConfig['retention_date']) }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-sm">
<span class="text-[11px] text-on-surface-variant">Pada tanggal ini, laporan arsip/selesai diringkas menjadi statistik dan dibersihkan otomatis; laporan terbuka tetap aman.</span>
</div>
<div class="flex flex-col gap-2">
<label for="cfg-logo" class="text-xs font-semibold text-on-surface">Logo Sekolah (PNG/JPG/WEBP maks 2MB)</label>
<input id="cfg-logo" x-ref="logoInput" name="school_logo" type="file" accept="image/png,image/jpeg,image/webp" @change="onLogoSelected($event)" class="sr-only">
<div class="flex flex-wrap items-center gap-2">
<button type="button" @click="$refs.logoInput.click()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold border border-outline-variant/40 transition-colors cursor-pointer">
<span class="material-symbols-outlined text-[17px] text-secondary">add_photo_alternate</span>
<span>Pilih Gambar</span>
</button>
<button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-secondary hover:bg-secondary/90 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
<span class="material-symbols-outlined text-[17px]">upload</span>
<span>Unggah Logo</span>
</button>
</div>
<div class="flex items-center gap-2.5 mt-0.5" x-show="logoPreviewUrl || logoFileName">
<template x-if="logoPreviewUrl">
<img :src="logoPreviewUrl" alt="Logo sekolah" class="w-9 h-9 rounded-lg object-contain border border-surface-container bg-white p-0.5 shrink-0">
</template>
<span class="text-[11px] font-medium truncate" :class="logoFileName ? 'text-secondary' : 'text-emerald-700'" x-text="logoFileName ? ('Siap diunggah: ' + logoFileName) : 'Logo aktif terpasang'"></span>
</div>
</div>
</div>
<div class="px-6 py-4 bg-surface-container-low/40 border-t border-surface-container flex justify-end">
<button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-on-primary text-xs font-semibold hover:bg-inverse-surface transition-all cursor-pointer flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">save</span>
<span>Simpan Konfigurasi Sekolah</span>
</button>
</div>
</form>

<form method="POST" action="{{ route('bk.manage.retention.run') }}" class="lg:col-span-4 bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 p-6 flex flex-col gap-4">
@csrf
<div class="flex items-center gap-2.5 text-secondary">
<span class="material-symbols-outlined text-[22px]">auto_delete</span>
<h3 class="font-title-md text-title-md font-bold text-on-surface">Retensi Akhir Tahun Ajaran</h3>
</div>
<p class="text-xs text-on-surface-variant leading-relaxed">
Meringkas laporan yang sudah diarsipkan atau berstatus selesai menjadi statistik bulanan lalu membersihkan detailnya apabila tanggal retensi telah tercapai. Laporan yang masih terbuka/belum selesai tidak akan terhapus.
</p>
<div class="flex flex-col gap-1.5">
<label for="retention-test-date" class="text-xs font-semibold text-on-surface">Tanggal Uji / Simulasi Retensi (Opsional)</label>
<input id="retention-test-date" name="test_date" type="date" value="{{ now('Asia/Jakarta')->toDateString() }}" class="w-full px-3.5 py-2.5 bg-surface-container-low rounded-xl border border-outline-variant/40 text-xs">
<span class="text-[11px] text-outline">Kosongkan untuk memakai tanggal hari ini ({{ now('Asia/Jakarta')->translatedFormat('d M Y') }}).</span>
</div>
<button type="submit" onclick="return confirm('Jalankan retensi arsip terhadap laporan yang sudah diarsipkan/selesai? Laporan terbuka tidak akan terhapus.')" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-all cursor-pointer flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-[18px]">play_circle</span>
<span>Jalankan / Simulasikan Retensi</span>
</button>
</form>
</div>
</section>

{{-- =========================================================================
     MODAL: TAMBAH / UBAH KATEGORI ADUAN
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-kategori'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto transition-opacity duration-200" id="modal-kategori" aria-labelledby="modal-kategori-title">
<div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
<div class="flex gap-3 items-center">
<div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]" id="modal-kategori-icon" x-text="editing ? 'edit' : 'add_circle'">add_circle</span>
</div>
<div class="flex flex-col">
<h3 x-text="editing ? 'Ubah Jenis Kejadian' : 'Tambah Jenis Kejadian'" class="font-headline-sm text-headline-sm font-bold text-on-surface" id="modal-kategori-title">Tambah Jenis Kejadian</h3>
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
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md font-semibold text-on-surface" for="modal-input-permendikbud">
  Pemetaan Bentuk Kekerasan (Permendikbudristek 46/2023)
</label>
<select x-model="categoryPermendikbud" id="modal-input-permendikbud" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-xs font-medium border border-outline-variant/40 focus:outline-none focus:ring-2 focus:ring-secondary">
@foreach($permendikbudTypes as $ptype)
<option value="{{ $ptype }}">{{ $ptype }}</option>
@endforeach
</select>
</div>
<div class="p-3.5 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-start gap-2.5">
<span class="material-symbols-outlined text-secondary text-[19px] shrink-0 mt-0.5">info</span>
<p class="text-xs text-on-surface-variant leading-relaxed">
  Semua kategori yang dibuat Tim BK langsung tampil bagi siswa saat melapor dan otomatis masuk ke akun Tim Kesiswaan, kecuali kategori <strong>Masalah Pribadi &amp; Teman Cerita</strong> yang bersifat khusus konselor BK.
</p>
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
     MODAL: HAPUS KATEGORI ADUAN (DISAMAKAN DENGAN HAPUS AKSES STAF)
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-hapus-kategori'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-hapus-kategori">
<div class="bg-surface-container-lowest rounded-2xl max-w-md w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="p-6 flex flex-col items-center text-center gap-4">
<div class="w-14 h-14 rounded-2xl bg-error-container text-error flex items-center justify-center shrink-0 shadow-xs">
<span class="material-symbols-outlined text-[32px]">warning</span>
</div>
<div class="flex flex-col gap-1.5">
<h3 class="font-display text-base font-bold text-on-surface">Hapus Kategori Aduan?</h3>
<p class="text-sm text-on-surface-variant leading-relaxed">Kategori <strong x-text="deleteTargetLabel" class="text-on-surface font-semibold">Kategori</strong> akan dihapus dari daftar pilihan pelaporan siswa. Tindakan ini tidak dapat dibatalkan.</p>
</div>
<div class="w-full p-3 bg-error-container/30 border border-error/20 rounded-xl text-left flex items-start gap-2.5">
<span class="material-symbols-outlined text-error text-[18px] shrink-0 mt-0.5">shield</span>
<span class="text-xs text-error leading-snug">Riwayat laporan siswa yang sebelumnya dikirim dengan kategori ini tetap tersimpan aman di portal BK dan arsip sekolah.</span>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-red-700 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="confirmDeleteCategory()">
<span class="material-symbols-outlined text-[19px]">delete_forever</span>
<span x-text="saving ? 'Menghapus...' : 'Ya, Hapus Kategori'">Ya, Hapus Kategori</span>
</button>
</div>
</div>
</div>
</template>

{{-- =========================================================================
     MODAL: HAPUS TITIK LOKASI (DISAMAKAN DENGAN HAPUS AKSES STAF)
     ========================================================================= --}}
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-hapus-lokasi'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-hapus-lokasi">
<div class="bg-surface-container-lowest rounded-2xl max-w-md w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="p-6 flex flex-col items-center text-center gap-4">
<div class="w-14 h-14 rounded-2xl bg-error-container text-error flex items-center justify-center shrink-0 shadow-xs">
<span class="material-symbols-outlined text-[32px]">warning</span>
</div>
<div class="flex flex-col gap-1.5">
<h3 class="font-display text-base font-bold text-on-surface">Hapus Titik Lokasi?</h3>
<p class="text-sm text-on-surface-variant leading-relaxed">Titik lokasi <strong x-text="deleteTargetLabel" class="text-on-surface font-semibold">Lokasi</strong> akan dihapus dari daftar pilihan lokasi kejadian siswa. Tindakan ini tidak dapat dibatalkan.</p>
</div>
<div class="w-full p-3 bg-error-container/30 border border-error/20 rounded-xl text-left flex items-start gap-2.5">
<span class="material-symbols-outlined text-error text-[18px] shrink-0 mt-0.5">shield</span>
<span class="text-xs text-error leading-snug">Data lokasi pada laporan siswa yang sudah masuk sebelumnya tetap tersimpan aman di sistem.</span>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button :disabled="saving" class="px-5 py-2.5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-red-700 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="confirmDeleteLocation()">
<span class="material-symbols-outlined text-[19px]">delete_forever</span>
<span x-text="saving ? 'Menghapus...' : 'Ya, Hapus Lokasi'">Ya, Hapus Lokasi</span>
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
<input x-model="staffName" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="edit-staf-nama" placeholder="Nama lengkap beserta gelar" type="text">
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
<label :class="{ 'bk-choice-active': staffRole === 'bk' }" class="flex items-start gap-3 p-3.5 rounded-xl border hover:bg-surface-container-low/40 cursor-pointer transition-all border-surface-container">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" name="edit-role-choice" type="radio" value="bk">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Guru BK / Konselor</span>
<span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">Akses Penuh Konseling</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Menangani seluruh laporan, membalas pelapor, dan meneruskan ringkasan.</span>
</div>
</label>
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
<optgroup label="Kelas 7">
<option value="7A">Kelas 7A</option>
<option value="7B">Kelas 7B</option>
<option value="7C">Kelas 7C</option>
<option value="7D">Kelas 7D</option>
<option value="7E">Kelas 7E</option>
<option value="7F">Kelas 7F</option>
<option value="7G">Kelas 7G</option>
<option value="7H">Kelas 7H</option>
<option value="7I">Kelas 7I</option>
<option value="7J">Kelas 7J</option>
</optgroup>
<optgroup label="Kelas 8">
<option value="8A">Kelas 8A</option>
<option value="8B">Kelas 8B</option>
<option value="8C">Kelas 8C</option>
<option value="8D">Kelas 8D</option>
<option value="8E">Kelas 8E</option>
<option value="8F">Kelas 8F</option>
<option value="8G">Kelas 8G</option>
<option value="8H">Kelas 8H</option>
<option value="8I">Kelas 8I</option>
<option value="8J">Kelas 8J</option>
</optgroup>
<optgroup label="Kelas 9">
<option value="9A">Kelas 9A</option>
<option value="9B">Kelas 9B</option>
<option value="9C">Kelas 9C</option>
<option value="9D">Kelas 9D</option>
<option value="9E">Kelas 9E</option>
<option value="9F">Kelas 9F</option>
<option value="9G">Kelas 9G</option>
<option value="9H">Kelas 9H</option>
<option value="9I">Kelas 9I</option>
<option value="9J">Kelas 9J</option>
</optgroup>
<optgroup label="Kelas Lanjutan">
<option value="10A">Kelas 10A</option>
<option value="11A">Kelas 11A</option>
<option value="12A">Kelas 12A</option>
</optgroup>
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
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-undang-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" id="modal-undang-staf" aria-label="Undang staf unit sekolah">
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
<input x-model="staffName" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary border border-outline-variant/40 transition-all placeholder:text-outline" id="invite-nama-input" placeholder="Nama lengkap beserta gelar" type="text">
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
<label :class="{ 'bk-choice-active': staffRole === 'bk' }" class="flex items-start gap-3 p-3.5 rounded-xl border border-surface-container hover:bg-surface-container-low/40 cursor-pointer transition-all">
<input x-model="staffRole" class="mt-1 text-secondary focus:ring-secondary" name="invite-role-choice" type="radio" value="bk">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-on-surface">Guru BK / Konselor</span>
<span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-semibold">Konselor Inti</span>
</div>
<span class="text-xs text-on-surface-variant mt-0.5">Menangani seluruh aduan siswa secara tertutup &amp; meneruskan ringkasan.</span>
</div>
</label>
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
<optgroup label="Kelas 7">
<option value="7A">Kelas 7A</option>
<option value="7B">Kelas 7B</option>
<option value="7C">Kelas 7C</option>
<option value="7D">Kelas 7D</option>
<option value="7E">Kelas 7E</option>
<option value="7F">Kelas 7F</option>
<option value="7G">Kelas 7G</option>
<option value="7H">Kelas 7H</option>
<option value="7I">Kelas 7I</option>
<option value="7J">Kelas 7J</option>
</optgroup>
<optgroup label="Kelas 8">
<option value="8A">Kelas 8A</option>
<option value="8B">Kelas 8B</option>
<option value="8C">Kelas 8C</option>
<option value="8D">Kelas 8D</option>
<option value="8E">Kelas 8E</option>
<option value="8F">Kelas 8F</option>
<option value="8G">Kelas 8G</option>
<option value="8H">Kelas 8H</option>
<option value="8I">Kelas 8I</option>
<option value="8J">Kelas 8J</option>
</optgroup>
<optgroup label="Kelas 9">
<option value="9A">Kelas 9A</option>
<option value="9B">Kelas 9B</option>
<option value="9C">Kelas 9C</option>
<option value="9D">Kelas 9D</option>
<option value="9E">Kelas 9E</option>
<option value="9F">Kelas 9F</option>
<option value="9G">Kelas 9G</option>
<option value="9H">Kelas 9H</option>
<option value="9I">Kelas 9I</option>
<option value="9J">Kelas 9J</option>
</optgroup>
<optgroup label="Kelas Lanjutan">
<option value="10A">Kelas 10A</option>
<option value="11A">Kelas 11A</option>
<option value="12A">Kelas 12A</option>
</optgroup>
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

</div>
</main>
</x-layouts.bk>
