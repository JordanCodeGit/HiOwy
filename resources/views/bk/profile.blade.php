<x-layouts.bk title="Profil & Keamanan" page="profile" mode="">
<main x-data="bkProfile" class="relative bg-surface min-h-screen">
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
          Kelola informasi identitas dinas, keamanan kata sandi, serta kendali sesi aktif staf konselor.
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
<div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-on-primary font-headline-md text-headline-md font-bold tracking-tight shadow-xs select-none overflow-hidden shrink-0">
    @if($user->avatar && file_exists(public_path($user->avatar)))
        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
    @else
        {{ $initials }}
    @endif
</div>
<div class="flex flex-col min-w-0">
<div class="flex items-center gap-1.5">
<span class="font-title-md text-title-md font-bold text-on-surface truncate">{{ $user->name }}</span>
<span class="material-symbols-outlined text-secondary text-[18px]" title="Identitas Terverifikasi Operator">verified</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $user->email }}</span>
</div>
</div>

@if(session('success_profile'))
<div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 flex items-center gap-2 text-body-sm">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    <span>{{ session('success_profile') }}</span>
</div>
@endif

<form method="POST" action="{{ route('bk.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-4" id="nameUpdateForm">
@csrf
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between" for="avatar">
<span>Ganti Foto Profil</span>
<span class="font-label-sm text-label-sm text-outline">Opsional (JPG/PNG maks 4MB)</span>
</label>
<input class="w-full px-3.5 py-2 bg-surface-container-lowest text-on-surface-variant font-body-sm text-body-sm rounded-lg shadow-xs focus:outline-none border border-outline-variant/40 focus:border-secondary transition-all file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer" id="avatar" type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp">
@error('avatar')
<span class="text-xs text-red-600">{{ $message }}</span>
@enderror
</div>

<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between" for="namaLengkap">
<span>Nama Tampilan Lengkap</span>
</label>
<div class="relative">
<input class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none focus:bg-surface-container-lowest border border-outline-variant/40 focus:border-secondary transition-all" id="namaLengkap" required="" type="text" name="name" value="{{ old('name', $user->name) }}">
</div>
@error('name')
<span class="text-xs text-red-600">{{ $message }}</span>
@enderror
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
<span class="truncate">{{ $user->email }}</span>
<span class="material-symbols-outlined text-outline text-[18px] ml-2 shrink-0">encrypted</span>
</div>
<p class="font-body-sm text-body-sm text-outline">
                Untuk mengubah alamat email, silakan ajukan permohonan ke operator IT sekolah.
              </p>
</div>

<div class="grid grid-cols-2 gap-3 pt-2">
<div class="p-3 rounded-lg bg-surface-container flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-on-surface-variant">Penugasan Peran</span>
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $roleLabel }}</span>
</div>
<div class="p-3 rounded-lg bg-surface-container flex flex-col gap-1">
<span class="font-label-sm text-label-sm text-on-surface-variant">Instansi Sekolah</span>
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $user->school_name ?? 'SMA Lincoln Senior High' }}</span>
</div>
</div>

<div class="pt-2 flex justify-end">
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs cursor-pointer" id="btnSimpanNama" type="submit">
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
<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>Periode Berjalan</span>
</div>

<div class="p-3.5 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-2.5">
<span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5">info</span>
<p class="font-body-sm text-body-sm leading-relaxed">Pengaturan ini menetapkan filter data default untuk laporan konseling, arsip kasus, dan statistik aktif Anda.</p>
</div>

@if(session('success_period'))
<div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 flex items-center gap-2 text-body-sm">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    <span>{{ session('success_period') }}</span>
</div>
@endif

<form method="POST" action="{{ route('bk.profile.period') }}" class="flex flex-col gap-4" id="formTahunAjaran">
@csrf
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="selectTahunAjaran">Tahun Ajaran Aktif</label>
<div class="relative">
<select name="tahun_ajaran" class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/50 transition-all appearance-none cursor-pointer pr-10" id="selectTahunAjaran">
<option value="2024/2025" {{ session('academic_year', '2024/2025') === '2024/2025' ? 'selected' : '' }}>2024/2025 (Aktif)</option>
<option value="2025/2026" {{ session('academic_year') === '2025/2026' ? 'selected' : '' }}>2025/2026 (Mendatang)</option>
<option value="2023/2024" {{ session('academic_year') === '2023/2024' ? 'selected' : '' }}>2023/2024 (Arsip)</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-2.5 text-outline pointer-events-none text-[20px]">expand_more</span>
</div>
</div>

<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="selectSemester">Semester / Kuartal</label>
<div class="relative">
<select name="semester" class="w-full px-3.5 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/50 transition-all appearance-none cursor-pointer pr-10" id="selectSemester">
<option value="ganjil" {{ session('academic_semester', 'ganjil') === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
<option value="genap" {{ session('academic_semester') === 'genap' ? 'selected' : '' }}>Semester Genap</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-2.5 text-outline pointer-events-none text-[20px]">expand_more</span>
</div>
</div>

<div class="pt-1 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-outline">Sinkronisasi portal konselor</span>
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs cursor-pointer" id="btnSimpanTA" type="submit">
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

@if(session('success_password'))
<div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 flex items-center gap-2 text-body-sm">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    <span>{{ session('success_password') }}</span>
</div>
@endif

@if($errors->has('current_password') || $errors->has('password'))
<div class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-2.5 text-body-sm">
    <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
    <div class="flex-1">
        {{ $errors->first('current_password') ?: $errors->first('password') }}
    </div>
</div>
@endif

<form method="POST" action="{{ route('bk.profile.password') }}" class="flex flex-col gap-4" id="passwordForm">
@csrf

<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="currPass">Kata Sandi Saat Ini</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/40 focus:border-secondary transition-all" id="currPass" name="current_password" autocomplete="current-password" placeholder="Masukkan kata sandi lama Anda" required="" type="password">
<button aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" @click="togglePassword($el)">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="newPass">Kata Sandi Baru</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/40 focus:border-secondary transition-all" id="newPass" name="password" minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" required="" type="password">
<button aria-label="Tampilkan kata sandi baru" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" @click="togglePassword($el)">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>

<div class="flex flex-col gap-1.5">
<label class="font-label-md text-label-md text-on-surface font-medium" for="confPass">Konfirmasi Kata Sandi</label>
<div class="relative">
<input class="w-full pl-3.5 pr-10 py-2.5 bg-surface-container-lowest text-on-surface placeholder:text-outline font-body-md text-body-md rounded-lg shadow-xs focus:outline-none border border-outline-variant/40 focus:border-secondary transition-all" id="confPass" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru" required="" type="password">
<button aria-label="Tampilkan konfirmasi kata sandi" class="absolute right-3 top-2.5 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" @click="togglePassword($el)">
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
<button class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-inverse-surface transition-all flex items-center gap-2 shadow-xs cursor-pointer" id="btnSubmitPass" type="submit">
<span class="material-symbols-outlined text-[18px]">key</span>
<span>Perbarui Kata Sandi</span>
</button>
</div>
</form>
</div>

<!-- Kendali Sesi Aktif -->
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
<button class="px-4 py-2.5 rounded-lg bg-error-container text-on-error-container hover:bg-error hover:text-on-error transition-all font-label-md text-label-md flex items-center justify-center gap-2 shrink-0 shadow-xs cursor-pointer" id="btnLogoutAll" type="button" @click="openModal('staff-signout')">
<span class="material-symbols-outlined text-[18px]">logout</span>
<span>Keluar dari Semua Perangkat</span>
</button>
</div>
</div>

</div>
</div>
</div>

<!-- Modal Konfirmasi Keluar dari Semua Perangkat -->
<template x-teleport="body">
    <div x-cloak x-show="modal === 'staff-signout'" x-transition.opacity class="bk-modal fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <section id="staff-signout" role="dialog" aria-modal="true" aria-labelledby="staff-signout-title" tabindex="-1" @keydown.tab="trapFocus($event)" class="w-full max-w-lg max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <h2 id="staff-signout-title" class="text-lg font-bold text-slate-900">Keluar dari semua perangkat?</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Tindakan ini akan mengakhiri sesi aktif akun Anda di seluruh peramban dan perangkat lain demi perlindungan data konseling siswa. Anda akan dialihkan ke halaman masuk.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2.5 hover:bg-slate-50 cursor-pointer">Batal</button>
                <form method="POST" action="{{ route('bk.profile.logout-all') }}" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="rounded-xl bg-error text-on-error px-4 py-2.5 hover:bg-error-container hover:text-on-error-container transition-colors font-semibold cursor-pointer">Konfirmasi Keluar</button>
                </form>
            </div>
        </section>
    </div>
</template>

</div>
</main>
</x-layouts.bk>
