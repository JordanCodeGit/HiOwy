<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaliKelasPreview
{
    private const DEFAULT_SUMMARIES = [];

    private function getStoragePath(): string
    {
        return storage_path('framework/walas_summaries.json');
    }

    public function getTeacherClass(): string
    {
        if (Auth::check()) {
            $staff = rescue(fn() => DB::table('staff_members')->where('email', Auth::user()->email)->first(), null, false);
            if ($staff && !empty($staff->class_name)) {
                return $staff->class_name;
            }
        }
        return '7B';
    }

    public function all(): array
    {
        $path = $this->getStoragePath();
        $class = $this->getTeacherClass();

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) {
                foreach ($data as &$item) {
                    $item['class'] = $item['class'] ?? $class;
                }
                return $data;
            }
        }

        return [];
    }

    /**
     * Mengambil seluruh ringkasan binaan kelas guru aktif.
     */
    public function summaries(): Collection
    {
        return collect($this->all());
    }

    public function findSummary(int $id): array
    {
        return $this->summaries()->firstWhere('id', $id) ?? abort(404);
    }

    public function toggleRead(int $id): array
    {
        $items = $this->all();
        $updated = null;

        foreach ($items as &$item) {
            if ((int)$item['id'] === (int)$id) {
                if ($item['status'] === 'read') {
                    $item['status'] = 'unread';
                    $item['read_at'] = null;
                } else {
                    $item['status'] = 'read';
                    $item['read_at'] = now()->translatedFormat('d M Y, H:i') . ' WIB';
                }
                $updated = $item;
                break;
            }
        }

        @file_put_contents($this->getStoragePath(), json_encode($items, JSON_PRETTY_PRINT));
        return $updated ?? abort(404);
    }

    public function markAllAsRead(): void
    {
        $items = $this->all();
        $now = now()->translatedFormat('d M Y, H:i') . ' WIB';

        foreach ($items as &$item) {
            if ($item['status'] === 'unread') {
                $item['status'] = 'read';
                $item['read_at'] = $now;
            }
        }

        @file_put_contents($this->getStoragePath(), json_encode($items, JSON_PRETTY_PRINT));
    }
}
