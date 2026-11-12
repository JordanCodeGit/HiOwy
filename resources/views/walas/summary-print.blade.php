<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposisi Konseling Kelas #{{ $summary['id'] }} — {{ $schoolName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-page { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-body-md min-h-screen py-8 px-4 antialiased">
    <!-- Top Action Bar -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ route('walas.summary', $summary['id']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Ringkasan</span>
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Sheet -->
    <div class="print-page max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-200 p-8 sm:p-12">
        <!-- Header -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-2xl tracking-tighter shadow-sm">
                    Hi!
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $schoolName }}</h1>
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mt-0.5">Unit Layanan Bimbingan & Konseling (BK)</p>
                    <p class="text-[11px] text-slate-500">Sistem Perlindungan Siswa & Disposisi Terpadu — HiOwy!</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700 border border-red-200">
                    Sangat Rahasia
                </span>
                <p class="text-[11px] text-slate-500 font-mono mt-1">ID: DSP-{{ str_pad($summary['id'], 4, '0', STR_PAD_LEFT) }}-{{ strtoupper($className) }}</p>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6">
            <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wide">Lembar Disposisi Pendampingan Siswa</h2>
            <p class="text-xs text-slate-500 mt-1">Catatan rekomendasi observasi tertutup untuk Wali Kelas {{ $className }}</p>
        </div>

        <!-- Metadata Table -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 rounded-xl p-4 border border-slate-200 text-xs mb-6">
            <div>
                <span class="text-slate-400 block font-medium">Konselor BK Pengirim:</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $summary['counselor'] }}</span>
                <span class="text-slate-500">{{ $summary['role'] }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Target Pendampingan:</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">Kelas {{ $className }}</span>
                <span class="text-slate-500">Tahun Ajaran 2024/2025</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Waktu Disposisi Diteruskan:</span>
                <span class="font-semibold text-slate-800 mt-0.5 block">{{ $summary['time'] }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Status Konfirmasi Wali Kelas:</span>
                <span class="font-semibold text-slate-800 mt-0.5 block">
                    {{ $summary['status'] === 'read' ? 'Sudah Ditelaah (' . ($summary['read_at'] ?? 'Aktif') . ')' : 'Menunggu Telaah' }}
                </span>
            </div>
        </div>

        <!-- Disposition Content -->
        <div class="mb-8">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Isi Catatan Rekomendasi Observasi
            </h3>
            <div class="bg-blue-50/40 border-l-4 border-blue-600 p-4 rounded-r-xl text-slate-800 text-sm leading-relaxed">
                {{ $summary['detail'] }}
            </div>
        </div>

        <!-- Action Items -->
        <div class="mb-8">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                Protokol Observasi Wali Kelas
            </h3>
            <ul class="list-disc list-inside text-xs text-slate-700 space-y-1.5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <li>Lakukan pemantauan secara wajar tanpa memicu atensi berlebih dari rekan sekelas.</li>
                <li>Jaga kerahasiaan identitas saksi maupun pelapor dari forum terbuka kelas.</li>
                <li>Berikan pendekatan suportif serta apresiasi wajar saat dinamika pembelajaran berlangsung.</li>
                <li>Segera koordinasikan dengan Guru BK jika ditemukan eskalasi situasi yang memerlukan mediasi khusus.</li>
            </ul>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 gap-8 pt-8 mt-8 border-t border-slate-200 text-xs">
            <div class="text-center">
                <p class="text-slate-500 mb-16">Guru Bimbingan Konseling,</p>
                <p class="font-bold text-slate-900 underline">{{ $summary['counselor'] }}</p>
                <p class="text-slate-400">{{ $summary['role'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-500 mb-16">Wali Kelas {{ $className }},</p>
                <p class="font-bold text-slate-900 underline">{{ Auth::user()?->name ?? 'Wali Kelas ' . $className }}</p>
                <p class="text-slate-400">NIP. — / ID Dinas</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span>Dicetak secara terenkripsi melalui Portal HiOwy!</span>
            <span>Halaman 1 dari 1</span>
        </div>
    </div>
</body>
</html>
