<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengaduan Siswa — {{ $schoolName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col justify-between font-sans">

    <header class="w-full bg-surface-container-lowest border-b border-surface-container py-4 px-6 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center font-bold text-lg shadow-xs">
                    <span class="material-symbols-outlined text-[24px]">school</span>
                </div>
                <div>
                    <h1 class="font-bold text-on-surface text-base sm:text-lg leading-tight">{{ $schoolName }}</h1>
                    <p class="text-xs text-on-surface-variant">Layanan Pendampingan Siswa &amp; Ruang Aman BK</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Kanal Terenkripsi</span>
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-2xl w-full mx-auto p-4 sm:p-6 my-6 flex-1" x-data="{ submitted: false, isAnonymous: true, category: '', location: '', story: '' }">
        <div x-show="!submitted" class="bg-surface-container-lowest rounded-2xl border border-surface-container/80 shadow-sm p-6 sm:p-8 flex flex-col gap-6">
            <div>
                <div class="flex items-center gap-2 text-secondary font-semibold text-xs uppercase tracking-wider mb-1.5">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span>Ruang Dialog Bebas Tekanan</span>
                </div>
                <h2 class="text-2xl font-bold text-on-surface tracking-tight">Kirim Aduan atau Ceritamu</h2>
                <p class="text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                    Setiap kata yang kamu tuliskan di sini didengarkan dengan empati oleh Guru BK. Identitasmu sepenuhnya dilindungi.
                </p>
            </div>

            @if ($fromQr)
            <div class="p-3.5 bg-secondary-fixed/30 border border-secondary/20 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary text-[22px]">qr_code_scanner</span>
                <p class="text-xs text-on-secondary-fixed-variant leading-snug">
                    Kamu terhubung melalui pemindaian <strong>Kode QR Resmi Sekolah</strong>.
                </p>
            </div>
            @endif

            <form @submit.prevent="submitted = true" class="flex flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Jenis Kejadian / Masalah <span class="text-error">*</span></label>
                    <select x-model="category" required class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                        <option value="">-- Pilih Jenis Kejadian --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->label }}">{{ $cat->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Titik Lokasi Kejadian <span class="text-error">*</span></label>
                    <select x-model="location" required class="w-full px-4 py-3 bg-surface-container-low text-on-surface rounded-xl text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none">
                        <option value="">-- Pilih Lokasi Kejadian --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->label }}">{{ $loc->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-on-surface uppercase tracking-wider">Ceritakan Apa yang Terjadi <span class="text-error">*</span></label>
                    <textarea x-model="story" required rows="5" placeholder="Tuliskan dengan tenang apa yang kamu rasakan atau saksikan..." class="w-full p-4 bg-surface-container-low text-on-surface rounded-xl text-sm border border-outline-variant/40 focus:ring-2 focus:ring-secondary focus:outline-none leading-relaxed"></textarea>
                </div>

                <div class="p-4 bg-surface-container-low/70 rounded-xl border border-surface-container flex items-center justify-between gap-4">
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-on-surface">Kirim Sebagai Anonim</span>
                        <span class="text-xs text-on-surface-variant">Nama dan kelasmu tidak akan ditampilkan kepada siapapun.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" x-model="isAnonymous" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-primary hover:bg-inverse-surface text-on-primary rounded-xl font-bold text-sm transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer mt-2">
                    <span class="material-symbols-outlined text-[20px]">send</span>
                    <span>Kirimkan Aduan Sekarang</span>
                </button>
            </form>
        </div>

        <div x-cloak x-show="submitted" class="bg-surface-container-lowest rounded-2xl border border-surface-container p-8 text-center flex flex-col items-center gap-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs">
                <span class="material-symbols-outlined text-[36px]">check_circle</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface">Aduanmu Telah Diterima Guru BK</h3>
            <p class="text-sm text-on-surface-variant max-w-md leading-relaxed">
                Terima kasih sudah berani bersuara. Tim Konselor {{ $schoolName }} akan segera membaca dan menindaklanjuti laporanmu dengan perlindungan penuh.
            </p>
            <div class="p-3 bg-surface-container-low rounded-xl text-xs text-on-surface-variant font-mono mt-2">
                ID Tiket Aduan: #ADU-{{ strtoupper(substr(md5(time()), 0, 8)) }}
            </div>
            <button type="button" @click="submitted = false; story = ''" class="mt-4 px-6 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-sm transition-colors cursor-pointer">
                Kirim Aduan Lain
            </button>
        </div>
    </main>

    <footer class="w-full py-4 px-6 text-center text-xs text-on-surface-variant border-t border-surface-container">
        &copy; {{ date('Y') }} {{ $schoolName }} • Portal Pengaduan Siswa Terproteksi HiOwy!
    </footer>

</body>
</html>
