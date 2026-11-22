<?php

namespace App\Support;

use App\Models\RetentionSummary;
use App\Models\StudentReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class StatisticsService
{
    /**
     * Penyamaran angka kecil 1..4 menjadi "kurang dari 5" (FR-60).
     */
    public static function maskCount(int $count): string
    {
        if ($count > 0 && $count < 5) {
            return 'kurang dari 5';
        }

        return (string) $count;
    }

    /**
     * Bangun struktur statistik teragregasi bulanan tanpa PII atau isi cerita (FR-59, FR-60, FR-61).
     *
     * @param  Collection<int, StudentReport>  $reports
     * @return array<string, mixed>
     */
    public static function buildFromReports(
        Collection $reports,
        ?string $selectedMonth = null,
        bool $includeRetention = true,
    ): array {
        $availableMonths = $reports
            ->filter(fn (StudentReport $r) => $r->submitted_date !== null)
            ->map(fn (StudentReport $r) => $r->submitted_date->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        $retentionRows = ($includeRetention && Schema::hasTable('retention_summaries'))
            ? RetentionSummary::all()
            : collect([]);

        foreach ($retentionRows as $ret) {
            foreach (array_keys($ret->summary_payload['months'] ?? []) as $m) {
                if (! $availableMonths->contains($m)) {
                    $availableMonths->push($m);
                }
            }
        }
        $availableMonths = $availableMonths->unique()->sortDesc()->values();

        $filtered = $selectedMonth
            ? $reports->filter(fn (StudentReport $r) => $r->submitted_date?->format('Y-m') === $selectedMonth)->values()
            : $reports->values();

        $byCategoryRaw = $filtered->groupBy(fn (StudentReport $r) => $r->category ?: 'Lainnya')->map->count()->all();
        $byRoleRaw = $filtered->groupBy(fn (StudentReport $r) => $r->reporter_role ?: 'Siswa')->map->count()->all();
        $byLocationRaw = $filtered->groupBy(fn (StudentReport $r) => $r->location ?: 'Lainnya')->map->count()->all();
        $byUrgencyRaw = [
            'Darurat' => $filtered->filter(fn (StudentReport $r) => $r->urgency_normalized === 'urgent')->count(),
            'Sedang / Penting' => $filtered->filter(fn (StudentReport $r) => $r->urgency_normalized === 'medium')->count(),
            'Rendah / Konseling' => $filtered->filter(fn (StudentReport $r) => $r->urgency_normalized === 'easy')->count(),
        ];
        $byStatusRaw = [
            'belum_dibaca' => $filtered->where('status', 'belum_dibaca')->count(),
            'dibaca' => $filtered->where('status', 'dibaca')->count(),
            'ditindaklanjuti' => $filtered->where('status', 'ditindaklanjuti')->count(),
            'selesai' => $filtered->where('status', 'selesai')->count(),
            'iseng' => $filtered->where('archive_reason', 'iseng')->count(),
        ];

        $monthsRaw = $reports
            ->filter(fn (StudentReport $r) => $r->submitted_date !== null)
            ->groupBy(fn (StudentReport $r) => $r->submitted_date->format('Y-m'))
            ->map->count()
            ->all();

        $archivedCompletedFromRetention = 0;
        if ($includeRetention) {
            foreach ($retentionRows as $ret) {
                $payload = $ret->summary_payload ?? [];
                foreach (($payload['months'] ?? []) as $mKey => $mCount) {
                    $monthsRaw[$mKey] = ($monthsRaw[$mKey] ?? 0) + (int) $mCount;
                }

                if ($selectedMonth && empty($payload['months'][$selectedMonth])) {
                    continue;
                }

                $scale = $selectedMonth ? ($payload['by_month'][$selectedMonth] ?? []) : $payload;
                foreach (($scale['by_category'] ?? []) as $k => $v) {
                    $byCategoryRaw[$k] = ($byCategoryRaw[$k] ?? 0) + (int) $v;
                }
                foreach (($scale['by_role'] ?? []) as $k => $v) {
                    $byRoleRaw[$k] = ($byRoleRaw[$k] ?? 0) + (int) $v;
                }
                foreach (($scale['by_location'] ?? []) as $k => $v) {
                    $byLocationRaw[$k] = ($byLocationRaw[$k] ?? 0) + (int) $v;
                }
                foreach (($scale['by_urgency'] ?? []) as $k => $v) {
                    $byUrgencyRaw[$k] = ($byUrgencyRaw[$k] ?? 0) + (int) $v;
                }
                foreach (($scale['by_status'] ?? []) as $k => $v) {
                    if (array_key_exists($k, $byStatusRaw)) {
                        $byStatusRaw[$k] += (int) $v;
                    }
                }
                $archivedCompletedFromRetention += (int) ($scale['completed'] ?? 0);
            }
        }

        arsort($byCategoryRaw);
        arsort($byRoleRaw);
        arsort($byLocationRaw);
        krsort($monthsRaw);

        $total = $filtered->count() + ($includeRetention ? self::countRetentionTotal($retentionRows, $selectedMonth) : 0);
        $active = $filtered->whereNull('archived_at')->count();
        $completed = $filtered->filter(fn (StudentReport $r) => $r->status === 'selesai' || $r->archive_reason === 'selesai')->count()
            + $archivedCompletedFromRetention;
        $rate = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        $unread = $filtered->whereNull('archived_at')->where('status', 'belum_dibaca')->count();

        $avgReadHours = self::computeAverageReadHours($filtered);

        return [
            'total' => $total,
            'total_display' => self::maskCount($total),
            'active' => $active,
            'active_display' => self::maskCount($active),
            'completed' => $completed,
            'completed_display' => self::maskCount($completed),
            'rate' => $rate,
            'unread' => $unread,
            'unread_display' => self::maskCount($unread),
            'avg_read_hours' => $avgReadHours,
            'avg_read_label' => self::formatReadDuration($avgReadHours),
            'selected_month' => $selectedMonth,
            'available_months' => $availableMonths->mapWithKeys(fn (string $m) => [
                $m => Carbon::createFromFormat('Y-m', $m)->translatedFormat('F Y'),
            ])->all(),
            'months' => self::formatBuckets($monthsRaw, $total, fn (string $k) => Carbon::createFromFormat('Y-m', $k)->translatedFormat('M Y')),
            'by_category' => self::formatBuckets($byCategoryRaw, $total),
            'by_role' => self::formatBuckets($byRoleRaw, $total),
            'by_location' => self::formatBuckets($byLocationRaw, $total),
            'by_urgency' => self::formatBuckets($byUrgencyRaw, $total),
            'by_status' => self::formatBuckets($byStatusRaw, $total),
        ];
    }

    /**
     * Estimasi rata-rata waktu sampai laporan dibaca (jam) berbasis daypart + tanggal masuk vs bk_read_at (FR-14 + FR-59).
     */
    private static function computeAverageReadHours(Collection $reports): ?float
    {
        $daypartHours = [
            'pagi' => '09:00:00',
            'siang' => '13:00:00',
            'sore' => '16:30:00',
            'malam' => '20:00:00',
        ];

        $durations = [];
        foreach ($reports as $report) {
            if (! $report->bk_read_at || ! $report->submitted_date) {
                continue;
            }
            $daypartKey = strtolower((string) ($report->submitted_daypart ?: 'pagi'));
            $approxTime = $daypartHours[$daypartKey] ?? '09:00:00';
            $submittedApprox = Carbon::parse($report->submitted_date->format('Y-m-d').' '.$approxTime, 'Asia/Jakarta');
            $readAt = $report->bk_read_at->copy()->timezone('Asia/Jakarta');

            $hours = max(0.0, round($submittedApprox->diffInMinutes($readAt, false) / 60, 1));
            $durations[] = $hours;
        }

        if ($durations === []) {
            return null;
        }

        return round(array_sum($durations) / count($durations), 1);
    }

    private static function formatReadDuration(?float $hours): string
    {
        if ($hours === null) {
            return 'Belum ada data baca';
        }
        if ($hours < 1.0) {
            return '< 1 jam';
        }
        if ($hours >= 24.0) {
            $days = round($hours / 24, 1);

            return str_replace('.', ',', (string) $days).' hari';
        }

        return str_replace('.', ',', (string) $hours).' jam';
    }

    /**
     * @param  array<string, int>  $raw
     * @return array<string, array{label: string, count: int, display: string, pct: int}>
     */
    private static function formatBuckets(array $raw, int $total, ?callable $labelResolver = null): array
    {
        $out = [];
        foreach ($raw as $key => $cnt) {
            $count = (int) $cnt;
            $label = $labelResolver ? $labelResolver((string) $key) : (string) $key;
            $out[$key] = [
                'label' => $label,
                'count' => $count,
                'display' => self::maskCount($count),
                'pct' => $total > 0 ? (int) round(($count / $total) * 100) : 0,
            ];
        }

        return $out;
    }

    private static function countRetentionTotal(Collection $retentionRows, ?string $selectedMonth): int
    {
        $sum = 0;
        foreach ($retentionRows as $ret) {
            $payload = $ret->summary_payload ?? [];
            if ($selectedMonth) {
                $sum += (int) ($payload['months'][$selectedMonth] ?? 0);
            } else {
                $sum += (int) $ret->deleted_reports_count;
            }
        }

        return $sum;
    }
}
