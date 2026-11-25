<?php

namespace App\Console\Commands;

use App\Jobs\ClassifyStudentReportJob;
use App\Models\StudentReport;
use Illuminate\Console\Command;

class ClassifyReportsCommand extends Command
{
    protected $signature = 'hiowy:classify-reports {--all : Klasifikasi ulang seluruh laporan resmi, termasuk yang sudah selesai}';

    protected $description = 'Menjalankan model NLP Linear SVM pada laporan resmi untuk memprediksi dampak & tingkat urgensi';

    public function handle(): int
    {
        $query = StudentReport::production()->orderBy('id');
        if (! $this->option('all')) {
            $query->where(function ($q): void {
                $q->whereNull('ml_status')
                    ->orWhere('ml_status', '!=', 'selesai');
            });
        }

        $reports = $query->get();
        if ($reports->isEmpty()) {
            $this->info('Semua laporan resmi sudah terklasifikasi NLP.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($reports as $report) {
            (new ClassifyStudentReportJob($report))->handle();
            $report->refresh();
            $this->line(sprintf(
                '#%s -> %s (%.1f%%) | Urgensi: %s',
                $report->case_label,
                $report->ml_badge_label,
                ((float) $report->ml_confidence) * 100,
                $report->urgency
            ));
            $count++;
        }

        $this->info("Selesai mengklasifikasi {$count} laporan.");

        return self::SUCCESS;
    }
}
