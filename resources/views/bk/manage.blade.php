<x-layouts.bk title="Kelola Sekolah" page="manage" mode="">
<main x-data="bkManage" @keydown.escape.window="closeModal()" class="w-full  bg-surface flex-1 px-8 py-6">
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
<span class="font-title-md text-title-md text-on-surface font-semibold">SMA Negeri 1 Teladan</span>
</div>
</div>
</div>
<div role="tablist" aria-label="Kelola sekolah" class="flex items-center gap-2 p-1.5 bg-surface-container-low rounded-xl w-fit max-w-full overflow-x-auto mt-2">
<button @click="tab = 'jenis'" :aria-selected="tab === 'jenis'" role="tab" :class="{ 'bk-tab-active': tab === 'jenis' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-semibold transition-all text-on-surface-variant" id="tab-btn-jenis" type="button">
<span class="material-symbols-outlined text-[19px]">category</span>
<span>Jenis Kejadian</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary font-label-sm text-label-sm" id="badge-total-jenis">7</span>
</button>
<button @click="tab = 'lokasi'" :aria-selected="tab === 'lokasi'" role="tab" :class="{ 'bk-tab-active': tab === 'lokasi' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all" id="tab-btn-lokasi" type="button">
<span class="material-symbols-outlined text-[19px]">pin_drop</span>
<span>Lokasi Kejadian</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-outline font-label-sm text-label-sm">7</span>
</button>
<button @click="tab = 'qr'" :aria-selected="tab === 'qr'" role="tab" :class="{ 'bk-tab-active': tab === 'qr' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all" id="tab-btn-qr" type="button">
<span class="material-symbols-outlined text-[19px]">qr_code_2</span>
<span>QR Sekolah</span>
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>
</button>
<button @click="tab = 'staf'" :aria-selected="tab === 'staf'" role="tab" :class="{ 'bk-tab-active': tab === 'staf' }" class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-title-md text-title-md font-medium text-on-surface-variant hover:text-on-surface transition-all cursor-pointer" id="tab-btn-staf" type="button">
<span class="material-symbols-outlined text-[19px]">person_add</span>
<span>Undang Staf</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary font-label-sm text-label-sm" id="badge-total-staf">2</span>
</button>
</div>
</div>
<section x-cloak x-show="tab === 'jenis'" x-transition.opacity class="flex flex-col gap-6 flex" id="panel-jenis">
<div class="flex flex-col gap-6 w-full">
<div class="w-full flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50">
<div class="flex items-center gap-3">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-semibold text-on-surface">Daftar Pilihan Aduan Siswa</span>
</div>
</div>
<button   class="bg-primary text-on-primary px-4 py-2 rounded-xl text-label-md font-label-md font-semibold flex items-center gap-2 hover:bg-inverse-surface cursor-pointer shadow-xs transition-all" type="button" @click="openCategory()">
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
<th class="py-3.5 px-6 w-36">Status</th>
<th class="py-3.5 px-6 w-28 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0" id="kategori-table-body">
<template x-for="(item, index) in categories" :key="item.id">
<tr :draggable="!item.permanent" @dragstart="startDrag($event, 'categories', item.id)" @dragover.prevent="dragOver('categories', item.id)" @drop.prevent="dropRow('categories', item.id)" @dragend="endDrag()" :class="{ 'bk-row-dragging': dragging?.id === item.id, 'bk-row-drop': dropTarget === item.id }" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6 text-center">
<div class="inline-flex items-center gap-1 cursor-grab bg-surface-container px-2.5 py-1 rounded-lg">
<span class="font-label-md text-label-md font-bold text-on-surface" x-text="index + 1">
</span>
<span x-show="item.permanent" class="material-symbols-outlined text-[16px]" title="Kategori tetap">lock</span>
<div x-show="!item.permanent" class="flex flex-col">
<button   class="text-outline hover:text-on-surface leading-none  " title="Naikkan urutan" type="button" @click="moveRow('categories', item.id, -1)" :disabled="item.permanent || index === 0" aria-label="Pindahkan ke atas">
<span class="material-symbols-outlined text-[14px]">expand_less</span>
</button>
<button   class="text-outline hover:text-secondary leading-none" title="Turunkan urutan" type="button" @click="moveRow('categories', item.id, 1)" :disabled="item.permanent || index >= categories.filter(row => !row.permanent).length - 1" aria-label="Pindahkan ke bawah">
<span class="material-symbols-outlined text-[14px]">expand_more</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-6">
<span  class="font-title-md text-title-md text-on-surface font-semibold block row-label-text" x-text="item.label">
</span>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold row-status-badge">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>
<span x-text="item.active ? 'Aktif' : 'Nonaktif'">
</span>
</span>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="openCategory(item)">
<span class="material-symbols-outlined text-[16px]">edit</span>
<span>Ubah</span>
</button>
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors" type="button" :disabled="item.permanent" @click="unavailable('Penghapusan kategori')">
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
</div>
</section>
<section x-cloak x-show="tab === 'lokasi'" x-transition.opacity class="flex flex-col gap-6 " id="panel-lokasi">
<div class="w-full flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col w-full">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-semibold text-on-surface">Daftar Titik Lokasi Kejadian</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">7 Lokasi Terdaftar</span>
</div>
<button   class="px-3.5 py-2 rounded-xl bg-secondary-fixed text-on-secondary-fixed-variant hover:bg-secondary-fixed-dim transition-all font-title-md text-title-md font-semibold flex items-center gap-1.5" type="button" @click="openLocation()">
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
<span x-show="item.permanent" class="material-symbols-outlined text-[16px]" title="Kategori tetap">lock</span>
<div x-show="!item.permanent" class="flex flex-col">
<button   class="text-outline hover:text-secondary leading-none" type="button" @click="moveRow('locations', item.id, -1)" :disabled="item.permanent || index === 0" aria-label="Pindahkan ke atas">
<span class="material-symbols-outlined text-[14px]">expand_less</span>
</button>
<button   class="text-outline hover:text-secondary leading-none" type="button" @click="moveRow('locations', item.id, 1)" :disabled="item.permanent || index >= locations.filter(row => !row.permanent).length - 1" aria-label="Pindahkan ke bawah">
<span class="material-symbols-outlined text-[14px]">expand_more</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-6">
<span  class="font-title-md text-title-md font-semibold text-on-surface block" x-text="item.label">
</span>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>
<span x-text="item.active ? 'Aktif' : 'Nonaktif'">
</span>
</span>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="openLocation(item)">
<span class="material-symbols-outlined text-[16px]">edit</span>
<span>Ubah</span>
</button>
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error font-label-md text-label-md font-semibold transition-colors" type="button" :disabled="item.permanent" @click="unavailable('Penghapusan lokasi')">
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
<section x-cloak x-show="tab === 'qr'" x-transition.opacity class="flex flex-col gap-6 " id="panel-qr">
<div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden flex flex-col xl:flex-row">
<div class="p-8 flex flex-col items-center text-center xl:w-5/12 xl:border-r border-surface-container/60 bg-surface-container-lowest shrink-0 justify-between">
<div class="w-full flex flex-col items-center">
<div class="w-full h-1.5 rounded-full bg-secondary mb-6">
</div>
<div class="flex items-center gap-3 mb-5 self-start text-left">
<div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[28px]">school</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-outline uppercase tracking-wider font-semibold">Tautan Aduan Resmi</span>
<span class="font-title-lg text-title-lg font-bold text-on-surface">SMA Negeri 1 Teladan</span>
</div>
</div>
<div class="bk-hover-card p-5 bg-surface-container-lowest rounded-2xl border border-surface-container shadow-xs my-2 flex flex-col items-center justify-center">
<svg role="img" aria-label="Ilustrasi QR dari desain, belum dapat digunakan untuk pelaporan" class="w-52 h-52 text-slate-900" fill="currentColor" viewBox="0 0 240 240">
<rect fill="currentColor" height="60" rx="8" width="60" x="15" y="15">
</rect>
<rect fill="white" height="44" rx="4" width="44" x="23" y="23">
</rect>
<rect fill="currentColor" height="28" rx="2" width="28" x="31" y="31">
</rect>
<rect fill="currentColor" height="60" rx="8" width="60" x="165" y="15">
</rect>
<rect fill="white" height="44" rx="4" width="44" x="173" y="23">
</rect>
<rect fill="currentColor" height="28" rx="2" width="28" x="181" y="31">
</rect>
<rect fill="currentColor" height="60" rx="8" width="60" x="15" y="165">
</rect>
<rect fill="white" height="44" rx="4" width="44" x="23" y="173">
</rect>
<rect fill="currentColor" height="28" rx="2" width="28" x="31" y="181">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="85" y="25">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="105" y="25">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="125" y="25">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="145" y="25">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="85" y="45">
</rect>
<rect fill="currentColor" height="10" rx="2" width="20" x="115" y="45">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="145" y="45">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="25" y="85">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="25" y="105">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="25" y="125">
</rect>
<rect fill="currentColor" height="10" rx="2" width="10" x="25" y="145">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="85" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="105" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="125" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="145" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="165" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="195" y="85">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="85" y="105">
</rect>
<rect fill="currentColor" height="12" rx="2" width="30" x="115" y="105">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="155" y="105">
</rect>
<rect fill="currentColor" height="12" rx="2" width="32" x="175" y="105">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="85" y="125">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="105" y="125">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="135" y="125">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="165" y="125">
</rect>
<rect fill="currentColor" height="12" rx="2" width="22" x="185" y="125">
</rect>
<rect fill="currentColor" height="12" rx="2" width="30" x="85" y="145">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="125" y="145">
</rect>
<rect fill="currentColor" height="12" rx="2" width="25" x="145" y="145">
</rect>
<rect fill="currentColor" height="12" rx="2" width="27" x="180" y="145">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="85" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="105" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="135" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="155" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="185" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="205" y="165">
</rect>
<rect fill="currentColor" height="12" rx="2" width="22" x="95" y="185">
</rect>
<rect fill="currentColor" height="12" rx="2" width="22" x="125" y="185">
</rect>
<rect fill="currentColor" height="12" rx="2" width="32" x="155" y="185">
</rect>
<rect fill="currentColor" height="12" rx="2" width="22" x="195" y="185">
</rect>
<rect fill="currentColor" height="12" rx="2" width="30" x="85" y="205">
</rect>
<rect fill="currentColor" height="12" rx="2" width="20" x="125" y="205">
</rect>
<rect fill="currentColor" height="12" rx="2" width="12" x="155" y="205">
</rect>
<rect fill="currentColor" height="12" rx="2" width="42" x="175" y="205">
</rect>
<circle cx="120" cy="120" fill="white" r="16">
</circle>
<circle cx="120" cy="120" fill="#006398" r="12">
</circle>
</svg>
</div>
</div>
<div class="mt-4 p-3 bg-surface-container rounded-xl w-full flex flex-col items-center justify-center">
<div class="flex items-center gap-2 mt-0.5">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface tracking-wider">KODE SEKOLAH: N1-SMATLD</span>
<button  class="text-secondary hover:text-on-surface transition-colors p-1 cursor-pointer" title="Salin kode" type="button" @click="copyText('N1-SMATLD')" aria-label="Salin kode sekolah contoh">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tautan langsung: <strong class="text-secondary font-semibold">pedulisiswa.sch.id/s/smakita</strong>
</span>
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
</div>
<div class="pt-4 border-t border-surface-container/60 flex flex-col sm:flex-row items-center gap-3">
<button  class="w-full sm:w-auto flex-1 px-5 py-3.5 bg-primary hover:bg-inverse-surface text-on-primary rounded-xl font-title-md text-title-md font-semibold transition-all shadow-xs flex items-center justify-center gap-2.5 cursor-pointer" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
<span>Unduh Poster Panduan PDF (A4)</span>
</button>
<button  class="w-full sm:w-auto flex-1 px-5 py-3.5 bg-secondary-fixed text-on-secondary-fixed-variant hover:bg-secondary-fixed-dim rounded-xl font-title-md text-title-md font-semibold transition-all flex items-center justify-center gap-2.5 cursor-pointer" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[20px]">image</span>
<span>Unduh Gambar QR (PNG)</span>
</button>
</div>
</div>
</div>
<div class="w-full flex flex-col gap-4 mt-2">
<div class="bg-surface-container-lowest rounded-2xl shadow-xs overflow-hidden flex flex-col">
<div class="px-6 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-low/40 border-b border-surface-container/60">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-secondary-fixed flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
</div>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Daftar Kode QR Titik Lokasi Aktif</h2>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-secondary font-label-sm text-label-sm font-semibold">5 Titik Terbit</span>
</div>
</div>
<div class="flex items-center gap-3 shrink-0">
<div class="relative hidden sm:block">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[16px]">search</span>
<input x-model="qrQuery" aria-label="Cari titik QR" class="w-60 h-9 pl-8 pr-3 bg-surface-container-lowest text-on-surface placeholder:text-outline text-body-sm font-body-sm rounded-xl border border-surface-container focus:outline-none focus:ring-2 focus:ring-secondary transition-all" placeholder="Cari titik lokasi / slug..." type="text">
</div>
</div>
</div>
<div class="overflow-x-auto w-full">
<table class="w-full text-left">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-surface-container/60">
<th class="py-3.5 px-6">Nama Titik Lokasi</th>
<th class="py-3.5 px-6">Tautan / Link URL</th>
<th class="py-3.5 px-6 w-36">Status QR</th>
<th class="py-3.5 px-6 w-36 text-left">Total Akses</th>
<th class="py-3.5 px-6 w-52 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y-0">
<tr x-show="$el.textContent.toLowerCase().includes(qrQuery.toLowerCase())" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[18px]">storefront</span>
</div>
<span class="font-title-md text-title-md font-semibold text-on-surface">Kantin &amp; Area Pujasera</span>
</div>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md font-mono">
<span class="material-symbols-outlined text-[14px] text-outline">link</span>
<span>qr.pedulisiswa.sch.id/loc-knt01</span>
</div>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>Aktif</span>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 text-on-surface font-semibold font-title-md text-title-md">
<span class="material-symbols-outlined text-[18px] text-secondary">qr_code_scanner</span>
<span>142</span>
<span class="font-body-sm text-body-sm font-normal text-on-surface-variant">kali</span>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Unduh QR</span>
</button>
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-label-md text-label-md font-medium transition-colors" title="Regenerate QR" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Regenerate QR</span>
</button>
</div>
</td>
</tr>
<tr x-show="$el.textContent.toLowerCase().includes(qrQuery.toLowerCase())" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[18px]">stairs</span>
</div>
<span class="font-title-md text-title-md font-semibold text-on-surface">Koridor Kelas Lantai 2</span>
</div>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md font-mono">
<span class="material-symbols-outlined text-[14px] text-outline">link</span>
<span>qr.pedulisiswa.sch.id/loc-krd02</span>
</div>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>Aktif</span>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 text-on-surface font-semibold font-title-md text-title-md">
<span class="material-symbols-outlined text-[18px] text-secondary">qr_code_scanner</span>
<span>89</span>
<span class="font-body-sm text-body-sm font-normal text-on-surface-variant">kali</span>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Unduh QR</span>
</button>
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-label-md text-label-md font-medium transition-colors" title="Regenerate QR" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Regenerate QR</span>
</button>
</div>
</td>
</tr>
<tr x-show="$el.textContent.toLowerCase().includes(qrQuery.toLowerCase())" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[18px]">wc</span>
</div>
<span class="font-title-md text-title-md font-semibold text-on-surface">Toilet &amp; Area Belakang</span>
</div>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md font-mono">
<span class="material-symbols-outlined text-[14px] text-outline">link</span>
<span>qr.pedulisiswa.sch.id/loc-tlt03</span>
</div>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>Aktif</span>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 text-on-surface font-semibold font-title-md text-title-md">
<span class="material-symbols-outlined text-[18px] text-secondary">qr_code_scanner</span>
<span>64</span>
<span class="font-body-sm text-body-sm font-normal text-on-surface-variant">kali</span>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Unduh QR</span>
</button>
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-label-md text-label-md font-medium transition-colors" title="Regenerate QR" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Regenerate QR</span>
</button>
</div>
</td>
</tr>
<tr x-show="$el.textContent.toLowerCase().includes(qrQuery.toLowerCase())" class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60">
<td class="py-4 px-6">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[18px]">sports_soccer</span>
</div>
<span class="font-title-md text-title-md font-semibold text-on-surface">Lapangan Olahraga &amp; Tribun</span>
</div>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md font-mono">
<span class="material-symbols-outlined text-[14px] text-outline">link</span>
<span>qr.pedulisiswa.sch.id/loc-lpg04</span>
</div>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>Aktif</span>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 text-on-surface font-semibold font-title-md text-title-md">
<span class="material-symbols-outlined text-[18px] text-secondary">qr_code_scanner</span>
<span>53</span>
<span class="font-body-sm text-body-sm font-normal text-on-surface-variant">kali</span>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Unduh QR</span>
</button>
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-label-md text-label-md font-medium transition-colors" title="Regenerate QR" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Regenerate QR</span>
</button>
</div>
</td>
</tr>
<tr x-show="$el.textContent.toLowerCase().includes(qrQuery.toLowerCase())" class="hover:bg-surface-container-low/40 transition-colors">
<td class="py-4 px-6">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-[18px]">local_parking</span>
</div>
<span class="font-title-md text-title-md font-semibold text-on-surface">Area Parkir &amp; Gerbang Masuk</span>
</div>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md font-mono">
<span class="material-symbols-outlined text-[14px] text-outline">link</span>
<span>qr.pedulisiswa.sch.id/loc-prk05</span>
</div>
</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 text-on-tertiary-container font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container">
</span>Aktif</span>
</td>
<td class="py-4 px-6">
<div class="inline-flex items-center gap-1.5 text-on-surface font-semibold font-title-md text-title-md">
<span class="material-symbols-outlined text-[18px] text-secondary">qr_code_scanner</span>
<span>31</span>
<span class="font-body-sm text-body-sm font-normal text-on-surface-variant">kali</span>
</div>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary font-label-md text-label-md font-semibold transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Unduh QR</span>
</button>
<button  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface font-label-md text-label-md font-medium transition-colors" title="Regenerate QR" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">refresh</span>
<span>Regenerate QR</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</section>
<section x-cloak x-show="tab === 'staf'" x-transition.opacity class="flex flex-col gap-6" id="panel-staf">
<div class="w-full flex flex-col gap-5">
<div class="bg-surface-container-lowest rounded-2xl shadow-xs overflow-hidden flex flex-col w-full border border-surface-container/60">
<div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/50 border-b border-surface-container/60">
<div class="flex items-center gap-2.5">
<span class="text-base font-bold text-on-surface">Daftar Staf &amp; Hak Akses</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-xs font-semibold" id="staf-count-pill">2 Staf Terdaftar</span>
</div>
<button   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white hover:bg-slate-800 text-sm font-semibold transition-all shadow-xs hover:shadow-sm active:scale-[0.99] cursor-pointer" type="button" @click="openInvite()">
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
<tr class="hover:bg-surface-container-low/40 transition-colors border-b border-surface-container/60" id="row-staf-1">
<td class="py-4 px-6">
<div class="flex items-center gap-3.5">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center font-bold text-on-surface-variant text-sm shrink-0 shadow-xs col-avatar">DR</div>
<div class="flex flex-col min-w-0">
<span class="text-sm font-semibold text-on-surface leading-snug col-nama">Dewi Rahmawati, M.Pd.</span>
<span class="text-xs text-on-surface-variant col-email">dewi.rahma@sekolah.sch.id</span>
</div>
</div>
</td>
<td class="py-4 px-6 col-role">
<div class="flex flex-col gap-1 items-start">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-xs font-semibold text-on-surface-variant role-badge">
<span class="material-symbols-outlined text-[15px]">school</span>
<span class="role-text">Wali Kelas 8A</span>
</span>
</div>
</td>
<td class="py-4 px-6">
<div class="flex flex-col">
<span class="text-sm font-medium text-on-surface">02 Feb 2024</span>
<span class="text-xs text-outline">Terverifikasi</span>
</div>
</td>
<td class="py-4 px-6 col-status">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold w-fit status-badge">
<span class="w-2 h-2 rounded-full bg-emerald-600">
</span>
<span class="status-text">Aktif</span>
</span>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary text-xs font-semibold transition-colors cursor-pointer" type="button" @click="editStaff($el.closest('tr'))">
<span class="material-symbols-outlined text-[16px]">tune</span>
<span>Ubah</span>
</button>
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error text-xs font-semibold transition-colors cursor-pointer" type="button" @click="deleteStaff($el.closest('tr'))">
<span class="material-symbols-outlined text-[16px]">delete</span>
<span>Hapus</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/40 transition-colors bg-surface-container-low/20 border-b border-surface-container/60" id="row-staf-2">
<td class="py-4 px-6">
<div class="flex items-center gap-3.5">
<div class="w-10 h-10 rounded-full bg-secondary-fixed/50 flex items-center justify-center font-bold text-secondary text-sm shrink-0 shadow-xs col-avatar">HK</div>
<div class="flex flex-col min-w-0">
<span class="text-sm font-semibold text-on-surface leading-snug col-nama">Hendra Kurniawan, M.Pd.</span>
<span class="text-xs text-on-surface-variant col-email">hendra.kurniawan@sekolah.sch.id</span>
</div>
</div>
</td>
<td class="py-4 px-6 col-role">
<div class="flex flex-col gap-1 items-start">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-xs font-semibold text-on-surface-variant role-badge">
<span class="material-symbols-outlined text-[15px]">school</span>
<span class="role-text">Wali Kelas 9B</span>
</span>
</div>
</td>
<td class="py-4 px-6">
<div class="flex flex-col">
<span class="text-sm font-medium text-on-surface">Hari Ini, 09:20</span>
<span class="text-xs text-outline">Email Terkirim</span>
</div>
</td>
<td class="py-4 px-6 col-status">
<span class="inline-flex items-center gap-1.5 text-secondary text-xs font-semibold px-3 py-1 rounded-full bg-secondary-fixed/50 w-fit status-badge">
<span class="w-2 h-2 rounded-full bg-secondary animate-pulse">
</span>
<span class="status-text">Menunggu Penerimaan</span>
</span>
</td>
<td class="py-4 px-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-surface-container text-secondary text-xs font-semibold transition-colors cursor-pointer" type="button" @click="editStaff($el.closest('tr'))">
<span class="material-symbols-outlined text-[16px]">tune</span>
<span>Ubah</span>
</button>
<button   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-error-container text-error text-xs font-semibold transition-colors cursor-pointer" type="button" @click="deleteStaff($el.closest('tr'))">
<span class="material-symbols-outlined text-[16px]">delete</span>
<span>Hapus</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</section>
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-kategori'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto transition-opacity duration-200 " id="modal-kategori">
<div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/40">
<div class="flex gap-3 items-center">
<div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]" id="modal-kategori-icon">add_circle</span>
</div>
<div class="flex flex-col">
<h3 x-text="editing ? 'Ubah Kategori' : 'Tambah Kategori Baru'" class="font-headline-sm text-headline-sm font-bold text-on-surface" id="modal-kategori-title">Tambah Kategori Baru</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant" id="modal-kategori-subtitle">Konfigurasikan label jenis aduan yang akan muncul di alur pelaporan siswa.</p>
</div>
</div>
<button   class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="p-6 flex flex-col gap-4">
<div class="flex flex-col gap-1.5">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md font-semibold text-on-surface" for="modal-input-label">
            Nama Jenis Kejadian (Label Tampilan Siswa) <span class="text-error">*</span>
</label>
<span x-text="categoryLabel.length + '/80'" class="font-label-sm text-label-sm text-outline" id="modal-char-count">26/80</span>
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
<input x-model="categoryActive" :disabled="categoryPermanent" checked="" class="sr-only peer" id="modal-toggle-status" type="checkbox">
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-on-tertiary-container">
</div>
</label>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button   class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container font-title-md text-title-md font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button   class="px-5 py-2.5 rounded-xl bg-primary text-on-primary font-title-md text-title-md font-semibold hover:bg-inverse-surface active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="saveModal()">
<span class="material-symbols-outlined text-[19px]">check</span>
<span id="modal-btn-save-text">Simpan Perubahan</span>
</button>
</div>
</div>
</div>
</template>
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-labelledby="location-modal-title" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-tambah-lokasi'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 " id="modal-tambah-lokasi">
<div class="bg-surface-container-lowest rounded-2xl p-6 max-w-md w-full shadow-2xl flex flex-col gap-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2 text-secondary">
<span class="material-symbols-outlined text-[22px]" x-text="editing ? 'edit_location_alt' : 'add_location'">add_location</span>
<h4 id="location-modal-title" class="font-title-lg text-title-lg font-bold text-on-surface" x-text="editing ? 'Ubah Titik Lokasi' : 'Tambah Titik Lokasi Baru'">Tambah Titik Lokasi Baru</h4>
</div>
<button   class="text-outline hover:text-on-surface" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="flex flex-col gap-3">
<div class="flex flex-col gap-1">
<label for="new-loc-name" class="font-label-md text-label-md font-semibold text-on-surface">Nama Titik Lokasi</label>
<input x-model="locationLabel" class="w-full px-3.5 py-2.5 bg-surface-container-low text-on-surface rounded-xl font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary" id="new-loc-name" required maxlength="80" placeholder="Contoh: Perpustakaan &amp; Ruang Baca" type="text">
</div>
<div class="flex flex-col gap-1">
</div>
</div>
<div class="flex items-center justify-end gap-2 pt-2">
<button   class="px-4 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container font-title-md text-title-md font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">
          Batal
        </button>
<button   class="px-4 py-2.5 rounded-xl bg-primary text-on-primary font-title-md text-title-md font-semibold hover:bg-inverse-surface transition-colors shadow-xs cursor-pointer" type="button" @click="saveModal()">
          <span x-text="editing ? 'Simpan Perubahan' : 'Tambah Lokasi'">Tambah Lokasi</span>
        </button>
</div>
</div>
</div>
</template>
</div>
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-edit-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto " id="modal-edit-staf">
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
<button   class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="p-6 flex flex-col gap-4">
<input id="edit-row-id-hidden" type="hidden" value="row-staf-2">
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
<input x-model="staffRole" checked="checked" class="mt-1 text-secondary focus:ring-secondary" id="edit-radio-walikelas" name="edit-role-choice" type="radio" value="walikelas">
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
<button   class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button   class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-slate-800 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="saveModal()">
<span class="material-symbols-outlined text-[19px]">check</span>
<span>Simpan Perubahan</span>
</button>
</div>
</div>
</div>
</template>
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-hapus-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto " id="modal-hapus-staf">
<div class="bg-surface-container-lowest rounded-2xl max-w-md w-full shadow-2xl flex flex-col border border-surface-container my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
<input id="delete-row-id-hidden" type="hidden" value="row-staf-1">
<div class="p-6 flex flex-col items-center text-center gap-4">
<div class="w-14 h-14 rounded-2xl bg-error-container text-error flex items-center justify-center shrink-0 shadow-xs">
<span class="material-symbols-outlined text-[32px]">warning</span>
</div>
<div class="flex flex-col gap-1.5">
<h3 class="font-display text-base font-bold text-on-surface">Hapus Akses Staf?</h3>
<p class="text-sm text-on-surface-variant leading-relaxed">Akses portal unit sekolah untuk <strong x-text="staffName" class="text-on-surface font-semibold" id="delete-staf-target-name">Dewi Rahmawati, M.Pd.</strong> akan dicabut secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
</div>
<div class="w-full p-3 bg-error-container/30 border border-error/20 rounded-xl text-left flex items-start gap-2.5">
<span class="material-symbols-outlined text-error text-[18px] shrink-0 mt-0.5">shield</span>
<span class="text-xs text-error leading-snug">Data laporan &amp; catatan riwayat tindak lanjut yang pernah ditangani staf ini tetap tersimpan aman di arsip sekolah.</span>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button   class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button   class="px-5 py-2.5 rounded-xl bg-error text-white text-sm font-semibold hover:bg-red-700 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="unavailable('Pencabutan akses staf')">
<span class="material-symbols-outlined text-[19px]">delete_forever</span>
<span>Ya, Hapus Akses</span>
</button>
</div>
</div>
</div>
</template>
<template x-teleport="body">
<div @keydown.escape.window="closeModal()" role="dialog" aria-modal="true" tabindex="-1" @click.self="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'modal-undang-staf'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto " id="modal-undang-staf">
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
<button   class="w-9 h-9 rounded-xl flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-colors shrink-0 -mr-1 cursor-pointer" title="Tutup modal" type="button" @click="closeModal()">
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
<input x-model="staffRole" checked="checked" class="mt-1 text-secondary focus:ring-secondary" id="invite-radio-kesiswaan" name="invite-role-choice" type="radio" value="kesiswaan">
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
<div x-cloak x-show="staffRole === 'walikelas'" x-transition.opacity class="flex flex-col gap-1.5 " id="invite-wrapper-kelas-binaan">
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
</select>
</div>
</div>
<div class="p-3.5 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-start gap-3">
<span class="material-symbols-outlined text-secondary text-[20px] shrink-0 mt-0.5">info</span>
<p class="text-xs text-on-surface-variant">Tautan aktivasi berlaku selama 7 hari. Rekan staf akan diarahkan membuat kata sandi secara mandiri setelah memverifikasi email.</p>
</div>
</div>
<div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/30 flex items-center justify-end gap-3">
<button   class="px-4 py-2.5 rounded-xl border border-outline-variant/60 text-on-surface hover:bg-surface-container text-sm font-medium transition-colors cursor-pointer" type="button" @click="closeModal()">Batal</button>
<button   class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-slate-800 active:scale-[0.99] transition-all shadow-xs flex items-center gap-2 cursor-pointer" type="button" @click="submitInvite()">
<span class="material-symbols-outlined text-[19px]">send</span>
<span>Kirim Undangan</span>
</button>
</div>
</div>
</div>
</template>
<p class="mt-4 text-xs text-outline" aria-live="polite" x-text="orderMessage">
</p>
</main>
</x-layouts.bk>

