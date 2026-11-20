<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulir Pengaduan Siswa — {{ $schoolName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..24,400..600,0..1,-50..200" />
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col justify-between font-sans antialiased" x-data="publicReporter()">

    <!-- Top Navigation Header -->
    <header class="w-full bg-surface-container-lowest border-b border-surface-container/80 py-3.5 px-4 sm:px-6 sticky top-0 z-30 shadow-xs backdrop-blur-md bg-white/95">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                    <span class="material-symbols-outlined text-[24px]">school</span>
                </div>
                <div class="min-w-0">
                    <h1 class="font-bold text-on-surface text-sm sm:text-base leading-tight truncate">{{ $schoolName }}</h1>
                    <p class="text-[11px] sm:text-xs text-on-surface-variant truncate">Ruang Konsultasi Aman &amp; Pendampingan Siswa BK</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('public.learn.default') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-surface-container hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">auto_stories</span>
                    <span>Materi Edukasi</span>
                </a>
                <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] sm:text-xs font-semibold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Terenkripsi</span>
                </span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-2xl w-full mx-auto p-4 sm:p-6 my-4 sm:my-6 flex-1 flex flex-col gap-5">
        @if(!empty($trainingMode))
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-lg text-amber-700 shrink-0">science</span>
            <div>
                <strong class="font-bold">Mode Latihan / Simulasi Aktif:</strong>
                <span>Laporan uji coba yang kamu kirim saat ini tidak akan masuk ke daftar aduan asli sekolah maupun mengirim peringatan darurat.</span>
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

        <!-- ========================================================================= -->
        <!-- TAB 1: FORMULIR PENGADUAN SISWA -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'form'" x-transition.opacity>

            <!-- Success Card (When submitted) -->
            <div x-cloak x-show="submitted" class="bg-surface-container-lowest rounded-2xl border border-surface-container p-6 sm:p-8 text-center flex flex-col items-center gap-4 shadow-sm animate-in fade-in zoom-in-95 duration-200">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Aduanmu Telah Diterima Guru BK</h2>
                    <p class="text-xs sm:text-sm text-on-surface-variant max-w-md mx-auto leading-relaxed mt-1">
                        Terima kasih sudah berani bersuara. Tim Konselor {{ $schoolName }} mendengarkan ceritamu dengan empati dan jaminan perlindungan penuh.
                    </p>
                </div>

                <!-- Ticket Box -->
                <div class="w-full max-w-md p-4 bg-surface-container-low rounded-2xl border border-surface-container flex flex-col gap-3 my-2 text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-surface-container/60">
                        <span class="text-xs font-semibold text-on-surface-variant">Nomor Tiket Aduan</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-bold text-base text-blue-700" x-text="ticketCode"></span>
                            <button type="button" @click="copyTicketCode()" class="p-1 rounded-lg hover:bg-surface-container text-slate-500 hover:text-slate-900 transition-colors cursor-pointer" title="Salin kode tiket">
                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block">Kategori:</span>
                            <span class="font-semibold text-slate-800 truncate block" x-text="category || 'Umum'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Lokasi:</span>
                            <span class="font-semibold text-slate-800 truncate block" x-text="location || 'Area Sekolah'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Status Awal:</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Menunggu Telaah Konselor
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Kerahasiaan:</span>
                            <span class="font-semibold text-slate-800" x-text="isAnonymous ? 'Anonim (Terproteksi)' : 'Data Diri Terlampir'"></span>
                        </div>
                    </div>
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
                        <span>Ruang Dialog Bebas Tekanan &amp; Rasa Takut</span>
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

                    <!-- 2. Titik Lokasi Kejadian -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Titik Lokasi Kejadian <span class="text-red-500">*</span></span>
                            <span class="text-[11px] text-slate-400 font-normal normal-case">Di mana kejadian berlangsung?</span>
                        </label>
                        <select x-model="location" required class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none transition-all">
                            <option value="">-- Pilih Lokasi Kejadian --</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->label }}">{{ $loc->label }}</option>
                            @endforeach
                        </select>
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

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border transition-all cursor-pointer" :class="urgency === 'Tinggi' ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-400 text-amber-900' : 'bg-surface-container-low/50 border-surface-container hover:bg-surface-container-low text-slate-700'">
                                <input type="radio" x-model="urgency" value="Tinggi" class="text-amber-600 focus:ring-amber-500">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-600"></span> Penting
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight">Tekanan berat berulang</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border transition-all cursor-pointer" :class="urgency === 'Sedang' ? 'bg-blue-50/70 border-blue-300 ring-1 ring-blue-400 text-blue-900' : 'bg-surface-container-low/50 border-surface-container hover:bg-surface-container-low text-slate-700'">
                                <input type="radio" x-model="urgency" value="Sedang" class="text-blue-600 focus:ring-blue-500">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Konseling
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

                    <!-- 4. Peran Pelapor & Waktu Kejadian -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Peran Kamu</label>
                            <select x-model="reporterRole" class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                                <option value="Siswa (Korban Langsung)">Saya Korban Langsung</option>
                                <option value="Teman / Saksi Kejadian">Saya Teman / Saksi Kejadian</option>
                                <option value="Orang Tua / Wali Siswa">Orang Tua / Wali Siswa</option>
                                <option value="Warga Sekolah">Warga Sekolah / Lainnya</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Waktu Kejadian</label>
                            <select x-model="incidentTime" class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                                <option value="Hari ini saat istirahat">Hari ini saat istirahat</option>
                                <option value="Hari ini saat jam pelajaran">Hari ini saat jam pelajaran</option>
                                <option value="Saat jam pulang sekolah">Saat jam pulang sekolah</option>
                                <option value="Pekan ini berulang kali">Pekan ini berulang kali</option>
                                <option value="Waktu lainnya">Waktu lainnya / tidak pasti</option>
                            </select>
                        </div>
                    </div>

                    <!-- 5. Pihak Terlibat (Opsional) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Pihak yang Terlibat / Saksi <span class="text-[11px] font-normal text-slate-400 normal-case">(Opsional)</span></span>
                        </label>
                        <input type="text" x-model="involvedParties" placeholder="Misal: Kakak kelas, teman sekelas, atau nama jika ingin disebutkan" class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                    </div>

                    <!-- 6. Cerita Kejadian -->
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Ceritakan Apa yang Terjadi <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400" x-text="story.length + ' karakter'"></span>
                        </div>
                        <textarea x-model="story" required rows="5" placeholder="Tuliskan dengan tenang apa yang kamu rasakan atau saksikan. Ceritakan apa adanya, tim konselor mendengarkan tanpa menghakimi..." class="w-full p-4 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none leading-relaxed transition-all"></textarea>
                    </div>

                    <!-- 7. Pengaturan Anonimitas -->
                    <div class="p-4 bg-surface-container-low rounded-xl border border-surface-container flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex flex-col">
                                <span class="font-semibold text-xs sm:text-sm text-on-surface flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[17px] text-blue-600" style="font-variation-settings: 'FILL' 1;">security</span>
                                    <span>Kirim Sebagai Anonim</span>
                                </span>
                                <span class="text-[11px] text-on-surface-variant mt-0.5">
                                    Nama dan identitasmu tidak akan disimpan atau ditampilkan kepada siapapun.
                                </span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="isAnonymous" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Revealed fields if NOT anonymous -->
                        <div x-cloak x-show="!isAnonymous" x-transition class="pt-3 border-t border-slate-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-700">Nama Lengkap / Panggilan</label>
                                <input type="text" x-model="reporterName" placeholder="Nama kamu" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-700">Kelas</label>
                                <div class="relative">
                                    <select x-model="reporterClass" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none appearance-none pr-8 cursor-pointer text-slate-800">
                                        <option value="">-- Pilih Kelas --</option>
                                        <optgroup label="Kelas 7 (Tujuh)">
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
                                        <optgroup label="Kelas 8 (Delapan)">
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
                                        <optgroup label="Kelas 9 (Sembilan)">
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
                                        <optgroup label="Tingkat Lanjutan (SMA / SMK)">
                                            <option value="10A">Kelas 10A</option>
                                            <option value="10B">Kelas 10B</option>
                                            <option value="11A">Kelas 11A</option>
                                            <option value="11B">Kelas 11B</option>
                                            <option value="12A">Kelas 12A</option>
                                            <option value="12B">Kelas 12B</option>
                                        </optgroup>
                                        <optgroup label="Lainnya">
                                            <option value="Alumni">Alumni</option>
                                            <option value="Guru / Staf">Guru / Staf</option>
                                            <option value="Orang Tua">Orang Tua / Wali Siswa</option>
                                            <option value="Lainnya">Lainnya / Luar Sekolah</option>
                                        </optgroup>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-700">No. WhatsApp / Kontak</label>
                                <input type="text" x-model="reporterContact" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
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
                                <span>Kirimkan Aduan Sekarang</span>
                            </span>
                        </template>
                    </button>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: LACAK STATUS TIKET ADUAN -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'track'" x-transition.opacity class="bg-surface-container-lowest rounded-2xl border border-surface-container p-6 sm:p-8 flex flex-col gap-6 shadow-sm">
            <div>
                <div class="flex items-center gap-2 text-blue-600 font-semibold text-xs uppercase tracking-wider mb-1.5">
                    <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                    <span>Transparansi &amp; Keamanan Penanganan</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">Cek Status Aduanmu</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                    Masukkan nomor tiket aduan yang kamu peroleh saat mengirim laporan untuk melihat perkembangan penanganan oleh Guru BK.
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

            <!-- Tracking Result Box (hanya status & pesan BK <-> Pelapor) -->
            <div x-cloak x-show="ticketData" class="p-5 rounded-2xl bg-surface-container-low border border-slate-200 flex flex-col gap-4 animate-in fade-in duration-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-medium">Status Penanganan Laporan</span>
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
                        <div class="p-3.5 rounded-xl border flex flex-col gap-1"
                             :class="msg.sender === 'bk' ? 'bg-white border-blue-200/80' : 'bg-blue-50/60 border-blue-200'">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold" :class="msg.sender === 'bk' ? 'text-blue-700' : 'text-slate-800'" x-text="msg.sender_label"></span>
                                <span class="text-slate-400" x-text="msg.date"></span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line" x-text="msg.body"></p>
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

    <!-- Alpine Logic for Reporter & Tracking -->
    <script>
        function publicReporter() {
            return {
                activeTab: 'form',
                submitted: false,
                submitting: false,
                tracking: false,
                errorMessage: '',
                trackError: '',
                ticketCode: '',

                // Form fields
                category: '',
                location: '',
                involvedParties: '',
                urgency: 'Sedang',
                dangerConfirmed: false,
                reporterRole: 'Siswa (Korban Langsung)',
                incidentTime: 'Hari ini saat istirahat',
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

                async submitReport() {
                    if (this.submitting) return;
                    if (this.urgency === 'Darurat' && !this.dangerConfirmed) {
                        this.errorMessage = 'Centang kotak konfirmasi darurat terlebih dahulu sebelum mengirim laporan Darurat.';
                        return;
                    }
                    this.submitting = true;
                    this.errorMessage = '';

                    try {
                        const formData = new FormData();
                        formData.append('category', this.category);
                        formData.append('location', this.location);
                        if (this.involvedParties.trim()) {
                            formData.append('involved_parties', this.involvedParties);
                        }
                        formData.append('urgency', this.urgency);
                        formData.append('reporter_role', this.reporterRole);
                        formData.append('incident_time', this.incidentTime);
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

                        const response = await fetch('{{ route('public.report.store') }}', {
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
                            this.submitted = true;
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
                        alert('Kode laporan ' + this.ticketCode + ' berhasil disalin!');
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
                    this.involvedParties = '';
                    this.urgency = 'Sedang';
                    this.dangerConfirmed = false;
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
