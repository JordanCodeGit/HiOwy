<?php

namespace App\Console\Commands;

use App\Support\RetentionService;
use Illuminate\Console\Command;

class RunRetentionCommand extends Command
{
    protected $signature = 'hiowy:run-retention {--force-date= : Tanggal simulasi retensi (YYYY-MM-DD)}';

    protected $description = 'Meringkas menjadi statistik dan menghapus laporan yang diarsipkan/selesai pada akhir tahun ajaran (FR-65)';

    public function handle(): int
    {
        $result = RetentionService::run($this->option('force-date') ?: null);

        if (! $result['executed']) {
            $this->warn($result['skipped_reason'] ?? 'Retensi dilewati.');

            return self::SUCCESS;
        }

        $this->info("Retensi selesai ({$result['retention_date']}). {$result['deleted_count']} laporan arsip/selesai diringkas menjadi statistik dan dihapus; laporan aktif/terbuka tetap utuh.");

        return self::SUCCESS;
    }
}
