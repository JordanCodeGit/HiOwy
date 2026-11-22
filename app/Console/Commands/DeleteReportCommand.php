<?php

namespace App\Console\Commands;

use App\Models\StudentReport;
use App\Support\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteReportCommand extends Command
{
    protected $signature = 'hiowy:delete-report {id : ID numerik laporan} {--approval= : Nomor/referensi persetujuan tertulis kepala sekolah} {--reason= : Alasan penghapusan di luar retensi otomatis}';

    protected $description = 'Penghapusan laporan di luar retensi otomatis khusus pengelola server sekolah dengan persetujuan tertulis & alasan wajib (FR-64)';

    public function handle(): int
    {
        $approval = trim((string) $this->option('approval'));
        $reason = trim((string) $this->option('reason'));

        if ($approval === '' || $reason === '') {
            $this->error('Penghapusan ditolak: parameter --approval (persetujuan tertulis kepala sekolah) dan --reason (alasan) wajib diisi.');

            return self::FAILURE;
        }

        $report = StudentReport::find($this->argument('id'));
        if (! $report) {
            $this->error('Laporan tidak ditemukan.');

            return self::FAILURE;
        }

        $caseLabel = $report->case_label;

        DB::transaction(function () use ($report, $caseLabel, $approval, $reason) {
            DB::table('access_logs')->where('student_report_id', $report->id)->update(['student_report_id' => null]);
            DB::table('report_messages')->where('student_report_id', $report->id)->delete();
            DB::table('forwards')->where('student_report_id', $report->id)->delete();
            $report->delete();

            AuditLogger::logSystem(
                'server_delete_report',
                'server-admin',
                null,
                "Laporan #{$caseLabel} dihapus pengelola server | Persetujuan: {$approval} | Alasan: {$reason}"
            );
        });

        $this->info("Laporan #{$caseLabel} berhasil dihapus permanen dan tercatat di log audit.");

        return self::SUCCESS;
    }
}
