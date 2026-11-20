<?php

namespace App\Jobs;

use App\Models\StudentReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ClassifyStudentReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 10;

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [2, 5, 10];
    }

    public function __construct(
        public readonly StudentReport $report
    ) {}

    /**
     * Classify report via internal ML HTTP service (0=ringan, 1=berat) without downgrading Darurat (FR-45, FR-46).
     */
    public function handle(): void
    {
        $report = $this->report->fresh();
        if (! $report || $report->is_training) {
            return;
        }

        $baseUrl = rtrim((string) config('services.ml.url', 'http://127.0.0.1:8001'), '/');
        $response = Http::timeout(10)
            ->acceptJson()
            ->post("{$baseUrl}/v1/classify", [
                'report_id' => $report->id,
                'text' => (string) $report->story,
                'category' => (string) $report->category,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException("Layanan ML mengembalikan status HTTP {$response->status()}.");
        }

        $data = $response->json();
        if (! is_array($data) || ! array_key_exists('label', $data) || ! is_numeric($data['label'])) {
            throw new \RuntimeException('Respons layanan ML tidak valid (field label tidak ditemukan).');
        }

        $label = (int) $data['label'];
        if (! in_array($label, [0, 1], true)) {
            throw new \RuntimeException("Label klasifikasi ML tidak didukung: {$label}");
        }

        $confidence = isset($data['confidence']) && is_numeric($data['confidence'])
            ? round(min(1.0, max(0.0, (float) $data['confidence'])), 4)
            : null;
        $modelVersion = isset($data['model_version']) ? substr((string) $data['model_version'], 0, 64) : null;

        // Urgency 'Darurat' set by reporter is never downgraded by ML output (FR-46).
        $report->update([
            'ml_label' => $label,
            'ml_confidence' => $confidence,
            'ml_model_version' => $modelVersion,
            'ml_status' => 'selesai',
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        report($exception);

        if ($report = $this->report->fresh()) {
            $report->update([
                'ml_status' => 'belum_diklasifikasi',
            ]);
        }
    }
}
