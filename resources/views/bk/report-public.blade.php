<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($trainingMode) ? '[MODE LATIHAN] ' : '' }}Formulir Pengaduan Siswa — {{ $schoolName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..24,400..600,0..1,-50..200" />
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col justify-between font-sans antialiased" x-data="publicReporter()">

    <!-- Top Navigation Header -->
    <header class="w-full bg-surface-container-lowest border-b border-surface-container/80 py-3.5 px-4 sm:px-6 sticky top-0 z-30 shadow-xs backdrop-blur-md bg-white/95">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center font-bold text-lg shadow-xs shrink-0 overflow-hidden">
                    @if(!empty($schoolConfig['school_logo_url']))
                        <img src="{{ $schoolConfig['school_logo_url'] }}" alt="{{ $schoolName }}" class="w-full h-full object-contain p-1">
                    @else
                        <span class="material-symbols-outlined text-[24px]">school</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="font-bold text-on-surface text-sm sm:text-base leading-tight truncate">{{ $schoolName }}</h1>
                        @if(!empty($trainingMode))
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-extrabold uppercase tracking-wide shrink-0">Mode Latihan</span>
                        @endif
                    </div>
                    <p class="text-[11px] sm:text-xs text-on-surface-variant truncate">Ruang Konsultasi Aman &amp; Pendampingan Siswa BK</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                @if(!empty($schoolConfig['school_website_url']))
                <a href="{{ $schoolConfig['school_website_url'] }}" target="_blank" rel="noopener noreferrer" class="hidden md:inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-surface-container hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-slate-600">language</span>
                    <span>Website Sekolah</span>
                </a>
                @endif
                <a href="{{ route('public.learn.default') }}" class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-surface-container hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-blue-600">auto_stories</span>
                    <span>Halaman Pelajari</span>
                </a>
                <button type="button" @click="privacyModalOpen = true" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer" title="Baca info privasi Owy">
                    <span class="material-symbols-outlined text-[15px]">shield_lock</span>
                    <span class="hidden sm:inline">Privasi Owy</span>
                </button>
                <button type="button" @click="quickExit()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer" title="Tutup cepat ke situs netral">
                    <span class="material-symbols-outlined text-[15px]">logout</span>
                    <span>Keluar Cepat</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Pop-up Pemberitahuan Privasi Owy (FR-02) -->
    <div x-cloak x-show="privacyModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true" aria-labelledby="owy-privacy-title">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="bg-primary-container text-on-primary px-6 py-5 flex items-center gap-4">
                <div class="w-16 h-16 shrink-0 flex items-center justify-center">
                    <x-auth.mascot pose="1" class="w-full h-full object-contain drop-shadow-lg" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-tertiary-fixed">Hai! Aku Owy</span>
                        @if(!empty($trainingMode))
                        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-amber-950 text-[10px] font-bold">MODE LATIHAN</span>
                        @endif
                    </div>
                    <h2 id="owy-privacy-title" class="text-base sm:text-lg font-bold leading-snug mt-0.5">Pemberitahuan Privasi Sebelum Melapor</h2>
                </div>
            </div>
            <div class="p-6 space-y-3.5 text-xs text-slate-700 leading-relaxed">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <strong class="text-emerald-900 block mb-1">Data yang Disimpan Sistem (Terenkripsi):</strong>
                    <span>Jenis kejadian, lokasi, peran pelapor, tanggal &amp; bagian waktu (pagi/siang/sore/malam), isi ceritamu, serta nama/kelas/kontak <em>hanya jika kamu memilih ingin dihubungi Guru BK</em>.</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900 block mb-1">Data yang TIDAK Disimpan:</strong>
                    <span>Sistem tidak menyimpan alamat IP, jenis perangkat, maupun jam &amp; menit pengiriman laporanmu.</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-950">
                    <strong class="block mb-1">Perhatian Penting tentang Isi Cerita:</strong>
                    <span>Meskipun sistem mendukung pelaporan anonim, <strong>isi ceritamu sendiri dapat mengungkap identitasmu</strong> apabila menyebutkan nama atau detail kejadian spesifik. HiOwy melindungi datamu sesuai kode etik konselor, bukan janji tanpa risiko sama sekali.</span>
                </div>
                <div class="pt-1 flex items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500">Jam baca BK: {{ $schoolConfig['reading_hours'] }}</span>
                    <button type="button" @click="dismissPrivacyModal()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs cursor-pointer shrink-0">
                        Saya Mengerti, Lanjut
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-2xl w-full mx-auto p-4 sm:p-6 my-4 sm:my-6 flex-1 flex flex-col gap-5">
        @if(!empty($trainingMode))
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-lg text-amber-700 shrink-0">science</span>
            <div>
                <strong class="font-bold">[MODE LATIHAN / SIMULASI AKTIF]</strong>
                <span>Laporan simulasi yang kamu kirim melalui halaman ini tidak akan masuk ke daftar aduan asli sekolah maupun mengirim peringatan darurat ke email Guru BK.</span>
            </div>
        </div>
        @endif

        <!-- Navigation Tabs: Buat Aduan vs Lacak Tiket -->
        <div class="flex items-center justify-center p-1 bg-surface-container-low rounded-2xl max-w-sm mx-auto w-full shadow-2xs border border-surface-container">
            <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-white text-on-surface font-bold shadow-xs' : 'text-on-surface-variant hover:text-on-surface font-medium'" class="flex-1 py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                <span>Buat Aduan</span>
            </button>
            <button type="button" @click="activeTab = 'track'" :class="activeTab === 'track' ? 'bg-white text-on-surface font-bold shadow-xs' : 'text-on-surface-variant hover:text-on-surface font-medium'" class="flex-1 py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">search_check</span>
                <span>Lacak Aduan Saya</span>
            </button>
        </div>

        <!-- Jam Baca & Kontak Darurat BK Strip (FR-03) -->
        <div class="rounded-2xl bg-blue-50/70 border border-blue-200/80 px-4 py-3 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-start sm:items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-blue-700 shrink-0">schedule</span>
                <span><strong>Jam Baca BK:</strong> {{ $schoolConfig['reading_hours'] }}</span>
            </div>
            @if(!empty($schoolConfig['emergency_contact']))
            <div class="flex items-center gap-2 shrink-0">
                <span class="material-symbols-outlined text-[17px] text-rose-600">emergency_share</span>
                <span>Kontak Darurat BK:</span>
                <a href="{{ $schoolConfig['emergency_wa_url'] ?: $schoolConfig['emergency_tel_url'] }}" target="_blank" rel="noopener noreferrer" class="font-bold text-rose-700 underline hover:text-rose-900">{{ $schoolConfig['emergency_contact'] }}</a>
            </div>
            @endif
        </div>

        <!-- Ringkasan Privasi Owy Inline Banner (Collapsible FR-02) -->
        <div class="rounded-2xl bg-white border border-slate-200/90 px-4 py-3 text-xs text-slate-600 shadow-2xs" x-data="{ openPrivacy: false }">
            <button type="button" @click="openPrivacy = !openPrivacy" class="w-full flex items-center justify-between gap-2 text-left font-semibold text-slate-800 cursor-pointer">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-blue-600">verified_user</span>
                    <span>Catatan Owy: Apa yang Disimpan, Tidak Disimpan &amp; Batas Anonimitas</span>
                </span>
                <span class="material-symbols-outlined text-[18px] text-slate-400" x-text="openPrivacy ? 'expand_less' : 'expand_more'">expand_more</span>
            </button>
            <div x-cloak x-show="openPrivacy" x-transition class="mt-2.5 pt-2.5 border-t border-slate-100 space-y-1.5 text-[11px] leading-relaxed">
                <p><strong>Disimpan:</strong> Jenis kejadian, lokasi, peran, tanggal &amp; bagian waktu (pagi/siang/sore/malam), kronologi terenkripsi, serta nama/kelas/kontak jika memilih dihubungi.</p>
                <p><strong>Tidak Disimpan:</strong> Alamat IP, tipe perangkat, serta jam-menit pengiriman laporan.</p>
                <p class="text-amber-800"><strong>Perhatian:</strong> Cerita kejadian bisa mengungkap pelapor jika memuat detail/nama tertentu; sistem ini menjaga kerahasiaan sesuai standar BK tanpa menjanjikan anonimitas mutlak.</p>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: FORMULIR PENGADUAN SISWA -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'form'" x-transition.opacity>

            <!-- Success Card (When submitted - FR-20: Tampil 1 kali + peringatan simpan) -->
            <div x-cloak x-show="submitted" class="bg-surface-container-lowest rounded-2xl border border-surface-container p-6 sm:p-8 text-center flex flex-col items-center gap-4 shadow-sm animate-in fade-in zoom-in-95 duration-200">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <div class="flex flex-col gap-1">
                    @if(!empty($trainingMode))
                    <span class="mx-auto px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-bold uppercase mb-1">[Mode Latihan — Simulasi]</span>
                    @endif
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Aduanmu Telah Diterima Guru BK</h2>
                    <p class="text-xs sm:text-sm text-on-surface-variant max-w-md mx-auto leading-relaxed mt-1">
                        Terima kasih sudah berani bersuara. Tim Konselor {{ $schoolName }} mendengarkan ceritamu secara tertutup dan terlindungi.
                    </p>
                </div>

                <!-- Ticket Box -->
                <div class="w-full max-w-md p-4 bg-surface-container-low rounded-2xl border border-surface-container flex flex-col gap-3 my-2 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-surface-container/60">
                        <span class="text-xs font-semibold text-on-surface-variant">Kode Laporan (Tampil Satu Kali)</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-bold text-base text-blue-700" x-text="ticketCode"></span>
                            <button type="button" @click="copyTicketCode()" class="p-1 rounded-lg hover:bg-surface-container text-slate-500 hover:text-slate-900 transition-colors cursor-pointer" title="Salin kode laporan">
                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-relaxed">
                        <strong>Penting (FR-20):</strong> Salin atau catat kode di atas sekarang juga dan <strong>jangan bagikan kepada siapa pun</strong>. Demi keamanan, kode hanya ditampilkan sekali pada layar ini dan tidak dapat dimunculkan ulang bila halaman ditutup.
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block">Kategori:</span>
                            <span class="font-semibold text-slate-800 truncate block" x-text="category || 'Umum'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Lokasi:</span>
                            <span class="font-semibold text-slate-800 truncate block" x-text="submittedLocationLabel || 'Area Sekolah'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Jam Baca BK:</span>
                            <span class="font-semibold text-slate-800 block">{{ $schoolConfig['reading_hours'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Kerahasiaan:</span>
                            <span class="font-semibold text-slate-800" x-text="isAnonymous ? 'Anonim (Terproteksi)' : 'Ingin Dihubungi BK'"></span>
                        </div>
                    </div>
                    @if(!empty($schoolConfig['emergency_contact']))
                    <div class="pt-2 border-t border-surface-container/60 flex items-center justify-between gap-2 text-xs">
                        <span class="text-slate-600 font-medium">Butuh respons darurat langsung?</span>
                        <a href="{{ $schoolConfig['emergency_wa_url'] ?: $schoolConfig['emergency_tel_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition-colors">
                            <span class="material-symbols-outlined text-[14px]">call</span>
                            <span>Hubungi BK ({{ $schoolConfig['emergency_contact'] }})</span>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-md mt-2">
                    <button type="button" @click="goToTrack(ticketCode)" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Pantau Status Tiket</span>
                    </button>
                    <button type="button" @click="resetForm()" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs sm:text-sm border border-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">replay</span>
                        <span>Kirim Aduan Lain</span>
                    </button>
                </div>
            </div>

            <!-- Form Card (Before submitted) -->
            <div x-show="!submitted" class="bg-surface-container-lowest rounded-2xl border border-surface-container/80 shadow-sm p-6 sm:p-8 flex flex-col gap-6">

                <!-- Header Info -->
                <div>
                    <div class="flex items-center gap-2 text-secondary font-semibold text-xs uppercase tracking-wider mb-1.5">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                        <span>{{ !empty($trainingMode) ? '[Mode Latihan] Simulasi Pelaporan Siswa' : 'Ruang Dialog Bebas Tekanan & Rasa Takut' }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">Kirim Aduan atau Ceritamu</h2>
                    <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                        Ceritakan apa yang kamu alami atau saksikan. Formulir ini dipantau langsung oleh Guru BK secara tertutup tanpa sorotan teman sekelas.
                    </p>
                </div>

                <!-- Feedback Error Banner -->
                <div x-cloak x-show="errorMessage" class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-lg shrink-0">error</span>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitReport()" class="flex flex-col gap-5">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label for="website-field">Website</label>
                        <input id="website-field" type="text" name="website" x-model="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- 1. Jenis Kejadian -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Jenis Kejadian / Masalah <span class="text-red-500">*</span></span>
                            <span class="text-[11px] text-slate-400 font-normal normal-case">Pilih yang paling sesuai</span>
                        </label>
                        <select x-model="category" required class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none transition-all">
                            <option value="">-- Pilih Jenis Kejadian --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->label }}">{{ $cat->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Titik Lokasi Kejadian + Opsi Lainnya Ketik Sendiri (FR-10) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Titik Lokasi Kejadian <span class="text-red-500">*</span></span>
                            <span class="text-[11px] text-slate-400 font-normal normal-case">Di mana kejadian berlangsung?</span>
                        </label>
                        <select x-model="location" required class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none transition-all">
                            <option value="">-- Pilih Lokasi Kejadian --</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->label }}">{{ $loc->label }}</option>
                            @endforeach
                            <option value="__other__">Lainnya (ketik sendiri tempat kejadian)</option>
                        </select>
                        <div x-cloak x-show="location === '__other__'" x-transition class="flex flex-col gap-1">
                            <label class="text-[11px] font-bold text-slate-700">Sebutkan Tempat Kejadian Lainnya <span class="text-red-500">*</span></label>
                            <input type="text" x-model="customLocation" :required="location === '__other__'" maxlength="120" placeholder="Contoh: Perpustakaan lantai 2, area parkir, luar gerbang..." class="w-full px-4 py-2.5 bg-white text-on-surface rounded-xl text-xs sm:text-sm border border-blue-300 focus:ring-2 focus:ring-secondary focus:outline-none">
                        </div>
                    </div>

                    <!-- 3. Tingkat Urgensi Bantuan -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Kebutuhan Respons Bantuan</span>
                            <span class="text-[11px] text-slate-400 font-normal normal-case">Pilih kondisi yang kamu hadapi</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border transition-all cursor-pointer" :class="urgency === 'Darurat' ? 'bg-red-50/70 border-red-300 ring-1 ring-red-400 text-red-900' : 'bg-surface-container-low/50 border-surface-container hover:bg-surface-container-low text-slate-700'">
                                <input type="radio" x-model="urgency" value="Darurat" class="text-red-600 focus:ring-red-500">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span> Darurat
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight">Ada ancaman fisik / bahaya</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border transition-all cursor-pointer" :class="urgency === 'Sedang' ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-400 text-amber-900' : 'bg-surface-container-low/50 border-surface-container hover:bg-surface-container-low text-slate-700'">
                                <input type="radio" x-model="urgency" value="Sedang" class="text-amber-600 focus:ring-amber-500">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-600"></span> Penting
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight">Tekanan berat berulang</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border transition-all cursor-pointer" :class="urgency === 'Rendah' ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400 text-emerald-900' : 'bg-surface-container-low/50 border-surface-container hover:bg-surface-container-low text-slate-700'">
                                <input type="radio" x-model="urgency" value="Rendah" class="text-emerald-600 focus:ring-emerald-500">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Konseling
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight">Butuh teman bercerita</span>
                                </div>
                            </label>
                        </div>
                        <div x-cloak x-show="urgency === 'Darurat'" x-transition class="mt-1 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
                            <label class="flex items-start gap-2.5 text-xs cursor-pointer">
                                <input type="checkbox" x-model="dangerConfirmed" class="mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-rose-300">
                                <span>
                                    <strong class="font-bold">Konfirmasi Darurat:</strong> Saya menyatakan kejadian ini melibatkan ancaman fisik atau bahaya mendesak yang perlu notifikasi segera ke Guru BK.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- 4. Peran Pelapor (FR-07) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Melapor Sebagai</label>
                        <select x-model="reporterRole" class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                            <option value="Siswa (Korban Langsung)">Korban (Mengalami Langsung)</option>
                            <option value="Saksi Kejadian">Saksi (Melihat / Mengetahui Kejadian)</option>
                            <option value="Teman Korban">Teman Korban</option>
                            <option value="Orang Tua / Wali Siswa">Orang Tua / Wali Siswa</option>
                        </select>
                    </div>

                    <!-- 4b. Tanggal Kejadian (Kalender + Tidak ingat pasti) & Waktu Kejadian (FR-09) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Tanggal Kejadian</label>
                            <input type="date" x-model="incidentDate" :disabled="incidentDateUnknown" :required="!incidentDateUnknown" max="{{ now('Asia/Jakarta')->toDateString() }}" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none disabled:opacity-50">
                            <label class="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer mt-0.5">
                                <input type="checkbox" x-model="incidentDateUnknown" @change="if (incidentDateUnknown) incidentDate = ''" class="rounded text-blue-600 focus:ring-blue-500">
                                <span>Saya tidak ingat tanggal pastinya</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Waktu Kejadian</label>
                            <select x-model="incidentTime" class="w-full px-4 py-2.5 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                                <option value="Pagi (Jam Pelajaran Sekolah)">Pagi (Jam Pelajaran Sekolah)</option>
                                <option value="Siang / Jam Istirahat">Siang / Jam Istirahat</option>
                                <option value="Sore / Jam Pulang Sekolah">Sore / Jam Pulang Sekolah</option>
                                <option value="Malam (Daring / Luar Sekolah)">Malam (Daring / Luar Sekolah)</option>
                                <option value="Tidak ingat pasti">Tidak ingat pasti</option>
                            </select>
                            <span class="text-[11px] text-slate-400">Jam &amp; menit rinci sengaja tidak dikumpulkan demi privasi (FR-14).</span>
                        </div>
                    </div>

                    <!-- 5. Pihak Terlibat (Opsional - FR-11) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Pihak yang Terlibat / Saksi <span class="text-[11px] font-normal text-slate-400 normal-case">(Boleh dikosongkan)</span></span>
                        </label>
                        <input type="text" x-model="involvedParties" placeholder="Misal: Kakak kelas, teman sekelas, atau kosongkan bila ragu" class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                    </div>

                    <!-- 6. Cerita Kejadian (FR-12) -->
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Ceritakan Apa yang Terjadi <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400" x-text="story.length + ' / {{ $storyMaxLength }} karakter'"></span>
                        </div>
                        <p class="text-[11px] text-slate-500">Pertanyaan pemandu: Apa yang terjadi? Sejak kapan berlangsung? Dan apa dampak atau dukungan yang kamu harapkan dari Guru BK?</p>
                        <textarea x-model="story" required maxlength="{{ $storyMaxLength }}" rows="5" placeholder="Tuliskan ceritamu di sini dengan tenang. Jika memilih melapor anonim, hindari menulis nama atau ciri yang dapat langsung mengarah ke dirimu..." class="w-full p-4 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none leading-relaxed transition-all"></textarea>
                    </div>

                    <!-- 7. Pengaturan Anonimitas atau Ingin Dihubungi Guru BK (FR-13) -->
                    <div class="p-4 bg-surface-container-low rounded-xl border border-surface-container flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex flex-col">
                                <span class="font-semibold text-xs sm:text-sm text-on-surface flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[17px] text-blue-600" style="font-variation-settings: 'FILL' 1;">security</span>
                                    <span>Kirim Sebagai Anonim (Matikan jika ingin dihubungi Guru BK)</span>
                                </span>
                                <span class="text-[11px] text-on-surface-variant mt-0.5">
                                    Saat mode Anonim aktif, nama, kelas, dan kontak tidak disimpan. Jika dimatikan, identitasmu terenkripsi khusus untuk Guru BK.
                                </span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="isAnonymous" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Revealed fields if NOT anonymous -->
                        <div x-cloak x-show="!isAnonymous" x-transition class="pt-3 border-t border-slate-200/80 space-y-3">
                            <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 text-[11px] text-blue-900">
                                <strong>Ketentuan Ingin Dihubungi Guru BK:</strong> <strong>Nama</strong> dan <strong>Kelas wajib diisi</strong> agar Guru BK mengetahui siswa yang perlu didampingi. <strong>Nomor WhatsApp / Kontak bersifat opsional</strong> (boleh dikosongkan).
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="flex flex-col gap-1">
                                    <label class="text-[11px] font-bold text-slate-700">Nama Lengkap / Panggilan <span class="text-red-600">* (Wajib)</span></label>
                                    <input type="text" x-model="reporterName" :required="!isAnonymous" placeholder="Nama lengkapmu" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[11px] font-bold text-slate-700">Kelas <span class="text-red-600">* (Wajib)</span></label>
                                    <div class="relative">
                                        <select x-model="reporterClass" :required="!isAnonymous" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none appearance-none pr-8 cursor-pointer text-slate-800">
                                            <option value="">-- Pilih Kelas (Wajib) --</option>
                                            <optgroup label="Kelas 7 (Tujuh)">
                                                @foreach(['7A','7B','7C','7D','7E','7F','7G','7H','7I','7J'] as $cls)
                                                <option value="{{ $cls }}">Kelas {{ $cls }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Kelas 8 (Delapan)">
                                                @foreach(['8A','8B','8C','8D','8E','8F','8G','8H','8I','8J'] as $cls)
                                                <option value="{{ $cls }}">Kelas {{ $cls }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Kelas 9 (Sembilan)">
                                                @foreach(['9A','9B','9C','9D','9E','9F','9G','9H','9I','9J'] as $cls)
                                                <option value="{{ $cls }}">Kelas {{ $cls }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Lainnya">
                                                <option value="Orang Tua">Orang Tua / Wali Siswa</option>
                                                <option value="Guru / Staf">Guru / Staf</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </optgroup>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[11px] font-bold text-slate-700">No. WhatsApp / Kontak <span class="font-normal text-slate-500">(Opsional)</span></label>
                                    <input type="text" x-model="reporterContact" placeholder="Boleh dikosongkan (08xxxx)" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($turnstileSiteKey))
                    <div class="pt-1">
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <button type="submit" :disabled="submitting || (urgency === 'Darurat' && !dangerConfirmed)" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl font-bold text-xs sm:text-sm transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer mt-1">
                        <template x-if="submitting">
                            <span class="inline-flex items-center gap-2">
                                <span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span>Mengirimkan secara aman...</span>
                            </span>
                        </template>
                        <template x-if="!submitting">
                            <span class="inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[19px]">send</span>
                                <span>{{ !empty($trainingMode) ? 'Kirim Simulasi (Mode Latihan)' : 'Kirimkan Aduan Sekarang' }}</span>
                            </span>
                        </template>
                    </button>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: LACAK STATUS TIKET ADUAN (FR-03 Jam Baca + Chat Centang 1/2 Opsi A) -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'track'" x-transition.opacity class="bg-surface-container-lowest rounded-2xl border border-surface-container p-6 sm:p-8 flex flex-col gap-6 shadow-sm">
            <div>
                <div class="flex items-center gap-2 text-blue-600 font-semibold text-xs uppercase tracking-wider mb-1.5">
                    <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                    <span>Transparansi &amp; Keamanan Penanganan</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">Cek Status Aduanmu</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                    Masukkan kode laporan 10 karakter yang kamu peroleh saat mengirim aduan untuk melihat status penanganan, jam dibaca oleh Guru BK, serta membalas pesan secara dua arah.
                </p>
            </div>

            <!-- Search Ticket Form -->
            <form @submit.prevent="searchTicket()" class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">tag</span>
                    <input type="text" x-model="searchCode" required maxlength="16" placeholder="Masukkan 10 karakter kode laporan (cth: 7K9M4P2WXR)" class="w-full pl-10 pr-4 py-3 bg-surface-container-low text-slate-800 rounded-xl text-xs sm:text-sm border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider">
                </div>
                <button type="submit" :disabled="tracking" class="py-3 px-6 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0 shadow-2xs">
                    <template x-if="tracking">
                        <span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    </template>
                    <template x-if="!tracking">
                        <span class="material-symbols-outlined text-lg">search</span>
                    </template>
                    <span>Lacak Tiket</span>
                </button>
            </form>

            <!-- Tracking Error Notice -->
            <div x-cloak x-show="trackError" class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2.5">
                <span class="material-symbols-outlined text-lg shrink-0">error</span>
                <span x-text="trackError"></span>
            </div>

            <!-- Tracking Result Box (hanya status, jam baca BK, kontak BK, & pesan BK <-> Pelapor) -->
            <div x-cloak x-show="ticketData" class="p-5 rounded-2xl bg-surface-container-low border border-slate-200 flex flex-col gap-4 animate-in fade-in duration-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium">Status Penanganan Laporan</span>
                            <template x-if="ticketData?.is_training">
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-bold">Mode Latihan</span>
                            </template>
                        </div>
                        <template x-if="ticketData?.bk_read_time">
                            <span class="text-[11px] font-semibold text-sky-700 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">done_all</span>
                                <span x-text="'Laporan dibaca Tim BK pada: ' + ticketData.bk_read_time"></span>
                            </span>
                        </template>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          :class="{
                              'bg-amber-100 text-amber-800 border border-amber-200': ticketData?.status === 'belum_dibaca',
                              'bg-sky-100 text-sky-800 border border-sky-200': ticketData?.status === 'dibaca',
                              'bg-blue-100 text-blue-800 border border-blue-200': ticketData?.status === 'ditindaklanjuti',
                              'bg-emerald-100 text-emerald-800 border border-emerald-200': ticketData?.status === 'selesai'
                          }">
                        <span class="w-1.5 h-1.5 rounded-full"
                              :class="{
                                  'bg-amber-600': ticketData?.status === 'belum_dibaca',
                                  'bg-sky-600': ticketData?.status === 'dibaca',
                                  'bg-blue-600 animate-pulse': ticketData?.status === 'ditindaklanjuti',
                                  'bg-emerald-600': ticketData?.status === 'selesai'
                              }"></span>
                        <span x-text="ticketData?.status_label"></span>
                    </span>
                </div>

                <!-- Kontak Darurat & Jam Layanan di Lacak Aduan -->
                <div class="p-3 rounded-xl bg-white border border-slate-200/80 flex flex-wrap items-center justify-between gap-2 text-[11px]">
                    <span class="text-slate-600"><strong>Jam Baca BK:</strong> <span x-text="ticketData?.reading_hours"></span></span>
                    <template x-if="ticketData?.emergency_contact">
                        <a :href="ticketData.emergency_wa_url || ticketData.emergency_tel_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 font-bold hover:bg-rose-100">
                            <span class="material-symbols-outlined text-[14px]">call</span>
                            <span x-text="'Kontak Darurat BK: ' + ticketData.emergency_contact"></span>
                        </a>
                    </template>
                </div>

                <div class="flex flex-col gap-2.5">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wide flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-blue-600">forum</span>
                        <span>Pesan Guru BK &amp; Pelapor</span>
                    </span>

                    <template x-if="!ticketData?.messages || ticketData.messages.length === 0">
                        <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 text-xs text-slate-500">
                            Belum ada pesan dari Guru BK.
                        </div>
                    </template>

                    <template x-for="(msg, idx) in (ticketData?.messages || [])" :key="idx">
                        <div class="p-3.5 rounded-xl border flex flex-col gap-1.5"
                             :class="msg.sender === 'bk' ? 'bg-white border-blue-200/80' : 'bg-blue-50/70 border-blue-200'">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold" :class="msg.sender === 'bk' ? 'text-blue-700' : 'text-slate-800'" x-text="msg.sender_label"></span>
                                <span class="text-slate-400" x-text="msg.date"></span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line" x-text="msg.body"></p>
                            <div class="flex items-center justify-end gap-1.5 text-[10px] text-slate-500">
                                <template x-if="msg.sender === 'bk' && msg.time">
                                    <span x-text="msg.time"></span>
                                </template>
                                <template x-if="msg.sender === 'reporter' && msg.is_read && msg.read_time">
                                    <span x-text="'Dibaca BK ' + msg.read_time"></span>
                                </template>
                                <span class="material-symbols-outlined text-[14px]"
                                      :class="msg.is_read ? 'text-blue-600' : 'text-slate-400'"
                                      :title="msg.is_read ? 'Sudah dibaca' : 'Terkirim, belum dibaca'"
                                      x-text="msg.is_read ? 'done_all' : 'done'">done</span>
                            </div>
                        </div>
                    </template>

                    <form x-show="ticketData && !ticketData.archived" @submit.prevent="sendReporterReply()" class="pt-2 border-t border-slate-200/80 flex flex-col gap-2">
                        <label class="text-[11px] font-semibold text-slate-700">Kirim Balasan / Info Tambahan ke Guru BK</label>
                        <textarea x-model="replyBody" rows="2" maxlength="2000" required placeholder="Tulis pesan balasan untuk Guru BK..." class="w-full p-2.5 bg-white text-slate-800 rounded-xl text-xs border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        <div class="flex items-center justify-between gap-2">
                            <span x-show="replyError" x-text="replyError" class="text-[11px] text-red-600"></span>
                            <span x-show="replySuccess" x-text="replySuccess" class="text-[11px] text-emerald-700 font-medium"></span>
                            <button type="submit" :disabled="replying || !replyBody.trim()" class="ml-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-semibold transition-colors cursor-pointer">
                                <span x-show="!replying">Kirim Balasan</span>
                                <span x-show="replying">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 px-6 text-center text-xs text-on-surface-variant border-t border-surface-container bg-surface-container-lowest">
        &copy; {{ date('Y') }} {{ $schoolName }} • Portal Pengaduan Siswa Terproteksi HiOwy!
    </footer>

    <!-- Alpine Logic for Reporter & Tracking (No form data saved in browser storage - FR-67) -->
    <script>
        window.addEventListener('pageshow', (event) => {
            const nav = performance.getEntriesByType('navigation')[0];
            try {
                if ((event.persisted || nav?.type === 'back_forward') && sessionStorage.getItem('hiowy_exited') === '1') {
                    window.location.replace('https://www.google.com');
                } else {
                    sessionStorage.removeItem('hiowy_exited');
                }
            } catch (e) {}
        });

        function publicReporter() {
            let showPrivacy = false;
            try {
                showPrivacy = sessionStorage.getItem('hiowy_privacy_seen') !== '1';
            } catch (e) {
                showPrivacy = true;
            }

            return {
                privacyModalOpen: showPrivacy,
                activeTab: 'form',
                submitted: false,
                submitting: false,
                tracking: false,
                errorMessage: '',
                trackError: '',
                ticketCode: '',
                submittedLocationLabel: '',

                // Form fields
                category: '',
                location: '',
                customLocation: '',
                involvedParties: '',
                urgency: 'Rendah',
                dangerConfirmed: false,
                reporterRole: 'Siswa (Korban Langsung)',
                incidentDate: '',
                incidentDateUnknown: false,
                incidentTime: 'Pagi (Jam Pelajaran Sekolah)',
                story: '',
                website: '',
                isAnonymous: true,
                reporterName: '',
                reporterClass: '',
                reporterContact: '',

                // Tracking tab
                searchCode: '',
                ticketData: null,
                replyBody: '',
                replying: false,
                replyError: '',
                replySuccess: '',

                init() {
                    window.addEventListener('pagehide', () => this.wipeClientState());
                },

                dismissPrivacyModal() {
                    this.privacyModalOpen = false;
                    try {
                        sessionStorage.setItem('hiowy_privacy_seen', '1');
                    } catch (e) {}
                },

                quickExit() {
                    this.wipeClientState();
                    try {
                        sessionStorage.setItem('hiowy_exited', '1');
                        window.history.replaceState(null, '', '/');
                    } catch (e) {}
                    window.location.replace('https://www.google.com');
                },

                wipeClientState() {
                    this.story = '';
                    this.involvedParties = '';
                    this.reporterName = '';
                    this.reporterClass = '';
                    this.reporterContact = '';
                    this.replyBody = '';
                    this.customLocation = '';
                },

                resolvedLocationDisplay() {
                    if (this.location === '__other__') {
                        return 'Lainnya: ' + (this.customLocation || '-');
                    }
                    return this.location || 'Area Sekolah';
                },

                async submitReport() {
                    if (this.submitting) return;
                    if (this.urgency === 'Darurat' && !this.dangerConfirmed) {
                        this.errorMessage = 'Centang kotak konfirmasi darurat terlebih dahulu sebelum mengirim laporan Darurat.';
                        return;
                    }
                    if (!this.incidentDateUnknown && !this.incidentDate) {
                        this.errorMessage = 'Pilih tanggal kejadian atau centang "Saya tidak ingat tanggal pastinya".';
                        return;
                    }
                    if (this.location === '__other__' && !this.customLocation.trim()) {
                        this.errorMessage = 'Tuliskan tempat kejadian lainnya terlebih dahulu.';
                        return;
                    }
                    if (!this.isAnonymous && (!this.reporterName.trim() || !this.reporterClass)) {
                        this.errorMessage = 'Jika ingin dihubungi Guru BK, Nama dan Kelas wajib diisi (WhatsApp bersifat opsional).';
                        return;
                    }
                    this.submitting = true;
                    this.errorMessage = '';

                    try {
                        const formData = new FormData();
                        formData.append('category', this.category);
                        formData.append('location', this.location);
                        if (this.location === '__other__' && this.customLocation.trim()) {
                            formData.append('custom_location', this.customLocation.trim());
                        }
                        if (this.involvedParties.trim()) {
                            formData.append('involved_parties', this.involvedParties);
                        }
                        formData.append('urgency', this.urgency);
                        formData.append('reporter_role', this.reporterRole);
                        formData.append('incident_time', this.incidentTime);
                        formData.append('incident_date_unknown', this.incidentDateUnknown ? '1' : '0');
                        if (!this.incidentDateUnknown && this.incidentDate) {
                            formData.append('incident_date', this.incidentDate);
                        }
                        formData.append('story', this.story);
                        formData.append('website', this.website);
                        formData.append('is_training', @json(!empty($trainingMode)) ? '1' : '0');
                        formData.append('is_anonymous', this.isAnonymous ? '1' : '0');

                        const turnstileEl = document.querySelector('[name="cf-turnstile-response"]');
                        if (turnstileEl && turnstileEl.value) {
                            formData.append('cf-turnstile-response', turnstileEl.value);
                        }

                        if (!this.isAnonymous) {
                            formData.append('reporter_name', this.reporterName);
                            formData.append('reporter_class', this.reporterClass);
                            formData.append('reporter_contact', this.reporterContact);
                        }

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const submitEndpoint = @json(!empty($trainingMode) && request()->routeIs('public.training') ? route('public.training.store') : route('public.report.store'));

                        const response = await fetch(submitEndpoint, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.ticketCode = data.ticket_code;
                            this.submittedLocationLabel = this.resolvedLocationDisplay();
                            this.submitted = true;
                            this.wipeClientState();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            this.errorMessage = data.message || 'Terjadi kendala saat mengirimkan laporan. Silakan periksa formulir Anda.';
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        this.errorMessage = 'Gagal menghubungi server. Pastikan koneksi internet Anda aktif.';
                    } finally {
                        this.submitting = false;
                    }
                },

                copyTicketCode() {
                    navigator.clipboard.writeText(this.ticketCode).then(() => {
                        alert('Kode laporan ' + this.ticketCode + ' berhasil disalin! Simpan baik-baik dan jangan bagikan.');
                    }).catch(() => {
                        prompt('Salin kode laporan Anda:', this.ticketCode);
                    });
                },

                goToTrack(code) {
                    this.searchCode = code;
                    this.activeTab = 'track';
                    this.searchTicket();
                },

                resetForm() {
                    this.submitted = false;
                    this.category = '';
                    this.location = '';
                    this.customLocation = '';
                    this.involvedParties = '';
                    this.urgency = 'Rendah';
                    this.dangerConfirmed = false;
                    this.incidentDate = '';
                    this.incidentDateUnknown = false;
                    this.story = '';
                    this.website = '';
                    this.isAnonymous = true;
                    this.reporterName = '';
                    this.reporterClass = '';
                    this.reporterContact = '';
                },

                async searchTicket() {
                    if (!this.searchCode || this.tracking) return;
                    this.tracking = true;
                    this.trackError = '';
                    this.ticketData = null;

                    try {
                        const url = '{{ route('public.report.track') }}?ticket_code=' + encodeURIComponent(this.searchCode);
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.ticketData = data.ticket;
                        } else {
                            this.trackError = data.message || 'Nomor tiket tidak ditemukan.';
                        }
                    } catch (error) {
                        console.error('Tracking error:', error);
                        this.trackError = 'Gagal melacak tiket. Periksa koneksi jaringan Anda.';
                    } finally {
                        this.tracking = false;
                    }
                },

                async sendReporterReply() {
                    if (!this.searchCode || !this.replyBody.trim() || this.replying) return;
                    this.replying = true;
                    this.replyError = '';
                    this.replySuccess = '';

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const response = await fetch('{{ route('public.report.reply') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                ticket_code: this.searchCode,
                                body: this.replyBody,
                            }),
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.replyBody = '';
                            this.replySuccess = data.message || 'Balasan terkirim ke Guru BK.';
                            if (data.messages && this.ticketData) {
                                this.ticketData.messages = data.messages;
                            } else {
                                await this.searchTicket();
                            }
                        } else {
                            this.replyError = data.message || 'Gagal mengirim balasan.';
                        }
                    } catch (error) {
                        console.error('Reply error:', error);
                        this.replyError = 'Gagal mengirim balasan. Periksa koneksi jaringan Anda.';
                    } finally {
                        this.replying = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
