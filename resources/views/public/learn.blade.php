<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Belajar &amp; Pengumuman Siswa — HiOwy!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen">
    <header class="border-b border-slate-200/80 bg-white/95 backdrop-blur sticky top-0 z-30">
        <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-4 p-4 lg:px-6">
            <x-brand />
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                    <span class="material-symbols-outlined text-[15px]">school</span>
                    <span>Portal Siswa &amp; Komunitas</span>
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10 lg:px-6 space-y-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-secondary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">campaign</span>
                <span>Materi &amp; Pengumuman Resmi BK</span>
            </p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Ruang Edukasi &amp; Ruang Cerita Siswa</h1>
            <p class="mt-2 text-sm text-slate-500 max-w-2xl leading-relaxed">
                Informasi pencegahan kekerasan, panduan saling jaga pertemanan, dan akses saluran bantuan terverifikasi dari Tim Bimbingan Konseling.
            </p>
        </div>

        <div class="space-y-5">
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

                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold hover:text-secondary transition-colors">
                        {{ $material->title }}
                    </h2>

                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed whitespace-pre-wrap">
                        {{ $material->content }}
                    </p>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-base text-blue-600">verified_user</span>
                            <span>Diterbitkan oleh: {{ $material->author_name ?? 'Tim Konselor BK' }}</span>
                        </span>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-5xl text-slate-300">auto_stories</span>
                    <h3 class="text-base font-bold text-slate-700 mt-2">Belum Ada Materi Diterbitkan</h3>
                    <p class="text-xs text-slate-400 mt-1">Kembali lagi nanti untuk pengumuman dan materi edukasi terbaru dari sekolah.</p>
                </div>
            @endforelse
        </div>

        <div class="p-6 rounded-2xl bg-blue-50/80 border border-blue-100 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Butuh Teman Bercerita atau Ruang Konseling?</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Segala kerahasiaan identitas dan isi pembicaraan dilindungi kode etik konselor.</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shrink-0 transition-colors shadow-xs">
                Hubungi Konselor
            </a>
        </div>
    </main>
</body>
</html>
