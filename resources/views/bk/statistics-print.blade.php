<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Statistik Agregat — {{ $schoolName }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; background: #f8fafc; margin: 0; padding: 24px; }
        .no-print { max-width: 900px; margin: 0 auto 16px auto; display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 12px 18px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .btn { padding: 8px 16px; border-radius: 8px; border: none; background: #006398; color: #fff; font-weight: 600; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-sec { background: #e2e8f0; color: #1e293b; }
        .sheet { max-width: 900px; margin: 0 auto; background: #fff; padding: 28px 32px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,.07); }
        h1 { font-size: 20px; margin: 0 0 4px 0; }
        .meta { font-size: 12px; color: #64748b; margin-bottom: 18px; }
        .notice { font-size: 11px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; margin-bottom: 18px; }
        .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .kpi { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; }
        .kpi small { font-size: 10px; text-transform: uppercase; color: #64748b; display: block; }
        .kpi strong { font-size: 17px; display: block; margin-top: 4px; }
        h2 { font-size: 14px; margin: 18px 0 8px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 10px; }
        th, td { text-align: left; padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; font-weight: 700; color: #475569; }
        @media print { body { background: #fff; padding: 0; } .no-print { display: none !important; } .sheet { box-shadow: none; padding: 12mm; } }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="{{ route('bk.statistics', ['month' => $selectedMonth]) }}" class="btn btn-sec">Kembali ke Statistik</a>
        <button type="button" onclick="window.print()" class="btn">Cetak / Simpan PDF</button>
    </div>
    <div class="sheet">
        <h1>Laporan Statistik Agregat HiOwy — {{ $schoolName }}</h1>
        <p class="meta">Periode: {{ $selectedMonth ? ($stats['available_months'][$selectedMonth] ?? $selectedMonth) : 'Semua Bulan' }} &bull; Dicetak {{ now('Asia/Jakarta')->translatedFormat('d F Y') }}</p>
        <div class="notice">
            <strong>Kepatuhan Privasi (FR-60 &amp; FR-61):</strong> Dokumen ini tidak memuat isi kronologi maupun identitas siswa. Kelompok berjumlah 1–4 ditampilkan sebagai &ldquo;kurang dari 5&rdquo;.
        </div>
        <div class="kpis">
            <div class="kpi"><small>Total Laporan</small><strong>{{ $stats['total_display'] }}</strong></div>
            <div class="kpi"><small>Laporan Aktif</small><strong>{{ $stats['active_display'] }}</strong></div>
            <div class="kpi"><small>Tingkat Selesai</small><strong>{{ $stats['rate'] }}% ({{ $stats['completed_display'] }})</strong></div>
            <div class="kpi"><small>Rata-rata Waktu Dibaca</small><strong>{{ $stats['avg_read_label'] }}</strong></div>
        </div>
        @foreach([
            'Tren Bulanan' => $stats['months'],
            'Distribusi Jenis Kejadian' => $stats['by_category'],
            'Distribusi Peran Pelapor' => $stats['by_role'],
            'Distribusi Tempat Kejadian' => $stats['by_location'],
            'Distribusi Tingkat Urgensi' => $stats['by_urgency'],
            'Distribusi Status Penanganan' => $stats['by_status'],
        ] as $title => $rows)
        <h2>{{ $title }}</h2>
        <table>
            <thead><tr><th>Kelompok</th><th>Jumlah</th><th>Persentase</th></tr></thead>
            <tbody>
                @forelse($rows as $row)
                <tr><td>{{ $row['label'] }}</td><td>{{ $row['display'] }}</td><td>{{ $row['pct'] }}%</td></tr>
                @empty
                <tr><td colspan="3">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endforeach
    </div>
</body>
</html>
