<x-layouts.bk title="Profil & Keamanan Wali Kelas" page="profile" portal="walas" :report-count="$unreadCount" :notifications="$notifications">
<main x-data="bkProfile" class="bg-background min-h-screen"><div class="flex flex-col w-full">

<div class="p-4 lg:p-margin-lg max-w-7xl w-full mx-auto flex flex-col gap-space-xl">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
<div class="flex flex-col gap-space-xs">
<div class="flex items-center gap-space-sm text-secondary font-label-sm uppercase tracking-wider">
<span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
<span class="">Portal Wali Kelas • Pengaturan Akun &amp; Keamanan</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
          Profil &amp; Keamanan Akun
        </h1>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
          Kelola informasi kredensial dinas, proteksi keamanan 2FA institusi, dan sesi aktif wali kelas.
        </p>
</div>

<div class="flex flex-wrap items-center gap-space-sm self-start md:self-auto">
<div class="inline-flex items-center gap-space-xs px-space-md py-1.5 rounded-full bg-surface-container-high text-on-tertiary-container shadow-xs">
<span class="material-symbols-outlined text-[16px] text-on-tertiary-container" style="font-variation-settings: 'FILL' 1;">verified</span>
<span class="font-label-md text-label-md font-bold">AKUN DINAS AKTIF</span>
</div>
<div class="inline-flex items-center gap-space-xs px-space-md py-1.5 rounded-full bg-surface-container-low text-secondary font-label-md text-label-md shadow-xs">
<span class="material-symbols-outlined text-[16px]">domain_verification</span>
<span class="">Unit Terisolasi: <strong>Kelas {{ $className ?? '7B' }}</strong></span>
</div>
</div>
</div>

@if (session('status'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
        <span class="font-medium">{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start gap-3">
        <span class="material-symbols-outlined text-red-600 text-xl shrink-0 mt-0.5">error</span>
        <div class="flex flex-col gap-1">
            <span class="font-bold">Terjadi kendala saat menyimpan:</span>
            <ul class="list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">

<div class="lg:col-span-5 flex flex-col gap-space-lg">
<div class="bg-surface-container-lowest rounded-xl shadow-xs p-space-xl flex flex-col gap-space-lg">

<div class="flex items-center gap-space-md pb-space-md bg-surface-container-low/50 p-space-md rounded-xl">
<div class="relative">
<img class="w-16 h-16 rounded-full object-cover shadow-xs ring-2 ring-surface-container-lowest" src="{{ asset('images/walas/ratna.jpg') }}" alt="Ibu Ratna Dewi, S.Pd.">
<span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-on-tertiary-container ring-2 ring-surface-container-lowest flex items-center justify-center text-on-tertiary">
<span class="material-symbols-outlined text-[10px]">check</span>
</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-title-lg text-title-lg text-on-surface truncate">{{ Auth::user()?->name ?? 'Ibu Ratna Dewi, S.Pd.' }}</span>
<span class="font-label-sm text-label-sm text-secondary font-semibold uppercase tracking-wider">Wali Kelas {{ $className ?? '8B' }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">ID: WLK-8B-2024</span>
</div>
</div>

<div class="flex items-center justify-between">
<span class="font-title-md text-title-md text-on-surface font-semibold flex items-center gap-space-xs">
<span class="material-symbols-outlined text-[20px] text-secondary">badge</span>
              Informasi Akun &amp; Tugas
            </span>
<span class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container-low px-space-sm py-0.5 rounded-full">Dinas Aktif</span>
</div>

<form method="POST" action="{{ route('walas.profile.update') }}" class="flex flex-col gap-space-md">
@csrf

<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface" for="fullName">Nama Lengkap &amp; Gelar</label>
<div class="relative flex items-center">
<input class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md px-space-md py-2.5 rounded-lg shadow-xs focus:outline-hidden focus:ring-2 focus:ring-secondary/40 transition" id="fullName" name="name" required maxlength="120" type="text" value="{{ Auth::user()?->name ?? 'Ibu Ratna Dewi, S.Pd.' }}">
<span class="material-symbols-outlined text-outline text-[18px] absolute right-3 pointer-events-none">edit</span>
</div>
</div>

<div class="flex flex-col gap-space-xs">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface">Email Resmi Institusi</label>
<span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-0.5">
<span class="material-symbols-outlined text-[13px]">lock</span> Terkunci
                </span>
</div>
<div class="flex items-center bg-surface-container-low px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md shadow-xs">
<span class="material-symbols-outlined text-[18px] text-outline mr-space-xs">mail</span>
<span class="truncate font-medium text-on-surface">{{ Auth::user()?->email ?? 'ratna.dewi@sman1teladan.sch.id' }}</span>
</div>

</div>

<div class="flex flex-col gap-space-xs pt-space-xs">
<label class="font-label-md text-label-md text-on-surface">Penugasan Peran Saat Ini</label>
<div class="bg-surface-container-low p-space-md rounded-lg flex items-center justify-between shadow-xs">
<div class="flex items-center gap-space-sm">
<div class="w-8 h-8 rounded-lg bg-secondary-container/30 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-[20px]">school</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md text-on-surface">Wali Kelas</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Pendampingan Siswa Kelas {{ $className ?? '7B' }}</span>
</div>
</div>
<span class="font-label-sm text-label-sm bg-surface-container-highest px-space-sm py-1 rounded-full text-on-secondary-container font-semibold">Aktif</span>
</div>
</div>

<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface">Unit Kerja Sekolah</label>
<div class="bg-surface-container-low p-space-md rounded-lg flex items-center justify-between shadow-xs">
<div class="flex items-center gap-space-sm">
<div class="w-8 h-8 rounded-lg bg-surface-variant flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">apartment</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md text-on-surface">{{ Auth::user()?->school_name ?? ($schoolName ?? 'SMP Telkom Purwokerto') }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Sistem Terpadu Antiperundungan HiOwy!</span>
</div>
</div>
<span class="material-symbols-outlined text-[18px] text-outline">verified</span>
</div>
</div>

<div class="pt-space-sm">
<button class="w-full bg-primary-container text-on-primary hover:bg-on-primary-fixed transition-colors font-label-lg text-label-lg py-3 px-space-lg rounded-lg shadow-xs flex items-center justify-center gap-space-sm cursor-pointer" type="submit">
<span class="material-symbols-outlined text-[18px]">check_circle</span>
<span class="">Simpan Perubahan Nama</span>
</button>
</div>
</form>
</div>
</div>

<div class="lg:col-span-7 flex flex-col gap-space-lg">

<div class="bg-surface-container-lowest rounded-xl shadow-xs p-space-xl flex flex-col gap-space-lg">
<div class="flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-[22px]">lock_reset</span>
</div>
<div class="flex flex-col">
<h2 class="font-title-lg text-title-lg text-on-surface">Ganti Kata Sandi</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui kata sandi secara berkala untuk integritas akun.</p>
</div>
</div>
<span class="font-label-sm text-label-sm text-secondary bg-surface-container-low px-space-sm py-1 rounded-full flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">shield</span> Terlindungi
            </span>
</div>
<form method="POST" action="{{ route('walas.profile.password') }}" class="flex flex-col gap-space-md">
@csrf

<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface" for="oldPassword">Kata Sandi Saat Ini</label>
<div class="relative flex items-center">
<input class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md px-space-md py-2.5 pr-10 rounded-lg shadow-xs focus:outline-hidden focus:ring-2 focus:ring-secondary/40 transition placeholder:text-outline" id="oldPassword" name="current_password" required autocomplete="current-password" placeholder="Masukkan kata sandi lama" type="password">
<button aria-label="Tampilkan sandi" class="absolute right-3 text-outline hover:text-on-surface transition-colors p-1 cursor-pointer" @click="togglePassword($el)" :aria-pressed="$el.parentElement.querySelector('input').type === 'text'" type="button">
<span class="material-symbols-outlined text-[20px]" id="toggleOldIcon">visibility_off</span>
</button>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface" for="newPassword">Kata Sandi Baru</label>
<div class="relative flex items-center">
<input class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md px-space-md py-2.5 pr-10 rounded-lg shadow-xs focus:outline-hidden focus:ring-2 focus:ring-secondary/40 transition placeholder:text-outline" id="newPassword" name="password" @input="document.getElementById('confirmPassword').setCustomValidity('')" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" type="password">
<button aria-label="Tampilkan sandi" class="absolute right-3 text-outline hover:text-on-surface transition-colors p-1 cursor-pointer" @click="togglePassword($el)" :aria-pressed="$el.parentElement.querySelector('input').type === 'text'" type="button">
<span class="material-symbols-outlined text-[20px]" id="toggleNewIcon">visibility_off</span>
</button>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface" for="confirmPassword">Konfirmasi Kata Sandi Baru</label>
<div class="relative flex items-center">
<input class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md px-space-md py-2.5 pr-10 rounded-lg shadow-xs focus:outline-hidden focus:ring-2 focus:ring-secondary/40 transition placeholder:text-outline" id="confirmPassword" name="password_confirmation" required autocomplete="new-password" @input="$el.setCustomValidity('')" placeholder="Ulangi kata sandi baru" type="password">
<button aria-label="Tampilkan sandi" class="absolute right-3 text-outline hover:text-on-surface transition-colors p-1 cursor-pointer" @click="togglePassword($el)" :aria-pressed="$el.parentElement.querySelector('input').type === 'text'" type="button">
<span class="material-symbols-outlined text-[20px]" id="toggleConfirmIcon">visibility_off</span>
</button>
</div>
</div>
</div>

<div class="bg-surface-container-low/70 p-space-sm rounded-lg flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-secondary">check_circle</span>
                Kombinasi minimal 8 karakter aman
              </span>

</div>

<div class="flex justify-end pt-space-xs">
<button class="bg-primary-container text-on-primary hover:bg-on-primary-fixed transition-colors font-label-lg text-label-lg py-2.5 px-space-lg rounded-lg shadow-xs flex items-center gap-space-sm cursor-pointer" type="submit">
<span class="material-symbols-outlined text-[18px]">key</span>
<span class="">Perbarui Kata Sandi</span>
</button>
</div>
</form>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-xs p-space-xl flex flex-col gap-space-lg">

<div class="flex flex-col gap-space-md pb-space-lg bg-surface-container-low/30 -mx-space-xl -mt-space-xl p-space-xl rounded-t-xl">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
<div class="flex items-center gap-space-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-[22px]">security</span>
</div>
<div class="flex flex-col">
<h3 id="security" class="font-title-lg text-title-lg text-on-surface">Autentikasi Dua Langkah (2FA)</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Proteksi autentikator dinas untuk otorisasi akses kelas.</p>
</div>
</div>
<div class="inline-flex items-center gap-1.5 px-space-sm py-1 rounded-full bg-surface-container-high text-on-tertiary-container font-label-sm text-label-sm font-bold shadow-xs">
<span class="material-symbols-outlined text-[14px]">lock</span>
<span class="">Contoh: 2FA Wajib Institusi</span>
</div>
</div>

<div class="bg-surface-container-lowest p-space-md rounded-lg flex items-start gap-space-sm shadow-xs">
<span class="material-symbols-outlined text-[20px] text-secondary mt-0.5">policy</span>
<div class="flex flex-col gap-0.5">
<span class="font-title-md text-title-md text-on-surface">Kebijakan Keamanan Sekolah Teladan</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">
                  2FA berstatus wajib dan tidak dapat dinonaktifkan demi perlindungan privasi data kerahasiaan siswa kelas 8B sesuai regulasi dinas.
                </p>
</div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center justify-between bg-surface-container-lowest p-space-md rounded-lg shadow-xs gap-space-md">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-secondary text-[22px]">pin</span>
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface">Kode Cadangan Pemulihan</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Kode aktif belum tersedia pada pratinjau</span>
</div>
</div>
<button @click="unavailable('Pengelolaan kode pemulihan')" class="inline-flex items-center justify-center gap-space-xs px-space-md py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-variant transition-colors font-label-md text-label-md shadow-xs" type="button">
<span class="material-symbols-outlined text-[16px]">visibility</span>
<span class="">Lihat / Buat Ulang Kode</span>
</button>
</div>
</div>

<div class="flex flex-col gap-space-md">
<div class="flex items-center justify-between">
<div class="flex items-center gap-space-xs font-title-md text-title-md text-on-surface">
<span class="material-symbols-outlined text-[20px] text-secondary">devices</span>
<span class="">Sesi &amp; Kendali Masuk</span>
</div>
<span class="font-label-sm text-label-sm text-on-tertiary-container font-semibold flex items-center gap-1">
<span class="w-2 h-2 rounded-full bg-on-tertiary-container"></span> Contoh Sesi Perangkat
              </span>
</div>
<div class="bg-surface-container-low p-space-md rounded-lg flex flex-col md:flex-row md:items-center justify-between gap-space-md shadow-xs">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-lg bg-surface-container-lowest flex items-center justify-center text-on-surface shadow-xs">
<span class="material-symbols-outlined text-[22px]">laptop_mac</span>
</div>
<div class="flex flex-col">
<span class="font-title-md text-title-md text-on-surface">Chrome di macOS (Komputer Ruang Guru)</span>
<div class="flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm">
<span class="">IP 10.14.22.15 (Jaringan Sekolah)</span>
<span class="">•</span>
<span class="">Data contoh desain</span>
</div>
</div>
</div>

<button @click="openModal('walas-signout')" class="inline-flex items-center justify-center gap-space-xs px-space-md py-2 rounded-lg bg-error-container text-on-error-container hover:bg-error hover:text-on-error transition-colors font-label-md text-label-md shadow-xs" type="button">
<span class="material-symbols-outlined text-[18px]">logout</span>
<span class="">Keluar dari Semua Perangkat</span>
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</div><x-kesiswaan.confirmation id="walas-signout" title="Keluar dari semua perangkat?" action="Pencabutan seluruh sesi">Konfirmasi untuk mengakhiri sesi akun pada semua perangkat setelah layanan terintegrasi.</x-kesiswaan.confirmation></main>
</x-layouts.bk>
