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

                <!-- QR Origin Notice -->
                @if ($fromQr)
                <div class="p-3.5 bg-secondary-fixed/20 border border-secondary/30 rounded-xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary text-[24px]">qr_code_scanner</span>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-on-surface">Terhubung melalui Kode QR Resmi Sekolah</span>
                        <p class="text-[11px] text-on-surface-variant leading-snug">
                            Kanal pelaporan ini otomatis disinkronkan ke ruang konselor {{ $schoolName }}.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Feedback Error Banner -->
                <div x-cloak x-show="errorMessage" class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-lg shrink-0">error</span>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitReport()" class="flex flex-col gap-5">
                    @csrf
                    <input type="hidden" name="from_qr" value="{{ $fromQr ? '1' : '0' }}">

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

                    <!-- 5. Cerita Kejadian -->
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Ceritakan Apa yang Terjadi <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400" x-text="story.length + ' karakter'"></span>
                        </div>
                        <textarea x-model="story" required rows="5" placeholder="Tuliskan dengan tenang apa yang kamu rasakan atau saksikan. Ceritakan apa adanya, tim konselor mendengarkan tanpa menghakimi..." class="w-full p-4 bg-surface-container-low text-on-surface rounded-xl text-xs sm:text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none leading-relaxed transition-all"></textarea>
                    </div>

                    <!-- 6. Lampiran Bukti / Screenshot Chat (Opsional) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-on-surface uppercase tracking-wider flex items-center justify-between">
                            <span>Lampiran Foto Bukti / Screenshot Chat <span class="text-[11px] font-normal text-slate-400 normal-case">(Opsional)</span></span>
                        </label>
                        <div class="p-3.5 bg-surface-container-low rounded-xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-center gap-2">
                            <!-- Preview if file selected -->
                            <template x-if="attachmentPreview">
                                <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-slate-200 w-full max-w-sm">
                                    <template x-if="attachmentIsImage">
                                        <img :src="attachmentPreview" alt="Preview Lampiran" class="w-12 h-12 object-cover rounded-md border border-slate-100">
                                    </template>
                                    <template x-if="!attachmentIsImage">
                                        <div class="w-12 h-12 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-xl">description</span>
                                        </div>
                                    </template>
                                    <div class="flex-1 min-w-0 text-left">
                                        <p class="text-xs font-semibold text-slate-800 truncate" x-text="attachmentName"></p>
                                        <p class="text-[10px] text-slate-400" x-text="attachmentSize"></p>
                                    </div>
                                    <button type="button" @click="removeAttachment()" class="text-slate-400 hover:text-red-600 p-1 rounded-md transition-colors cursor-pointer" title="Hapus berkas">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                            </template>

                            <!-- Input file trigger if no file -->
                            <template x-if="!attachmentPreview">
                                <div class="flex flex-col items-center gap-1.5 py-1">
                                    <span class="material-symbols-outlined text-2xl text-slate-400">upload_file</span>
                                    <p class="text-xs text-slate-600 font-medium">Unggah bukti screenshot chat, foto kejadian, atau surat</p>
                                    <p class="text-[10px] text-slate-400">Format JPG, PNG, WEBP, PDF. Maksimal 5MB.</p>
                                    <button type="button" @click="$refs.attachmentInput.click()" class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-sm text-blue-600">add_photo_alternate</span>
                                        <span>Pilih Berkas Lampiran</span>
                                    </button>
                                </div>
                            </template>
                            <input type="file" x-ref="attachmentInput" @change="handleAttachment($event)" accept="image/png,image/jpeg,image/webp,application/pdf" class="hidden">
                        </div>
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
                                <input type="text" x-model="reporterClass" placeholder="Contoh: 7B / 8A" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-700">No. WhatsApp / Kontak</label>
                                <input type="text" x-model="reporterContact" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white rounded-lg text-xs border border-slate-200 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="submitting" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white rounded-xl font-bold text-xs sm:text-sm transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer mt-1">
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
                    <input type="text" x-model="searchCode" required placeholder="Masukkan nomor tiket (contoh: SR-2026-8042)" class="w-full pl-10 pr-4 py-3 bg-surface-container-low text-slate-800 rounded-xl text-xs sm:text-sm border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider">
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-medium">Tiket Terverifikasi:</span>
                        <span class="font-mono font-bold text-sm text-slate-900" x-text="'#' + ticketData?.code"></span>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          :class="{
                              'bg-amber-100 text-amber-800 border border-amber-200': ticketData?.status === 'belum_dibaca',
                              'bg-blue-100 text-blue-800 border border-blue-200': ticketData?.status === 'sedang_diproses',
                              'bg-emerald-100 text-emerald-800 border border-emerald-200': ticketData?.status === 'selesai',
                              'bg-slate-100 text-slate-700': ticketData?.status === 'iseng'
                          }">
                        <span class="w-1.5 h-1.5 rounded-full"
                              :class="{
                                  'bg-amber-600': ticketData?.status === 'belum_dibaca',
                                  'bg-blue-600 animate-pulse': ticketData?.status === 'sedang_diproses',
                                  'bg-emerald-600': ticketData?.status === 'selesai',
                                  'bg-slate-400': ticketData?.status === 'iseng'
                              }"></span>
                        <span x-text="ticketData?.status_label"></span>
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-slate-400 block">Kategori</span>
                        <span class="font-semibold text-slate-800 block truncate" x-text="ticketData?.category"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Lokasi</span>
                        <span class="font-semibold text-slate-800 block truncate" x-text="ticketData?.location"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Tingkat Urgensi</span>
                        <span class="font-semibold text-slate-800 block" x-text="ticketData?.urgency"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Tanggal Masuk</span>
                        <span class="font-semibold text-slate-800 block truncate" x-text="ticketData?.created_at"></span>
                    </div>
                </div>

                <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-col gap-1">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wide flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-blue-600">support_agent</span>
                        <span>Catatan Perkembangan Tim BK</span>
                    </span>
                    <p class="text-xs text-slate-600 leading-relaxed" x-text="ticketData?.notes || 'Tim Konselor telah mencatat laporanmu dan menjadwalkan pemantauan tertutup.'"></p>
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
                urgency: 'Sedang',
                reporterRole: 'Siswa (Korban Langsung)',
                incidentTime: 'Hari ini saat istirahat',
                story: '',
                isAnonymous: true,
                reporterName: '',
                reporterClass: '',
                reporterContact: '',

                // Attachment state
                attachmentFile: null,
                attachmentPreview: null,
                attachmentName: '',
                attachmentSize: '',
                attachmentIsImage: false,

                // Tracking tab
                searchCode: '',
                ticketData: null,

                handleAttachment(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran berkas maksimal 5MB.');
                        event.target.value = '';
                        return;
                    }

                    this.attachmentFile = file;
                    this.attachmentName = file.name;
                    this.attachmentSize = (file.size / 1024 < 1024) 
                        ? (file.size / 1024).toFixed(1) + ' KB' 
                        : (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                    this.attachmentIsImage = file.type.startsWith('image/');

                    if (this.attachmentIsImage) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.attachmentPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.attachmentPreview = 'file';
                    }
                },

                removeAttachment() {
                    this.attachmentFile = null;
                    this.attachmentPreview = null;
                    this.attachmentName = '';
                    this.attachmentSize = '';
                    this.attachmentIsImage = false;
                    if (this.$refs.attachmentInput) {
                        this.$refs.attachmentInput.value = '';
                    }
                },

                async submitReport() {
                    if (this.submitting) return;
                    this.submitting = true;
                    this.errorMessage = '';

                    try {
                        const formData = new FormData();
                        formData.append('category', this.category);
                        formData.append('location', this.location);
                        formData.append('urgency', this.urgency);
                        formData.append('reporter_role', this.reporterRole);
                        formData.append('incident_time', this.incidentTime);
                        formData.append('story', this.story);
                        formData.append('is_anonymous', this.isAnonymous ? '1' : '0');

                        if (!this.isAnonymous) {
                            formData.append('reporter_name', this.reporterName);
                            formData.append('reporter_class', this.reporterClass);
                            formData.append('reporter_contact', this.reporterContact);
                        }

                        if (this.attachmentFile) {
                            formData.append('attachment', this.attachmentFile);
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
                        alert('Nomor tiket ' + this.ticketCode + ' berhasil disalin!');
                    }).catch(() => {
                        prompt('Salin nomor tiket Anda:', this.ticketCode);
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
                    this.urgency = 'Sedang';
                    this.story = '';
                    this.isAnonymous = true;
                    this.reporterName = '';
                    this.reporterClass = '';
                    this.reporterContact = '';
                    this.removeAttachment();
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
                }
            };
        }
    </script>
</body>
</html>
