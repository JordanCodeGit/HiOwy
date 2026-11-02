<x-layouts.bk title="Profil & Keamanan" page="profile" mode="">
<main x-data="bkProfile" class="relative  bg-surface min-h-screen">
<div class="flex flex-col w-full">
<div class="px-8 pt-8 pb-4 max-w-7xl w-full mx-auto">
<div class="flex items-center gap-2 mb-3">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">Portal Staf Konseling</span>
<span class="text-outline-variant">•</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Preferensi Akses &amp; Kredensial</span>
</div>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">
          Profil &amp; Keamanan Akun
        </h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1.5 max-w-2xl">
          Kelola informasi identitas dinas, autentikasi berlapis (2FA), serta kendali sesi aktif staf konselor.
        </p>
</div>
<div class="flex items-center gap-3">
</div>
</div>
</div>
<div class="px-8 pb-16 max-w-7xl w-full mx-auto">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-5 flex flex-col gap-6">
<div class="bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-6 relative overflow-hidden">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-secondary text-[22px]">badge</span>
<h2 class="font-title-lg text-title-lg text-on-surface font-semibold">Data Akun Resmi</h2>
</div>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
              Aktif
            </span>
</div>
<div class="flex items-center gap-4 p-4 rounded-xl bg-surface-container-low">
<div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-on-primary font-headline-md text-headline-md font-bold tracking-tight shadow-xs select-none">
              SJ
            </div>
<div class="flex flex-col min-w-0">
<div class="flex items-center gap-1.5">
<span class="font-title-md text-title-md font-bold text-on-surface truncate">Sarah Jenkins, M.Pd.</span>
<span class="material-symbols-outlined text-secondary text-[18px]" title="Identitas Terverifikasi Operator">verified</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">NIP: 19840315 200812 2 004</span>
</div>
</div>
<form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitUnavailable($event)" class="flex flex-col gap-4" id="nameUpdateForm">
@csrf
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between" for="namaLengkap">
<span>Nama Tampilan Lengkap</span>
</label>
<div class="relative">
<input class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none focus:bg-surface-container-lowest transition-all" id="namaLengkap" required="" type="text" value="Sarah Jenkins, M.Pd.">
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between">
<span>Email</span>
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-outline">
<span class="material-symbols-outlined text-[13px]">lock</span>
                  Terkunci
                </span>
</label>
<div class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-surface-container-high text-on-surface-variant font-body-md text-body-md select-none">
<span class="truncate">sarah.jenkins@lincoln.sch.id</span>
<span class="material-symbols-outlined text-outline text-[18px] ml-2 shrink-0">encrypted</span>
</div>
<p class="font-body-sm text-body-sm text-outline">
                Untuk mengubah alamat email, silakan ajukan permohonan ke operator IT sekolah.
              </p>
</div>
<div class="grid grid-cols-2 gap-3 pt-2">
<div class="p-3 rounded-lg bg-surface-container flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-on-surface-variant">Penugasan Peran</span>
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">Guru BK</span>
</div>
<div class="p-3 rounded-lg bg-surface-container flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-on-surface-variant">Instansi Sekolah</span>
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">SMA Lincoln Senior High</span>
</div>
</div>
<div class="pt-2 flex justify-end">
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs" id="btnSimpanNama" type="submit">
<span class="material-symbols-outlined text-[18px]">check</span>
<span>Simpan Perubahan Nama</span>
</button>
</div>
</form>
</div>
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-5 border border-outline-variant/30">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-secondary text-[22px]">calendar_month</span>
<h2 class="font-title-lg text-title-lg text-on-surface font-semibold">Tahun Ajaran &amp; Periode</h2>
</div>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-secondary">
</span>Periode Berjalan</span>
</div>
<div class="p-3.5 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-2.5">
<span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5">info</span>
<p class="font-body-sm text-body-sm leading-relaxed">Pengaturan ini menetapkan filter data default untuk laporan konseling, arsip kasus, dan statistik aktif Anda.</p>
</div>
<form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitUnavailable($event)" class="flex flex-col gap-4" id="formTahunAjaran">
@csrf<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="selectTahunAjaran">Tahun Ajaran Aktif</label>
<div class="relative">
<select class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/50 transition-all appearance-none cursor-pointer pr-10" id="selectTahunAjaran">
<option selected="" value="2024/2025">2024/2025 (Aktif)</option>
<option value="2025/2026">2025/2026 (Mendatang)</option>
<option value="2023/2024">2023/2024 (Arsip)</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-2.5 text-outline pointer-events-none text-[20px]">expand_more</span>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="selectSemester">Semester / Kuartal</label>
<div class="relative">
<select class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/50 transition-all appearance-none cursor-pointer pr-10" id="selectSemester">
<option selected="" value="ganjil">Semester Ganjil</option>
<option value="genap">Semester Genap</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-2.5 text-outline pointer-events-none text-[20px]">expand_more</span>
</div>
</div>
<div class="pt-1 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-outline">Sinkronisasi portal konselor</span>
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs" id="btnSimpanTA" type="submit">
<span class="material-symbols-outlined text-[18px]">check</span>
<span>Simpan Tahun Ajaran</span>
</button>
</div>
</form>
</div>
</div>
<div class="lg:col-span-7 flex flex-col gap-6">
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-6">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-secondary text-[22px]">password</span>
<h2 class="font-title-lg text-title-lg text-on-surface font-semibold">Perbarui Kata Sandi</h2>
</div>
</div>
<div class="p-3.5 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-3">
<span class="material-symbols-outlined text-secondary text-[20px] shrink-0 mt-0.5">info</span>
<p class="font-body-sm text-body-sm leading-relaxed">
              Setelah kata sandi diperbarui, semua sesi di peramban atau perangkat lain akan otomatis diputus secara seketika demi perlindungan data konseling.
            </p>
</div>
<form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitPassword($event)" class="flex flex-col gap-4" id="passwordForm">
@csrf

<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="currPass">Kata Sandi Saat Ini</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none transition-all" id="currPass" autocomplete="current-password" placeholder="Masukkan kata sandi lama Anda" required="" type="password">
<button  aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors" type="button" @click="togglePassword($el)">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="newPass">Kata Sandi Baru</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none transition-all" id="newPass" minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" required="" type="password">
<button  aria-label="Tampilkan kata sandi baru" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors" type="button" @click="togglePassword($el)">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="confPass">Konfirmasi Kata Sandi</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none transition-all" id="confPass" autocomplete="new-password" @input="$el.setCustomValidity('')" placeholder="Ulangi kata sandi baru" required="" type="password">
<button  aria-label="Tampilkan konfirmasi kata sandi" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors" type="button" @click="togglePassword($el)">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
</div>
<div class="p-3 rounded-lg bg-surface-container text-on-surface-variant font-body-sm text-body-sm flex items-center gap-2">
<span class="material-symbols-outlined text-outline text-[18px]">verified</span>
<span>Kriteria ketat: Minimal 8 karakter kombinasi huruf kapital, kecil, angka, dan simbol khusus.</span>
</div>
<div class="pt-2 flex justify-end">
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs" id="btnSubmitPass" type="submit">
<span class="material-symbols-outlined text-[18px]">key</span>
<span>Perbarui Kata Sandi</span>
</button>
</div>
</form>
</div>
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-6">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-secondary text-[22px]">security</span>
<h2 id="security" class="font-title-lg text-title-lg text-on-surface font-semibold">Autentikasi Dua Langkah (2FA)</h2>
</div>
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm w-fit">
<span class="material-symbols-outlined text-[16px]">verified_user</span>
<span>2FA Aktif (Wajib Institusi)</span>
</div>
</div>
<div class="p-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-highest text-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">pin</span>
</div>
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">Kode Cadangan Pemulihan</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Sisa 6 dari 10 kode pemulihan tersimpan</span>
</div>
</div>
<button   class="px-4 py-2 rounded-lg bg-secondary-fixed text-on-secondary-fixed hover:bg-secondary-fixed-dim transition-colors font-label-md text-label-md flex items-center justify-center gap-2" type="button" @click="openModal('recoveryModal')">
<span class="material-symbols-outlined text-[18px]">autorenew</span>
<span>Buat Ulang Kode Pemulihan</span>
</button>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-surface-container text-on-surface-variant font-body-sm text-body-sm">
<span class="material-symbols-outlined text-outline text-[18px] shrink-0">help_center</span>
<span>Perangkat hilang atau berpindah authenticator? Hubungi operator pusat kampus untuk prosedur verifikasi pemulihan darurat.</span>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col gap-5">
<div class="flex items-center justify-between gap-4 p-4 rounded-xl bg-surface-container-low">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-highest text-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">devices</span>
</div>
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">Kendali Sesi Aktif</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Putuskan sambungan akun pada seluruh peramban dan perangkat</span>
</div>
</div>
<button  class="px-4 py-2.5 rounded-lg bg-error-container text-on-error-container hover:bg-error hover:text-on-error transition-all font-label-md text-label-md flex items-center justify-center gap-2 shrink-0 shadow-xs" id="btnLogoutAll" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[18px]">logout</span>
<span>Keluar dari Semua Perangkat</span>
</button>
</div>
</div>
</div>
</div>
</div>
<template x-teleport="body">
<div role="dialog" aria-modal="true" tabindex="-1" @keydown.tab="trapFocus($event)" @keydown.escape.window="closeModal()" x-cloak x-show="modal === 'recoveryModal'" x-transition.opacity class="bk-modal fixed inset-0 z-50  flex items-center justify-center p-4" id="recoveryModal">
<div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm">
</div>
<div class="relative bg-surface-container-lowest rounded-xl max-w-lg w-full p-6 shadow-xl flex flex-col gap-5 z-10">
<div class="flex items-start justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container text-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-[22px]">vpn_key</span>
</div>
<div class="flex flex-col">
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Buat Ulang Kode Pemulihan</h3>
<span class="font-label-sm text-label-sm text-on-surface-variant">Konfirmasi Keamanan Tingkat Dua</span>
</div>
</div>
<button   class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition-colors" @click="closeModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="flex flex-col gap-4" id="recoveryStepPassword">
<div class="p-3.5 rounded-lg bg-error-container text-on-error-container font-body-sm text-body-sm flex items-start gap-2.5">
<span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">warning</span>
<span>Peringatan: Membuat rangkaian kode pemulihan baru akan secara otomatis menghanguskan 6 kode lama Anda.</span>
</div>
<form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitUnavailable($event)" class="flex flex-col gap-4">
@csrf
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="modalVerifyPass">Masukkan Kata Sandi Akun</label>
<input class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none transition-all" id="modalVerifyPass" placeholder="Kata sandi Sarah Jenkins" required="" type="password">
</div>
<div class="flex items-center justify-end gap-3 pt-2">
<button   class="px-4 py-2.5 rounded-lg bg-surface-container text-on-surface-variant font-label-md text-label-md hover:bg-surface-container-high transition-colors" type="button" @click="closeModal()">
              Batal
            </button>
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-colors flex items-center gap-1.5 shadow-xs" type="submit">
<span>Verifikasi &amp; Buat Kode</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</div>
</form>
</div>
</div>
</div>
</template>
</div>
</main>
</x-layouts.bk>

