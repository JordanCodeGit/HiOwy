<?php

namespace App\Jobs;

use App\Models\StudentReport;
use App\Support\BullyingNlpClassifier;
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
     * Klasifikasi cerita laporan (0=Dampak Ringan, 1=Dampak Berat) dan prediksi tingkat urgensi otomatis.
     * Prinsip keselamatan: laporan yang sejak awal ditandai 'Darurat' oleh pelapor tidak pernah diturunkan.
     */
    public function handle(): void
    {
        $report = $this->report->fresh();
        if (! $report || $report->is_training) {
            return;
        }

        $driver = strtolower((string) config('services.ml.driver', 'local'));

        if ($driver === 'http') {
            $result = $this->classifyViaHttp($report);
        } else {
            $result = BullyingNlpClassifier::classify((string) $report->story, (string) $report->category);
        }

        $label = (int) $result['label'];
        $confidence = $result['confidence'];
        $probBerat = (float) $result['prob_berat'];
        $modelVersion = $result['model_version'];

        $resolvedUrgency = BullyingNlpClassifier::resolveUrgency($probBerat, $report->urgency);

        $report->update([
            'ml_label' => $label,
            'ml_confidence' => $confidence,
            'ml_model_version' => $modelVersion,
            'ml_status' => 'selesai',
            'urgency' => $resolvedUrgency,
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

    /**
     * @return array{label: int, confidence: float|null, prob_berat: float, model_version: string|null}
     */
    private function classifyViaHttp(StudentReport $report): array
    {
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

        if (isset($data['prob_berat']) && is_numeric($data['prob_berat'])) {
            $probBerat = min(1.0, max(0.0, (float) $data['prob_berat']));
        } elseif ($confidence !== null) {
            $probBerat = $label === 1 ? $confidence : (1.0 - $confidence);
        } else {
            $probBerat = $label === 1 ? 0.75 : 0.25;
        }

        $modelVersion = isset($data['model_version']) ? substr((string) $data['model_version'], 0, 64) : null;

        return [
            'label' => $label,
            'confidence' => $confidence,
            'prob_berat' => $probBerat,
            'model_version' => $modelVersion,
        ];
    }
}
