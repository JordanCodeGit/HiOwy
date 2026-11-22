<?php

namespace App\Support;

use App\Models\RetentionSummary;
use App\Models\SchoolSetting;
use App\Models\StudentReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RetentionService
{
    /**
     * Jalankan retensi akhir tahun ajaran terhadap laporan arsip/selesai sambil membiarkan laporan terbuka tetap aman (FR-65).
     *
     * @return array{executed: bool, skipped_reason: ?string, deleted_count: int, retention_date: ?string}
     */
    public static function run(?string $effectiveDate = null, ?User $actor = null): array
    {
        $configuredDate = trim((string) SchoolSetting::get('retention_date', ''));
        if ($configuredDate === '') {
            return [
                'executed' => false,
                'skipped_reason' => 'Tanggal akhir tahun ajaran / retensi belum diatur pada Konfigurasi Sekolah.',
                'deleted_count' => 0,
                'retention_date' => null,
            ];
        }

        $checkDate = $effectiveDate
            ? Carbon::parse($effectiveDate, 'Asia/Jakarta')->toDateString()
            : now('Asia/Jakarta')->toDateString();

        if ($checkDate < $configuredDate) {
            return [
                'executed' => false,
                'skipped_reason' => "Belum mencapai tanggal retensi ({$configuredDate}).",
                'deleted_count' => 0,
                'retention_date' => $configuredDate,
            ];
        }

        $targets = StudentReport::production()
            ->where(function ($q) {
                $q->whereNotNull('archived_at')
                    ->orWhere('status', 'selesai');
            })
            ->get();

        if ($targets->isEmpty()) {
            return [
                'executed' => true,
                'skipped_reason' => null,
                'deleted_count' => 0,
                'retention_date' => $configuredDate,
            ];
        }

        $months = [];
        $byMonth = [];
        $byCategory = [];
        $byRole = [];
        $byLocation = [];
        $byUrgency = [];
        $byStatus = [];
        $completed = 0;

        foreach ($targets as $r) {
            $monthKey = $r->submitted_date?->format('Y-m') ?: now('Asia/Jakarta')->format('Y-m');
            $cat = $r->category ?: 'Lainnya';
            $role = $r->reporter_role ?: 'Siswa';
            $loc = $r->location ?: 'Lainnya';
            $urg = match ($r->urgency_normalized) {
                'urgent' => 'Darurat',
                'medium' => 'Sedang / Penting',
                default => 'Rendah / Konseling',
            };
            $statusKey = $r->archive_reason === 'iseng' ? 'iseng' : ($r->status ?: 'selesai');
            $isComp = $r->status === 'selesai' || $r->archive_reason === 'selesai';

            $months[$monthKey] = ($months[$monthKey] ?? 0) + 1;
            $byCategory[$cat] = ($byCategory[$cat] ?? 0) + 1;
            $byRole[$role] = ($byRole[$role] ?? 0) + 1;
            $byLocation[$loc] = ($byLocation[$loc] ?? 0) + 1;
            $byUrgency[$urg] = ($byUrgency[$urg] ?? 0) + 1;
            $byStatus[$statusKey] = ($byStatus[$statusKey] ?? 0) + 1;
            if ($isComp) {
                $completed++;
            }

            $byMonth[$monthKey]['by_category'][$cat] = ($byMonth[$monthKey]['by_category'][$cat] ?? 0) + 1;
            $byMonth[$monthKey]['by_role'][$role] = ($byMonth[$monthKey]['by_role'][$role] ?? 0) + 1;
            $byMonth[$monthKey]['by_location'][$loc] = ($byMonth[$monthKey]['by_location'][$loc] ?? 0) + 1;
            $byMonth[$monthKey]['by_urgency'][$urg] = ($byMonth[$monthKey]['by_urgency'][$urg] ?? 0) + 1;
            $byMonth[$monthKey]['by_status'][$statusKey] = ($byMonth[$monthKey]['by_status'][$statusKey] ?? 0) + 1;
            if ($isComp) {
                $byMonth[$monthKey]['completed'] = ($byMonth[$monthKey]['completed'] ?? 0) + 1;
            }
        }

        $deletedCount = $targets->count();

        DB::transaction(function () use (
            $configuredDate,
            $deletedCount,
            $months,
            $byCategory,
            $byRole,
            $byLocation,
            $byUrgency,
            $byStatus,
            $completed,
            $byMonth,
            $targets,
            $actor
        ) {
            RetentionSummary::create([
                'retention_date' => $configuredDate,
                'deleted_reports_count' => $deletedCount,
                'summary_payload' => [
                    'months' => $months,
                    'by_category' => $byCategory,
                    'by_role' => $byRole,
                    'by_location' => $byLocation,
                    'by_urgency' => $byUrgency,
                    'by_status' => $byStatus,
                    'completed' => $completed,
                    'by_month' => $byMonth,
                ],
                'executed_by' => $actor?->name ?? 'Sistem Retensi Otomatis',
            ]);

            $ids = $targets->pluck('id')->all();
            DB::table('access_logs')->whereIn('student_report_id', $ids)->update(['student_report_id' => null]);
            DB::table('report_messages')->whereIn('student_report_id', $ids)->delete();
            DB::table('forwards')->whereIn('student_report_id', $ids)->delete();
            StudentReport::whereIn('id', $ids)->delete();
        });

        if ($actor) {
            AuditLogger::log($actor, 'run_retention', null, "Meringkas & membersihkan {$deletedCount} laporan arsip/selesai (tgl {$checkDate})");
        } else {
            AuditLogger::logSystem('run_retention', "retention-{$checkDate}", null, "Meringkas & membersihkan {$deletedCount} laporan arsip/selesai");
        }

        return [
            'executed' => true,
            'skipped_reason' => null,
            'deleted_count' => $deletedCount,
            'retention_date' => $configuredDate,
        ];
    }
}
