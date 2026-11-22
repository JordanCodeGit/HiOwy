<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <title>Ruang Belajar &amp; Pengumuman Siswa — HiOwy!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen">
    <header class="border-b border-slate-200/80 bg-white/95 backdrop-blur sticky top-0 z-30">
        <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-3 p-4 lg:px-6">
            <div class="flex items-center gap-3">
                @if(!empty($schoolConfig['school_logo_url']))
                    <img src="{{ $schoolConfig['school_logo_url'] }}" alt="{{ $schoolName ?? 'Sekolah' }}" class="w-8 h-8 rounded-lg object-contain border border-slate-200">
                @endif
                <x-brand />
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(!empty($schoolConfig['school_website_url']))
                    <a href="{{ $schoolConfig['school_website_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[15px]">language</span>
                        <span>Website Sekolah</span>
                    </a>
                @endif
                <a href="{{ route('public.report') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60 hover:bg-blue-100 transition-colors">
                    <span class="material-symbols-outlined text-[15px]">rate_review</span>
                    <span>Form Pengaduan</span>
                </a>
                <button type="button" onclick="quickExitHiOwy()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">logout</span>
                    <span>Keluar Cepat</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10 lg:px-6 space-y-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-secondary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">campaign</span>
                <span>Materi &amp; Pengumuman Resmi BK &bull; {{ $schoolName ?? 'SMP Telkom Purwokerto' }}</span>
            </p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Halaman Pelajari: Pencegahan &amp; Panduan Aman</h1>
            <p class="mt-2 text-sm text-slate-500 max-w-2xl leading-relaxed">
                Kenali pengertian dan bentuk perundungan serta langkah aman yang dapat diambil oleh korban, saksi, teman korban, maupun orang tua.
            </p>
        </div>

        <!-- Panduan Dasar Wajib Edukasi (FR-55) -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4" aria-label="Panduan Dasar Anti-Perundungan">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-2.5">
                <div class="flex items-center gap-2 text-blue-700 font-bold text-sm">
                    <span class="material-symbols-outlined text-lg">menu_book</span>
                    <h2>Pengertian &amp; Bentuk Perundungan</h2>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Merujuk Permendikbudristek No. 46/2023, perundungan adalah perlakuan menyakitkan secara berulang yang menimbulkan rasa tidak aman atau tekanan psikis, meliputi:
                </p>
                <ul class="text-xs text-slate-700 space-y-1 list-disc list-inside">
                    <li><strong>Fisik:</strong> memukul, mendorong, menendang, atau merusak barang.</li>
                    <li><strong>Verbal / Psikis:</strong> mengejek, menghina, mengancam, atau memberi julukan buruk.</li>
                    <li><strong>Pengucilan Sosial:</strong> sengaja mengisolasi atau memutus pertemanan.</li>
                    <li><strong>Daring (Cyberbullying):</strong> teror, komentar merendahkan, atau penyebaran aib di medsos/chat.</li>
                    <li><strong>Perilaku Seksual / Tidak Pantas:</strong> sentuhan, ucapan, atau kiriman bernuansa seksual tanpa persetujuan.</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-2.5">
                <div class="flex items-center gap-2 text-emerald-700 font-bold text-sm">
                    <span class="material-symbols-outlined text-lg">shield</span>
                    <h2>Langkah yang Dapat Diambil</h2>
                </div>
                <ul class="text-xs text-slate-700 space-y-1.5 list-disc list-inside leading-relaxed">
                    <li>Simpan bukti (catatan waktu/tempat kejadian atau tangkapan layar pesan).</li>
                    <li>Menjauh menuju tempat yang ramai, ruang guru, atau ruang BK saat merasa terancam.</li>
                    <li>Laporkan melalui kode QR HiOwy atau sampaikan langsung kepada Guru BK pada jam layanan sekolah.</li>
                    <li>Gunakan kontak darurat sekolah apabila terjadi ancaman keselamatan fisik mendesak.</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-sm">
                    <span class="material-symbols-outlined text-lg">person_raised_hand</span>
                    <h2>Bagi Korban</h2>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Kamu tidak bersalah atas perlakuan buruk yang menimpamu. Jangan memendam sendiri—kamu bisa melapor secara anonim maupun meminta dihubungi Guru BK, serta memantau balasan resmi konselor lewat Kode Tiket 10 karaktermu.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-amber-700 font-bold text-sm">
                    <span class="material-symbols-outlined text-lg">diversity_3</span>
                    <h2>Bagi Saksi, Teman Korban &amp; Orang Tua</h2>
                </div>
                <ul class="text-xs text-slate-600 space-y-1 leading-relaxed list-disc list-inside">
                    <li><strong>Saksi &amp; Teman Korban:</strong> temani korban agar tidak sendirian, hentikan penyebaran gosip/rekaman, dan laporkan kejadian lewat HiOwy.</li>
                    <li><strong>Orang Tua / Wali:</strong> dengarkan anak dengan tenang tanpa menyalahkan, lalu koordinasikan bersama Guru BK atau laporkan melalui peran Orang Tua/Wali.</li>
                </ul>
            </div>
        </section>

        <!-- Pengumuman & Materi Dinamis dari Guru BK (FR-56) -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Pengumuman &amp; Materi Terbaru dari Tim BK</h2>
            </div>

            @forelse($materials as $material)
                @php
                    $audienceLabel = method_exists($material, 'audienceLabel')
                        ? $material->audienceLabel()
                        : ($material->audience === 'orang_tua' ? 'Orang Tua & Wali' : ($material->audience === 'umum' ? 'Umum & Satgas TPPK' : 'Siswa & Remaja'));

                    $badgeClass = match($material->audience ?? 'siswa') {
                        'orang_tua' => 'bg-amber-100 text-amber-800',
                        'umum'      => 'bg-rose-100 text-rose-800',
                        default     => 'bg-blue-100 text-blue-800',
                    };
                    $date = $material->updated_at ? \Carbon\Carbon::parse($material->updated_at)->translatedFormat('d F Y') : 'Terbaru';
                @endphp
                <article class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 lg:p-8 shadow-xs hover:shadow-md transition-all space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $audienceLabel }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">{{ $material->code }}</span>
                        </div>
                        <span class="text-xs text-slate-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                            <span>{{ $date }}</span>
                        </span>
                    </div>

                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold hover:text-secondary transition-colors">
                        {{ $material->title }}
                    </h3>

                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed whitespace-pre-wrap">{{ $material->content }}</p>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-base text-blue-600">verified_user</span>
                            <span>Diterbitkan oleh: {{ $material->author_name ?? 'Tim Konselor BK' }}</span>
                        </span>
                    </div>
                </article>
            @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-4xl text-slate-300">auto_stories</span>
                    <h3 class="text-sm font-bold text-slate-700 mt-2">Belum Ada Pengumuman Tambahan dari Guru BK</h3>
                    <p class="text-xs text-slate-400 mt-1">Panduan dasar pencegahan perundungan di atas tetap dapat dibaca kapan saja.</p>
                </div>
            @endforelse
        </section>

        <div class="p-6 rounded-2xl bg-blue-50/80 border border-blue-100 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Jam Baca &amp; Saluran Bantuan BK</h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        {{ $schoolConfig['reading_hours'] ?? 'Senin–Jumat, 07.00–15.00 WIB' }}
                        @if(!empty($schoolConfig['emergency_contact']))
                            &bull; Kontak Darurat BK:
                            <a href="{{ $schoolConfig['emergency_wa_url'] ?: $schoolConfig['emergency_tel_url'] }}" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-700 underline">{{ $schoolConfig['emergency_contact'] }}</a>
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('public.report') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shrink-0 transition-colors shadow-xs">
                Kirim Aduan / Konseling
            </a>
        </div>
    </main>

    <script>
        function quickExitHiOwy() {
            try {
                sessionStorage.setItem('hiowy_exited', '1');
                window.history.replaceState(null, '', '/');
            } catch (e) {}
            window.location.replace('https://www.google.com');
        }
        window.addEventListener('pageshow', (event) => {
            const nav = performance.getEntriesByType('navigation')[0];
            if ((event.persisted || nav?.type === 'back_forward') && sessionStorage.getItem('hiowy_exited') === '1') {
                window.location.replace('https://www.google.com');
            } else {
                try { sessionStorage.removeItem('hiowy_exited'); } catch (e) {}
            }
        });
    </script>
</body>
</html>
