<?php

namespace App\Support;

use Illuminate\Support\Collection;

class KesiswaanPreview
{
    public const SCHOOL = 'preview-school';

    private const TYPES = [
        'fisik' => ['label' => 'Fisik', 'visible_to_kesiswaan' => true],
        'verbal' => ['label' => 'Ejekan & Verbal', 'visible_to_kesiswaan' => true],
        'dikucilkan' => ['label' => 'Pengucilan', 'visible_to_kesiswaan' => true],
        'cyber' => ['label' => 'Cyberbullying', 'visible_to_kesiswaan' => true],
        'pelecehan' => ['label' => 'Pelecehan', 'visible_to_kesiswaan' => true],
        'pribadi' => ['label' => 'Masalah Pribadi', 'visible_to_kesiswaan' => false],
        'lainnya' => ['label' => 'Lainnya', 'visible_to_kesiswaan' => false],
    ];

    private const REPORTS = [];

    /** Source-design fixtures only; replace with authorized school-scoped queries before integration. */
    public function visibleReports(): Collection
    {
        return collect(self::REPORTS)->filter(fn (array $report): bool => $report['school_id'] === self::SCHOOL
            && ((self::TYPES[$report['type']]['visible_to_kesiswaan'] ?? false) || $report['shared_by_bk'])
        )->values();
    }

    public function findVisible(int $id): array
    {
        return $this->visibleReports()->firstWhere('id', $id) ?? abort(404);
    }

    public function summary(): array
    {
        $active = $this->visibleReports()->where('archived', false);

        return [
            'unread' => $active->where('status', 'belum_dibaca')->count(),
            'handling' => $active->whereIn('status', ['dibaca', 'ditindaklanjuti'])->count(),
            'completed' => $active->where('status', 'selesai')->count(),
            'danger' => $active->where('status', 'belum_dibaca')->where('danger_flag', true)->count(),
            'overdue' => $active->where('date', '<', '2024-10-21')->count(),
        ];
    }

    public function types(): array
    {
        $shared = $this->visibleReports()->where('shared_by_bk', true)->pluck('type_label', 'type')->all();

        return collect(self::TYPES)->where('visible_to_kesiswaan', true)
            ->map(fn (array $type): string => $type['label'])->all() + $shared;
    }

    public function accessLogs(): Collection
    {
        $visibleTickets = $this->visibleReports()->pluck('ticket');

        return collect([['school_id' => 'preview-school',
            'time' => '24 Okt 2024 10:28:14 WIB',
            'user' => 'Sarah Jenkins, M.Pd. Konselor Inti',
            'action' => 'Membuka Detail Laporan',
            'ticket' => 'SR-2024-8042',
            'device' => '192.168.10.42 Chrome 130 • macOS Tahoe'],
            ['school_id' => 'preview-school',
                'time' => '24 Okt 2024 10:14:02 WIB',
                'user' => 'Sarah Jenkins, M.Pd. Konselor Inti',
                'action' => 'Memperbarui Status Kasus',
                'ticket' => 'SR-2024-8041',
                'device' => '192.168.10.42 Chrome 130 • macOS Tahoe'],
            ['school_id' => 'preview-school',
                'time' => '24 Okt 2024 09:47:33 WIB',
                'user' => 'Budi Setiawan, S.Pd. Tim Kesiswaan',
                'action' => 'Meneruskan Ringkasan Netral',
                'ticket' => 'SR-2024-8039',
                'device' => '192.168.10.18 Edge 129 • Windows 11'],
            ['school_id' => 'preview-school',
                'time' => '24 Okt 2024 08:15:10 WIB',
                'user' => 'Sarah Jenkins, M.Pd. Konselor Inti',
                'action' => 'Mengubah Label Urgensi',
                'ticket' => 'SR-2024-8035',
                'device' => '192.168.10.42 Chrome 130 • macOS Tahoe'],
            ['school_id' => 'preview-school',
                'time' => '24 Okt 2024 07:55:40 WIB',
                'user' => 'Sarah Jenkins, M.Pd. Konselor Inti',
                'action' => 'Login Terverifikasi 2FA',
                'ticket' => null,
                'device' => '192.168.10.42 Chrome 130 • macOS Tahoe'],
            ['school_id' => 'preview-school',
                'time' => '23 Okt 2024 23:00:01 WIB',
                'user' => 'SysAdmin Gateway Otomasi Sistem',
                'action' => 'Rotasi Kunci Sesi Harian',
                'ticket' => null,
                'device' => '10.0.0.1 (Internal) Daemon Cron v2.4'],
            ['school_id' => 'preview-school',
                'time' => '23 Okt 2024 15:42:19 WIB',
                'user' => 'Dr. H. Rusdianto, M.M. Kepala Sekolah',
                'action' => 'Melihat Agregat Statistik Sekolah',
                'ticket' => null,
                'device' => '192.168.10.5 Safari 17 • iPadOS 17.6'],
            ['school_id' => 'preview-school',
                'time' => '23 Okt 2024 11:05:44 WIB',
                'user' => 'Sarah Jenkins, M.Pd. Konselor Inti',
                'action' => 'Membuka Detail Laporan',
                'ticket' => 'SR-2024-8022',
                'device' => '192.168.10.42 Chrome 130 • macOS Tahoe']])->where('school_id', self::SCHOOL)
            ->map(function (array $log) use ($visibleTickets): array {
                $log['ticket'] = $log['ticket'] === null ? '-' : ($visibleTickets->contains($log['ticket']) ? $log['ticket'] : 'Laporan lain');

                return $log;
            })->values();
    }

    public function statistics(): array
    {
        $reports = $this->visibleReports();
        $total = $reports->count();
        $types = $reports->countBy('type')->all();
        arsort($types);

        return [
            'total' => $total,
            'completed' => $reports->where('status', 'selesai')->count(),
            'active' => $reports->where('archived', false)->count(),
            'rate' => $total ? round($reports->where('status', 'selesai')->count() / $total * 100) : 0,
            'types' => $types,
            'statuses' => $reports->countBy('status')->all(),
            'months' => $reports->countBy(fn (array $report): string => substr($report['date'], 0, 7))->sortKeys()->all(),
        ];
    }
}
