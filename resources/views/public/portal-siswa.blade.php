<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($trainingMode) ? '[MODE LATIHAN] ' : '' }}Formulir Pengaduan Siswa — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-owy.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..24,400..600,0..1,-50..200" />
    <style>
        @keyframes owyFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        .owy-float {
            animation: owyFloat 3.2s ease-in-out infinite;
        }
    </style>
</head>
<body class="student-portal bg-surface text-on-surface min-h-screen flex flex-col justify-between antialiased" x-data="publicReporter()">

    @include('public.portal-siswa-mobile')

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

    <!-- Pop-up Pemberitahuan Privasi Owy -->
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
                    <strong class="text-emerald-900">Data yang disimpan</strong>
                    <ul class="mt-1 list-disc pl-4">
                        <li>Jenis, tempat, tanggal, dan bagian hari kejadian.</li>
                        <li>Peranmu dan ceritamu. Isi cerita dienkripsi.</li>
                        <li>Nama, kelas, dan kontak hanya jika kamu ingin dihubungi BK.</li>
                    </ul>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-900">Data yang tidak disimpan</strong>
                    <ul class="mt-1 list-disc pl-4">
                        <li>Alamat IP dan jenis perangkatmu.</li>
                        <li>Jam dan menit saat kamu mengirim laporan.</li>
                    </ul>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-950">
                    <strong>Jika ingin tetap anonim</strong>
                    <ul class="mt-1 list-disc pl-4">
                        <li>Hindari nama dan ciri khas. Ingat, isi ceritamu sendiri dapat mengungkap identitasmu.</li>
                    </ul>
                </div>
                <div class="flex flex-wrap items-end justify-between gap-3 pt-1">
                    <div class="text-xs leading-relaxed text-slate-600">
                        <strong class="block text-[11px] tracking-wide text-slate-800">JAM AKTIF BK:</strong>
                        @php
                            $scheduleLines = preg_split('/[\s,]+(?=\d{1,2}[:.]\d{2})/u', $schoolConfig['reading_hours'], 2);
                        @endphp
                        @foreach ($scheduleLines as $line)
                            <span class="block">{{ $line }}</span>
                        @endforeach
                    </div>
                    <button type="button" @click="dismissPrivacyModal()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs cursor-pointer shrink-0">
                        Saya Mengerti, Lanjut
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="student-main">
        @if(!empty($trainingMode))
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-lg text-amber-700 shrink-0">science</span>
            <div>
                <strong class="font-bold">[MODE LATIHAN / SIMULASI AKTIF]</strong>
                <span>Laporan simulasi yang kamu kirim melalui halaman ini tidak akan masuk ke daftar aduan asli sekolah maupun mengirim peringatan darurat ke email Guru BK.</span>
            </div>
        </div>
        @endif

        <!-- Navigation Tabs: Buat Aduan vs Lacak Aduan Saya -->
        <div class="student-tabs">
            <button type="button" @click="switchTab('form')" :class="activeTab === 'form' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-on-surface-variant hover:text-on-surface font-medium'" class="flex-1 py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                <span>Buat Aduan</span>
            </button>
            <button type="button" @click="switchTab('track')" :class="activeTab === 'track' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-on-surface-variant hover:text-on-surface font-medium'" class="flex-1 py-2 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">search_check</span>
                <span>Lacak Aduan Saya</span>
            </button>
        </div>

        <section class="student-info" x-data="{ openPrivacy: false }">
            <div class="student-info-row">
                <span class="student-hours"><span class="material-symbols-outlined">schedule</span> Jam Baca BK: {{ $schoolConfig['reading_hours'] }}</span>
                <button type="button" class="student-privacy-toggle" @click="openPrivacy = !openPrivacy" :aria-expanded="openPrivacy" aria-controls="student-privacy-details">
                    <span class="material-symbols-outlined">verified_user</span> Catatan Owy: Batas Anonimitas
                    <span class="material-symbols-outlined" x-text="openPrivacy ? 'expand_less' : 'expand_more'">expand_more</span>
                </button>
            </div>
            @if (!empty($schoolConfig['emergency_contact']))
                <a class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-rose-700" href="{{ $schoolConfig['emergency_wa_url'] ?: $schoolConfig['emergency_tel_url'] }}" target="_blank" rel="noopener noreferrer">
                    <span class="material-symbols-outlined text-base">emergency_share</span> Kontak darurat BK · {{ $schoolConfig['emergency_contact'] }}
                </a>
            @endif
            <div id="student-privacy-details" x-cloak x-show="openPrivacy" x-transition class="mt-2.5 pt-2.5 border-t border-slate-100 space-y-1.5 text-[11px] leading-relaxed">
                <p><strong>Disimpan:</strong> Jenis kejadian, lokasi, peran, tanggal &amp; bagian waktu (pagi/siang/sore/malam), kronologi terenkripsi, serta nama/kelas/kontak jika memilih dihubungi.</p>
                <p><strong>Tidak Disimpan:</strong> Alamat IP, tipe perangkat, serta jam-menit pengiriman laporan.</p>
                <p class="text-amber-800"><strong>Perhatian:</strong> Cerita kejadian bisa mengungkap pelapor jika memuat detail/nama tertentu; sistem ini menjaga kerahasiaan sesuai standar BK tanpa menjanjikan anonimitas mutlak.</p>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- TAB 1: FORMULIR PENGADUAN SISWA (1 PERTANYAAN PER HALAMAN + MASKOT OWY)   -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'form'" x-transition.opacity>

            <!-- Layar Sukses Setelah Pengiriman (Wajib Salin Kode Terlebih Dahulu) -->
            <div x-cloak x-show="submitted" class="bg-surface-container-lowest rounded-3xl border border-surface-container p-6 sm:p-8 text-center flex flex-col items-center gap-4 shadow-sm animate-in fade-in zoom-in-95 duration-200">
                <div class="w-24 h-24 rounded-3xl bg-emerald-50 border border-emerald-200 flex items-center justify-center p-2 shadow-xs">
                    <img src="{{ asset('maskot_owy/maskot_5.png') }}" alt="Owy merayakan aduan berhasil terkirim" class="w-full h-full object-contain owy-float">
                </div>
                <div class="flex flex-col gap-1">
                    @if(!empty($trainingMode))
                    <span class="mx-auto px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-bold uppercase mb-1">[Mode Latihan — Simulasi]</span>
                    @endif
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Aduanmu Telah Diterima Guru BK</h2>
                    <p class="text-xs sm:text-sm text-on-surface-variant max-w-md mx-auto leading-relaxed mt-1">
                        Terima kasih sudah berani bersuara. Owy dan Tim Konselor {{ $schoolName }} siap mendengarkan dan mendampingimu secara tertutup.
                    </p>
                </div>

                <!-- Kotak Kode Tiket + Wajib Salin -->
                <div class="w-full max-w-md p-5 bg-surface-container-low rounded-2xl border border-surface-container flex flex-col gap-3.5 my-1 text-left">
                    <div class="flex flex-col gap-2.5 pb-3.5 border-b border-surface-container/80">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-on-surface-variant">Kode Laporan Kamu</span>
                            <span x-show="ticketCopied" x-cloak class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                <span>Tersalin</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3 bg-white px-4 py-3 rounded-xl border-2 transition-colors" :class="ticketCopied ? 'border-emerald-400' : 'border-blue-300'">
                            <span class="font-mono font-extrabold text-lg sm:text-xl tracking-widest text-blue-700 select-all" x-text="ticketCode"></span>
                            <button type="button" @click="copyTicketCode()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer shrink-0" :class="ticketCopied ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white animate-pulse'">
                                <span class="material-symbols-outlined text-[16px]" x-text="ticketCopied ? 'check' : 'content_copy'">content_copy</span>
                                <span x-text="ticketCopied ? 'Kode Tersalin' : 'Salin Kode'">Salin Kode</span>
                            </button>
                        </div>
                        <p class="text-[11px] leading-relaxed flex items-start gap-1.5" :class="ticketCopied ? 'text-emerald-800' : 'text-amber-800'">
                            <span class="material-symbols-outlined text-[15px] shrink-0 mt-0.5" x-text="ticketCopied ? 'verified' : 'info'">info</span>
                            <span x-text="ticketCopied ? 'Kode berhasil disalin! Simpan di tempat aman dan jangan bagikan ke siapa pun. Sekarang kamu bisa memantau status atau mengirim aduan lain.' : 'Salin kode laporanmu terlebih dahulu agar tidak hilang. Tombol Pantau Status dan Kirim Aduan Lain akan aktif setelah kode disalin.'"></span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div>
                            <span class="text-slate-400 block">Kategori:</span>
                            <span class="font-semibold text-slate-800 truncate block" x-text="submittedCategoryLabel || 'Umum'"></span>
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
                            <span class="font-semibold text-slate-800" x-text="submittedAnonymous ? 'Anonim (Terproteksi)' : 'Ingin Dihubungi BK'"></span>
                        </div>
                    </div>

                    @if(!empty($schoolConfig['emergency_contact']))
                    <div class="pt-2.5 border-t border-surface-container/60 flex items-center justify-between gap-2 text-xs">
                        <span class="text-slate-600 font-medium">Butuh respons darurat langsung?</span>
                        <a href="{{ $schoolConfig['emergency_wa_url'] ?: $schoolConfig['emergency_tel_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition-colors">
                            <span class="material-symbols-outlined text-[14px]">call</span>
                            <span>Hubungi BK ({{ $schoolConfig['emergency_contact'] }})</span>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-md mt-1">
                    <button type="button" @click="goToTrack(ticketCode)" :disabled="!ticketCopied" :title="!ticketCopied ? 'Salin kode laporan terlebih dahulu' : 'Pantau status tiket ini'" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-semibold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="ticketCopied ? 'search' : 'lock'">lock</span>
                        <span>Pantau Status Tiket</span>
                    </button>
                    <button type="button" @click="resetForm()" :disabled="!ticketCopied" :title="!ticketCopied ? 'Salin kode laporan terlebih dahulu' : 'Buat laporan baru'" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-surface-container hover:bg-surface-container-high disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed text-on-surface font-semibold text-xs sm:text-sm border border-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="ticketCopied ? 'replay' : 'lock'">lock</span>
                        <span>Kirim Aduan Lain</span>
                    </button>
                </div>
            </div>

            <!-- Wizard Pengaduan 1 Pertanyaan per Halaman dengan Owy -->
            <div x-show="!submitted" class="bg-surface-container-lowest rounded-3xl border border-surface-container/80 shadow-sm overflow-hidden flex flex-col">

                <!-- Progress Bar & Owy Companion Banner -->
                <div class="student-wizard-header">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-700">
                            <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                            <span x-text="'Langkah ' + currentStep + ' dari ' + totalSteps">Langkah 1 dari 8</span>
                        </span>
                        <div class="flex items-center gap-1">
                            <template x-for="n in totalSteps" :key="n">
                                <button type="button" @click="jumpToStep(n)" :disabled="n > maxReachedStep" :title="'Langkah ' + n" class="h-2 rounded-full transition-all duration-300" :aria-label="'Langkah ' + n" :class="n === currentStep ? 'w-6 bg-blue-600' : (n < currentStep ? 'w-2.5 bg-blue-400 hover:bg-blue-500 cursor-pointer' : (n <= maxReachedStep ? 'w-2.5 bg-blue-200 cursor-pointer' : 'w-2 bg-slate-200 cursor-default'))"></button>
                            </template>
                        </div>
                    </div>
                    <div class="w-full h-1.5 bg-blue-100 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-300" :style="'width: ' + Math.round((currentStep / totalSteps) * 100) + '%'"></div>
                    </div>

                    <div class="student-companion">
                        <img :src="currentOwy.image" :alt="currentOwy.alt" class="owy-float">
                        <span x-text="currentOwy.badge"></span>
                        <p x-text="currentOwy.speech"></p>
                    </div>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submitReport()" class="student-wizard-body">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label for="website-field">Website</label>
                        <input id="website-field" type="text" name="website" x-model="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Error Banner -->
                    <div x-cloak x-show="errorMessage" x-transition class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-lg shrink-0">error</span>
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- LANGKAH 1: MELAPOR SEBAGAI -->
                    <div x-show="currentStep === 1" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Kamu melapor sebagai siapa?</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Pilih peran yang paling sesuai dengan kondisimu saat ini.</p>
                        </div>
                        <div class="student-role-grid grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @php
                                $roles = [
                                    ['value' => 'Siswa (Korban Langsung)', 'title' => 'Korban Langsung', 'desc' => 'Aku mengalami langsung kejadian ini', 'icon' => 'person'],
                                    ['value' => 'Saksi Kejadian', 'title' => 'Saksi Kejadian', 'desc' => 'Aku melihat atau mengetahui kejadian ini', 'icon' => 'visibility'],
                                    ['value' => 'Teman Korban', 'title' => 'Teman Korban', 'desc' => 'Aku membantu melaporkan untuk temanku', 'icon' => 'group'],
                                    ['value' => 'Orang Tua / Wali Siswa', 'title' => 'Orang Tua / Wali', 'desc' => 'Melapor mewakili putra/putri kami', 'icon' => 'family_restroom'],
                                ];
                            @endphp
                            @foreach($roles as $role)
                            <button type="button" :aria-pressed="reporterRole === @js($role['value'])" @click="reporterRole = @js($role['value']); errorMessage = ''"
                                :class="reporterRole === @js($role['value']) ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="reporterRole === @js($role['value']) ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-slate-200'">
                                    <span class="material-symbols-outlined text-[20px]">{{ $role['icon'] }}</span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-sm font-bold block">{{ $role['title'] }}</span>
                                    <span class="text-[11px] text-slate-500 leading-snug block mt-0.5">{{ $role['desc'] }}</span>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- LANGKAH 2: JENIS KEJADIAN -->
                    <div x-cloak x-show="currentStep === 2" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Jenis kejadian apa yang kamu alami atau saksikan?</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Pilih satu kategori yang paling mendekati ceritamu. <span class="text-red-500">*</span></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach ($categories as $cat)
                            <button type="button" @click="category = @js($cat->label); errorMessage = ''"
                                :class="category === @js($cat->label) ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-800'"
                                class="p-3.5 rounded-2xl border text-left transition-all flex items-center justify-between gap-2.5 cursor-pointer">
                                <span class="text-xs sm:text-sm font-semibold leading-snug">{{ $cat->label }}</span>
                                <span class="material-symbols-outlined text-[19px] shrink-0" :class="category === @js($cat->label) ? 'text-blue-600' : 'text-slate-300'" x-text="category === @js($cat->label) ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- LANGKAH 3: TITIK LOKASI KEJADIAN -->
                    <div x-cloak x-show="currentStep === 3" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Di mana kejadian itu berlangsung?</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Pilih lokasi kejadian atau pilih Lainnya untuk menuliskan sendiri. <span class="text-red-500">*</span></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach ($locations as $loc)
                            <button type="button" @click="location = @js($loc->label); errorMessage = ''"
                                :class="location === @js($loc->label) ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-800'"
                                class="p-3.5 rounded-2xl border text-left transition-all flex items-center justify-between gap-2.5 cursor-pointer">
                                <span class="flex items-center gap-2 text-xs sm:text-sm font-semibold">
                                    <span class="material-symbols-outlined text-[18px]" :class="location === @js($loc->label) ? 'text-blue-600' : 'text-slate-400'">pin_drop</span>
                                    <span>{{ $loc->label }}</span>
                                </span>
                                <span class="material-symbols-outlined text-[19px] shrink-0" :class="location === @js($loc->label) ? 'text-blue-600' : 'text-slate-300'" x-text="location === @js($loc->label) ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>
                            @endforeach
                            <button type="button" @click="location = '__other__'; errorMessage = ''"
                                :class="location === '__other__' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-800'"
                                class="p-3.5 rounded-2xl border text-left transition-all flex items-center justify-between gap-2.5 cursor-pointer">
                                <span class="flex items-center gap-2 text-xs sm:text-sm font-semibold">
                                    <span class="material-symbols-outlined text-[18px]" :class="location === '__other__' ? 'text-blue-600' : 'text-slate-400'">edit_location_alt</span>
                                    <span>Lainnya (ketik sendiri)</span>
                                </span>
                                <span class="material-symbols-outlined text-[19px] shrink-0" :class="location === '__other__' ? 'text-blue-600' : 'text-slate-300'" x-text="location === '__other__' ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>
                        </div>
                        <div x-cloak x-show="location === '__other__'" x-transition class="flex flex-col gap-1.5 pt-1">
                            <label for="custom-location-input" class="text-xs font-bold text-slate-700">Sebutkan Tempat Kejadian Lainnya <span class="text-red-500">*</span></label>
                            <input id="custom-location-input" type="text" x-model="customLocation" maxlength="120" placeholder="Contoh: Perpustakaan lantai 2, area parkir, luar gerbang..." class="w-full px-4 py-3 bg-white text-on-surface rounded-xl text-xs sm:text-sm border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- LANGKAH 4: KEBUTUHAN RESPONS BANTUAN -->
                    <div x-cloak x-show="currentStep === 4" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Kebutuhan Respons Bantuan</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Pilih kondisi yang paling menggambarkan kebutuhanmu agar Guru BK bisa memprioritaskan bantuan.</p>
                        </div>
                        <div class="flex flex-col gap-3">
                            <button type="button" @click="urgency = 'Darurat'; errorMessage = ''"
                                :class="urgency === 'Darurat' ? 'bg-rose-50 border-rose-400 ring-2 ring-rose-200 text-rose-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-center justify-between gap-3 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded-full bg-rose-600 shrink-0"></span>
                                    <div>
                                        <span class="text-sm font-bold block">Darurat</span>
                                        <span class="text-xs text-slate-500 block">Ada ancaman fisik, kekerasan langsung, atau bahaya mendesak</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[20px]" :class="urgency === 'Darurat' ? 'text-rose-600' : 'text-slate-300'" x-text="urgency === 'Darurat' ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>

                            <button type="button" @click="urgency = 'Sedang'; dangerConfirmed = false; errorMessage = ''"
                                :class="urgency === 'Sedang' ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-200 text-amber-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-center justify-between gap-3 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded-full bg-amber-500 shrink-0"></span>
                                    <div>
                                        <span class="text-sm font-bold block">Penting</span>
                                        <span class="text-xs text-slate-500 block">Tekanan berat berulang, perundungan, atau intimidasi yang mengganggu</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[20px]" :class="urgency === 'Sedang' ? 'text-amber-600' : 'text-slate-300'" x-text="urgency === 'Sedang' ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>

                            <button type="button" @click="urgency = 'Rendah'; dangerConfirmed = false; errorMessage = ''"
                                :class="urgency === 'Rendah' ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-200 text-emerald-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-center justify-between gap-3 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 shrink-0"></span>
                                    <div>
                                        <span class="text-sm font-bold block">Konseling</span>
                                        <span class="text-xs text-slate-500 block">Butuh teman bercerita, saran, atau pendampingan tenang dari Guru BK</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[20px]" :class="urgency === 'Rendah' ? 'text-emerald-600' : 'text-slate-300'" x-text="urgency === 'Rendah' ? 'check_circle' : 'radio_button_unchecked'"></span>
                            </button>
                        </div>

                        <div x-cloak x-show="urgency === 'Darurat'" x-transition class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900">
                            <label class="flex items-start gap-2.5 text-xs cursor-pointer">
                                <input type="checkbox" x-model="dangerConfirmed" @change="errorMessage = ''" class="mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-rose-300">
                                <span>
                                    <strong class="font-bold">Konfirmasi Darurat:</strong> Saya menyatakan kejadian ini melibatkan ancaman fisik atau bahaya mendesak yang perlu notifikasi segera ke Guru BK.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- LANGKAH 5: TANGGAL & WAKTU KEJADIAN -->
                    <div x-cloak x-show="currentStep === 5" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Kapan kejadian tersebut berlangsung?</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Pilih tanggal (atau centang tidak ingat) beserta perkiraan bagian waktunya.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-surface-container-low/70 border border-surface-container flex flex-col gap-2.5">
                            <label for="incident-date-input" class="text-xs font-bold text-on-surface uppercase tracking-wider">Tanggal Kejadian</label>
                            <input id="incident-date-input" type="date" x-model="incidentDate" :disabled="incidentDateUnknown" max="{{ now('Asia/Jakarta')->toDateString() }}" class="w-full px-4 py-3 bg-white text-on-surface rounded-xl text-sm border border-outline-variant/40 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:opacity-50">
                            <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer mt-0.5">
                                <input type="checkbox" x-model="incidentDateUnknown" @change="if (incidentDateUnknown) incidentDate = ''; errorMessage = ''" class="rounded text-blue-600 focus:ring-blue-500">
                                <span>Saya tidak ingat tanggal pastinya</span>
                            </label>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Perkiraan Bagian Waktu</label>
                            @php
                                $timeOptions = [
                                    'Pagi (Jam Pelajaran Sekolah)',
                                    'Siang / Jam Istirahat',
                                    'Sore / Jam Pulang Sekolah',
                                    'Malam (Daring / Luar Sekolah)',
                                    'Tidak ingat pasti',
                                ];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($timeOptions as $timeOpt)
                                <button type="button" @click="incidentTime = @js($timeOpt)"
                                    :class="incidentTime === @js($timeOpt) ? 'bg-blue-50 border-blue-500 text-blue-900 font-bold' : 'bg-surface-container-low/50 border-surface-container text-slate-700 hover:bg-surface-container-low font-medium'"
                                    class="px-3.5 py-2.5 rounded-xl border text-left text-xs transition-all flex items-center justify-between gap-2 cursor-pointer">
                                    <span>{{ $timeOpt }}</span>
                                    <span class="material-symbols-outlined text-[16px]" :class="incidentTime === @js($timeOpt) ? 'text-blue-600' : 'text-slate-300'" x-text="incidentTime === @js($timeOpt) ? 'check_circle' : 'radio_button_unchecked'"></span>
                                </button>
                                @endforeach
                            </div>
                            <span class="text-[11px] text-slate-400">Jam &amp; menit rinci sengaja tidak dikumpulkan demi menjaga privasimu.</span>
                        </div>
                    </div>

                    <!-- LANGKAH 6: PIHAK TERLIBAT / SAKSI (OPSIONAL) -->
                    <div x-cloak x-show="currentStep === 6" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                                <h2 id="involvedParties-heading" class="text-lg sm:text-xl font-bold text-on-surface">Siapa pihak yang terlibat atau saksi?</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Kamu boleh menuliskan ciri, peran, atau langsung tekan <strong>Lanjut</strong> bila ragu atau ingin mengosongkan.</p>
                        </div>
                        <div class="flex min-w-0 flex-col gap-1.5 px-0.5">
                            <span id="involvedParties-meta" class="self-end px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">Opsional</span>
                            <input type="text" x-model="involvedParties" maxlength="255" placeholder="Misal: Kakak kelas, teman sekelas, atau biarkan kosong..." aria-labelledby="involvedParties-heading" aria-describedby="involvedParties-meta" class="box-border min-w-0 w-full px-4 py-3.5 bg-surface-container-low text-on-surface rounded-2xl text-sm border border-outline-variant/40 focus:bg-white focus:inset-ring-2 focus:inset-ring-blue-500 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- LANGKAH 7: CERITAKAN APA YANG TERJADI -->
                    <div x-cloak x-show="currentStep === 7" x-transition.opacity class="flex flex-col gap-3.5">
                            <div>
                                <h2 id="story-heading" class="text-lg sm:text-xl font-bold text-on-surface">Ceritakan apa yang terjadi <span class="text-red-500">*</span></h2>
                                <p class="text-xs text-on-surface-variant mt-1">Tulis dengan tenang sesuai bahasamu sendiri. Owy &amp; Guru BK siap mendengarkan.</p>
                            </div>
                        <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-[11px] text-blue-900 leading-relaxed">
                            <strong>Pertanyaan pemandu:</strong> Apa yang terjadi? Sejak kapan berlangsung? Dan dukungan apa yang kamu harapkan dari Guru BK?
                        </div>
                        <div class="flex min-w-0 flex-col gap-1.5 px-0.5">
                            <span id="story-meta" class="self-end text-[11px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 shrink-0" x-text="story.length + ' / {{ $storyMaxLength }}'"></span>
                            <textarea x-model="story" maxlength="{{ $storyMaxLength }}" rows="6" placeholder="Tuliskan ceritamu di sini dengan tenang. Jika memilih melapor anonim, hindari menulis nama atau ciri yang langsung mengarah ke dirimu..." aria-labelledby="story-heading" aria-describedby="story-meta" class="box-border min-w-0 w-full p-4 bg-surface-container-low text-on-surface rounded-2xl text-sm border border-outline-variant/40 focus:bg-white focus:inset-ring-2 focus:inset-ring-blue-500 focus:outline-none leading-relaxed transition-all"></textarea>
                        </div>
                    </div>

                    <!-- LANGKAH 8: ANONIM VS INGIN DIHUBUNGI + KONFIRMASI KIRIM -->
                    <div x-cloak x-show="currentStep === 8" x-transition.opacity class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-on-surface">Pilihan Kerahasiaan &amp; Kirim Aduan</h2>
                            <p class="text-xs text-on-surface-variant mt-1">Tentukan apakah kamu ingin melapor secara anonim atau bersedia dihubungi langsung oleh Guru BK.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" @click="isAnonymous = true; errorMessage = ''"
                                :class="isAnonymous ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer">
                                <span class="material-symbols-outlined text-[22px] shrink-0" :class="isAnonymous ? 'text-blue-600' : 'text-slate-400'">visibility_off</span>
                                <div>
                                    <span class="text-sm font-bold block">Kirim Sebagai Anonim</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">Nama, kelas, dan kontak tidak disimpan. Kamu tetap bisa chat via kode tiket.</span>
                                </div>
                            </button>

                            <button type="button" @click="isAnonymous = false; errorMessage = ''"
                                :class="!isAnonymous ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200 text-blue-950' : 'bg-surface-container-low/60 border-surface-container hover:bg-surface-container-low text-slate-700'"
                                class="p-4 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer">
                                <span class="material-symbols-outlined text-[22px] shrink-0" :class="!isAnonymous ? 'text-blue-600' : 'text-slate-400'">support_agent</span>
                                <div>
                                    <span class="text-sm font-bold block">Ingin Dihubungi Guru BK</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">Identitasmu terenkripsi dan hanya bisa dilihat khusus oleh konselor BK.</span>
                                </div>
                            </button>
                        </div>

                        <!-- Form Identitas jika memilih Ingin Dihubungi -->
                        <div x-cloak x-show="!isAnonymous" x-transition class="p-4 rounded-2xl bg-blue-50/50 border border-blue-200 space-y-3">
                            <p class="text-[11px] text-blue-900">
                                <strong>Ketentuan Ingin Dihubungi Guru BK:</strong> <strong>Nama</strong> dan <strong>Kelas wajib diisi</strong> agar Guru BK mengetahui siswa yang perlu didampingi. <strong>Nomor WhatsApp / Kontak bersifat opsional</strong>.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="flex flex-col gap-1">
                                    <label class="text-[11px] font-bold text-slate-700">Nama Lengkap / Panggilan <span class="text-red-600">*</span></label>
                                    <input type="text" x-model="reporterName" placeholder="Nama lengkapmu" class="w-full px-3 py-2.5 bg-white rounded-xl text-xs border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[11px] font-bold text-slate-700">Kelas <span class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <select x-model="reporterClass" class="w-full px-3 py-2.5 bg-white rounded-xl text-xs border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none pr-8 cursor-pointer text-slate-800">
                                            <option value="">-- Pilih Kelas --</option>
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
                                    <label class="text-[11px] font-bold text-slate-700">No. WhatsApp <span class="font-normal text-slate-500">(Opsional)</span></label>
                                    <input type="text" x-model="reporterContact" placeholder="Boleh dikosongkan" class="w-full px-3 py-2.5 bg-white rounded-xl text-xs border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Singkat Sebelum Kirim -->
                        <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-container text-xs space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Ringkasan Aduanmu</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Peran:</span>
                                    <span class="font-semibold text-slate-800 block truncate" x-text="reporterRole"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Jenis Kejadian:</span>
                                    <span class="font-semibold text-slate-800 block truncate" x-text="category || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Lokasi:</span>
                                    <span class="font-semibold text-slate-800 block truncate" x-text="resolvedLocationDisplay()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Respons Bantuan:</span>
                                    <span class="font-semibold text-slate-800 block" x-text="urgency === 'Darurat' ? 'Darurat' : (urgency === 'Sedang' ? 'Penting' : 'Konseling')"></span>
                                </div>
                            </div>
                        </div>

                        @if(!empty($turnstileSiteKey))
                        <div class="pt-1">
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                            <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                        </div>
                        @endif
                    </div>

                    <!-- Wizard Footer Navigation -->
                    <div class="student-step-actions pt-4 border-t border-surface-container flex items-center justify-between gap-3">
                        <button type="button" x-show="currentStep > 1" @click="prevStep()" class="px-4 py-3 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs sm:text-sm transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Kembali</span>
                        </button>
                        <div x-show="currentStep === 1" class="text-[11px] text-slate-400">Jawabanmu tersimpan otomatis jika halaman dimuat ulang.</div>

                        <button type="button" x-show="currentStep < totalSteps" @click="nextStep()" class="ml-auto px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            <span x-text="currentStep === 6 && !involvedParties.trim() ? 'Lewati / Lanjut' : 'Lanjut'">Lanjut</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>

                        <button type="submit" x-cloak x-show="currentStep === totalSteps" :disabled="submitting || (urgency === 'Darurat' && !dangerConfirmed)" class="ml-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl font-bold text-xs sm:text-sm transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer">
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
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: LACAK ADUAN SAYA                                                   -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'track'" x-transition.opacity class="bg-surface-container-lowest rounded-3xl border border-surface-container p-6 sm:p-8 flex flex-col gap-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center p-2 shrink-0">
                    <img src="{{ asset('maskot_owy/maskot_9.png') }}" alt="Owy membantu melacak aduan" class="w-full h-full object-contain owy-float">
                </div>
                <div>
                    <div class="flex items-center gap-1.5 text-blue-600 font-semibold text-xs uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-[17px]">travel_explore</span>
                        <span>Transparansi &amp; Keamanan Penanganan</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">Cek Status Aduanmu</h2>
                    <p class="text-xs sm:text-sm text-on-surface-variant mt-1 leading-relaxed">
                        Masukkan kode laporan 10 karakter untuk melihat status penanganan, jam dibaca Guru BK, serta membalas pesan secara dua arah.
                    </p>
                </div>
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

            <!-- Tracking Result Box -->
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

    <!-- Alpine Logic untuk Wizard Owy, Persistensi Refresh, Wajib Salin Kode, & Lacak Aduan -->
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
            const draftStorageKey = 'hiowy_reporter_state_v1:' + window.location.pathname;
            const mascotBase = @js(asset('maskot_owy'));
            const stepMetaMap = {
                1: {
                    image: mascotBase + '/maskot_1.png',
                    alt: 'Owy melambai menyapa pelapor',
                    badge: 'Hai! Aku Owy',
                    speech: 'Senang kamu mau bercerita hari ini. Sebelum mulai, kamu melapor sebagai siapa?'
                },
                2: {
                    image: mascotBase + '/maskot_2.png',
                    alt: 'Owy mendengarkan dengan penuh perhatian',
                    badge: 'Owy Siap Membantu',
                    speech: 'Tenang ya, pelan-pelan saja. Jenis kejadian apa yang sedang kamu alami atau saksikan?'
                },
                3: {
                    image: mascotBase + '/maskot_9.png',
                    alt: 'Owy membawa kaca pembesar mencari lokasi',
                    badge: 'Catat Lokasi Kejadian',
                    speech: 'Owy bantu catat lokasinya supaya Guru BK lebih paham situasinya. Di mana kejadian itu terjadi?'
                },
                4: {
                    image: mascotBase + '/maskot_4.png',
                    alt: 'Owy peduli dan empatik',
                    badge: 'Keselamatanmu Utama',
                    speech: 'Kamu berhak merasa aman. Seberapa mendesak bantuan yang kamu butuhkan saat ini?'
                },
                5: {
                    image: mascotBase + '/maskot_7.png',
                    alt: 'Owy memegang catatan tanggal dan waktu',
                    badge: 'Waktu Kejadian',
                    speech: 'Kira-kira kapan kejadian itu terjadi? Kalau lupa tanggal pastinya, centang saja tidak ingat ya!'
                },
                6: {
                    image: mascotBase + '/maskot_3.png',
                    alt: 'Owy tersenyum lembut',
                    badge: 'Info Tambahan (Opsional)',
                    speech: 'Apakah ada pihak lain atau saksi yang terlibat? Kalau ragu, bagian ini boleh kamu lewati kok.'
                },
                7: {
                    image: mascotBase + '/maskot_10.png',
                    alt: 'Owy duduk tenang mendengarkan ceritamu',
                    badge: 'Ruang Cerita Aman',
                    speech: 'Aku duduk mendengarkan dengan tenang. Tuliskan apa yang kamu alami atau saksikan di sini ya.'
                },
                8: {
                    image: mascotBase + '/maskot_6.png',
                    alt: 'Owy memegang perisai perlindungan',
                    badge: 'Satu Langkah Terakhir',
                    speech: 'Pilih apakah kamu ingin melapor secara anonim atau ingin dihubungi langsung oleh Guru BK.'
                }
            };

            let showPrivacy = false;
            let saved = {};
            try {
                showPrivacy = sessionStorage.getItem('hiowy_privacy_seen') !== '1';
                const raw = sessionStorage.getItem(draftStorageKey);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object') {
                        saved = parsed;
                    }
                }
            } catch (e) {
                showPrivacy = true;
            }

            const restoredStep = Math.min(8, Math.max(1, Number(saved.currentStep) || 1));

            return {
                privacyModalOpen: showPrivacy,
                activeTab: saved.activeTab === 'track' ? 'track' : 'form',
                currentStep: restoredStep,
                maxReachedStep: Math.min(8, Math.max(restoredStep, Number(saved.maxReachedStep) || 1)),
                totalSteps: 8,
                submitted: false,
                ticketCopied: false,
                submitting: false,
                tracking: false,
                errorMessage: '',
                trackError: '',
                ticketCode: '',
                submittedCategoryLabel: '',
                submittedLocationLabel: '',
                submittedAnonymous: true,

                // Form fields (dipulihkan dari sessionStorage saat refresh)
                category: typeof saved.category === 'string' ? saved.category : '',
                location: typeof saved.location === 'string' ? saved.location : '',
                customLocation: typeof saved.customLocation === 'string' ? saved.customLocation : '',
                involvedParties: typeof saved.involvedParties === 'string' ? saved.involvedParties : '',
                urgency: ['Darurat', 'Sedang', 'Rendah'].includes(saved.urgency) ? saved.urgency : 'Rendah',
                dangerConfirmed: Boolean(saved.dangerConfirmed),
                reporterRole: typeof saved.reporterRole === 'string' && saved.reporterRole ? saved.reporterRole : 'Siswa (Korban Langsung)',
                incidentDate: typeof saved.incidentDate === 'string' ? saved.incidentDate : '',
                incidentDateUnknown: Boolean(saved.incidentDateUnknown),
                incidentTime: typeof saved.incidentTime === 'string' && saved.incidentTime ? saved.incidentTime : 'Pagi (Jam Pelajaran Sekolah)',
                story: typeof saved.story === 'string' ? saved.story : '',
                website: '',
                isAnonymous: typeof saved.isAnonymous === 'boolean' ? saved.isAnonymous : true,
                reporterName: typeof saved.reporterName === 'string' ? saved.reporterName : '',
                reporterClass: typeof saved.reporterClass === 'string' ? saved.reporterClass : '',
                reporterContact: typeof saved.reporterContact === 'string' ? saved.reporterContact : '',

                // Tracking tab (dipulihkan saat refresh)
                searchCode: typeof saved.searchCode === 'string' ? saved.searchCode : '',
                hasTrackedTicket: Boolean(saved.hasTrackedTicket),
                ticketData: null,
                replyBody: typeof saved.replyBody === 'string' ? saved.replyBody : '',
                replying: false,
                replyError: '',
                replySuccess: '',

                get currentOwy() {
                    return stepMetaMap[this.currentStep] || stepMetaMap[1];
                },

                init() {
                    const persistedKeys = [
                        'activeTab', 'currentStep', 'maxReachedStep',
                        'category', 'location', 'customLocation', 'involvedParties',
                        'urgency', 'dangerConfirmed', 'reporterRole',
                        'incidentDate', 'incidentDateUnknown', 'incidentTime',
                        'story', 'isAnonymous', 'reporterName', 'reporterClass', 'reporterContact',
                        'searchCode', 'replyBody', 'hasTrackedTicket'
                    ];
                    persistedKeys.forEach((key) => {
                        this.$watch(key, () => this.persistDraftState());
                    });

                    window.addEventListener('beforeunload', () => this.persistDraftState());

                    if (this.activeTab === 'track' && this.hasTrackedTicket && this.searchCode.trim().length >= 10) {
                        this.searchTicket();
                    }
                },

                persistDraftState() {
                    try {
                        sessionStorage.setItem(draftStorageKey, JSON.stringify({
                            activeTab: this.activeTab,
                            currentStep: this.currentStep,
                            maxReachedStep: this.maxReachedStep,
                            category: this.category,
                            location: this.location,
                            customLocation: this.customLocation,
                            involvedParties: this.involvedParties,
                            urgency: this.urgency,
                            dangerConfirmed: this.dangerConfirmed,
                            reporterRole: this.reporterRole,
                            incidentDate: this.incidentDate,
                            incidentDateUnknown: this.incidentDateUnknown,
                            incidentTime: this.incidentTime,
                            story: this.story,
                            isAnonymous: this.isAnonymous,
                            reporterName: this.reporterName,
                            reporterClass: this.reporterClass,
                            reporterContact: this.reporterContact,
                            searchCode: this.searchCode,
                            replyBody: this.replyBody,
                            hasTrackedTicket: this.hasTrackedTicket,
                        }));
                    } catch (e) {}
                },

                clearDraftStorage() {
                    try {
                        sessionStorage.removeItem(draftStorageKey);
                    } catch (e) {}
                },

                switchTab(tab) {
                    this.activeTab = tab;
                    this.errorMessage = '';
                    this.persistDraftState();
                },

                dismissPrivacyModal() {
                    this.privacyModalOpen = false;
                    try {
                        sessionStorage.setItem('hiowy_privacy_seen', '1');
                    } catch (e) {}
                },

                quickExit() {
                    this.wipeClientState();
                    this.clearDraftStorage();
                    try {
                        sessionStorage.setItem('hiowy_exited', '1');
                        window.history.replaceState(null, '', '/');
                    } catch (e) {}
                    window.location.replace('https://www.google.com');
                },

                wipeClientState() {
                    this.category = '';
                    this.location = '';
                    this.customLocation = '';
                    this.involvedParties = '';
                    this.urgency = 'Rendah';
                    this.dangerConfirmed = false;
                    this.reporterRole = 'Siswa (Korban Langsung)';
                    this.incidentDate = '';
                    this.incidentDateUnknown = false;
                    this.incidentTime = 'Pagi (Jam Pelajaran Sekolah)';
                    this.story = '';
                    this.website = '';
                    this.isAnonymous = true;
                    this.reporterName = '';
                    this.reporterClass = '';
                    this.reporterContact = '';
                    this.replyBody = '';
                    this.currentStep = 1;
                    this.maxReachedStep = 1;
                },

                validateStep(step) {
                    this.errorMessage = '';
                    if (step === 1 && !this.reporterRole) {
                        this.errorMessage = 'Pilih peran pelapor terlebih dahulu.';
                        return false;
                    }
                    if (step === 2 && !this.category) {
                        this.errorMessage = 'Pilih jenis kejadian yang paling sesuai terlebih dahulu.';
                        return false;
                    }
                    if (step === 3) {
                        if (!this.location) {
                            this.errorMessage = 'Pilih titik lokasi kejadian terlebih dahulu.';
                            return false;
                        }
                        if (this.location === '__other__' && !this.customLocation.trim()) {
                            this.errorMessage = 'Tuliskan tempat kejadian lainnya terlebih dahulu.';
                            return false;
                        }
                    }
                    if (step === 4 && this.urgency === 'Darurat' && !this.dangerConfirmed) {
                        this.errorMessage = 'Centang kotak konfirmasi darurat terlebih dahulu sebelum melanjutkan.';
                        return false;
                    }
                    if (step === 5 && !this.incidentDateUnknown && !this.incidentDate) {
                        this.errorMessage = 'Pilih tanggal kejadian atau centang "Saya tidak ingat tanggal pastinya".';
                        return false;
                    }
                    if (step === 7 && this.story.trim().length < 5) {
                        this.errorMessage = 'Tuliskan ceritamu minimal 5 karakter agar Guru BK dapat memahami situasinya.';
                        return false;
                    }
                    if (step === 8 && !this.isAnonymous && (!this.reporterName.trim() || !this.reporterClass)) {
                        this.errorMessage = 'Jika ingin dihubungi Guru BK, Nama dan Kelas wajib diisi (WhatsApp bersifat opsional).';
                        return false;
                    }
                    return true;
                },

                nextStep() {
                    if (!this.validateStep(this.currentStep)) return;
                    if (this.currentStep < this.totalSteps) {
                        this.currentStep += 1;
                        if (this.currentStep > this.maxReachedStep) {
                            this.maxReachedStep = this.currentStep;
                        }
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                prevStep() {
                    this.errorMessage = '';
                    if (this.currentStep > 1) {
                        this.currentStep -= 1;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                jumpToStep(step) {
                    if (step > this.maxReachedStep || step === this.currentStep) return;
                    if (step > this.currentStep && !this.validateStep(this.currentStep)) return;
                    this.errorMessage = '';
                    this.currentStep = step;
                },

                resolvedLocationDisplay() {
                    if (this.location === '__other__') {
                        return 'Lainnya: ' + (this.customLocation || '-');
                    }
                    return this.location || 'Area Sekolah';
                },

                async submitReport() {
                    if (this.submitting) return;
                    for (const s of [1, 2, 3, 4, 5, 7, 8]) {
                        if (!this.validateStep(s)) {
                            this.currentStep = s;
                            return;
                        }
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
                            this.submittedCategoryLabel = this.category || 'Umum';
                            this.submittedLocationLabel = this.resolvedLocationDisplay();
                            this.submittedAnonymous = this.isAnonymous;
                            this.ticketCopied = false;
                            this.submitted = true;
                            this.wipeClientState();
                            this.persistDraftState();
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

                async copyTicketCode() {
                    if (!this.ticketCode) return;
                    let copied = false;
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(this.ticketCode);
                            copied = true;
                        }
                    } catch (e) {
                        copied = false;
                    }

                    if (!copied) {
                        try {
                            const temp = document.createElement('textarea');
                            temp.value = this.ticketCode;
                            temp.setAttribute('readonly', '');
                            temp.style.position = 'fixed';
                            temp.style.opacity = '0';
                            document.body.appendChild(temp);
                            temp.select();
                            copied = document.execCommand('copy');
                            document.body.removeChild(temp);
                        } catch (e) {
                            copied = false;
                        }
                    }

                    if (copied) {
                        this.ticketCopied = true;
                    } else {
                        const manual = prompt('Salin kode laporanmu di bawah ini (Ctrl+C lalu OK):', this.ticketCode);
                        if (manual !== null) {
                            this.ticketCopied = true;
                        }
                    }
                },

                goToTrack(code) {
                    if (!this.ticketCopied) return;
                    this.searchCode = code;
                    this.activeTab = 'track';
                    this.submitted = false;
                    this.searchTicket();
                },

                resetForm() {
                    if (!this.ticketCopied) return;
                    this.submitted = false;
                    this.ticketCopied = false;
                    this.ticketCode = '';
                    this.errorMessage = '';
                    this.wipeClientState();
                    this.persistDraftState();
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
                            this.hasTrackedTicket = true;
                            this.persistDraftState();
                        } else {
                            this.hasTrackedTicket = false;
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
                            this.persistDraftState();
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
