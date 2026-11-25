<?php

namespace App\Support;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;

class SchoolConfigService
{
    /**
     * Ambil seluruh konfigurasi publik & kebijakan sekolah dengan fallback aman.
     *
     * @return array<string, mixed>
     */
    public static function getConfig(): array
    {
        $hasSettings = Schema::hasTable('school_settings');

        $get = fn (string $key, string $default = ''): string => $hasSettings
            ? (string) (SchoolSetting::get($key, $default) ?? $default)
            : $default;

        $emergencyRaw = trim($get('emergency_contact', ''));
        $digits = preg_replace('/\D+/', '', $emergencyRaw) ?: '';
        $waDigits = str_starts_with($digits, '0')
            ? '62'.substr($digits, 1)
            : $digits;

        $websiteRaw = trim($get('school_website_url', ''));
        $websiteUrl = preg_match('#^https?://#i', $websiteRaw) ? $websiteRaw : '';

        $logoRaw = trim($get('school_logo', ''));
        $logoUrl = $logoRaw !== '' && file_exists(public_path(ltrim($logoRaw, '/')))
            ? asset(ltrim($logoRaw, '/'))
            : '';

        $storyMax = (int) $get('story_max_length', '2000');
        if ($storyMax < 200 || $storyMax > 10000) {
            $storyMax = 2000;
        }

        $dayStart = trim($get('reading_day_start', 'Senin')) ?: 'Senin';
        $dayEnd = trim($get('reading_day_end', 'Jumat')) ?: 'Jumat';
        $timeStart = trim($get('reading_time_start', '07:00')) ?: '07:00';
        $timeEnd = trim($get('reading_time_end', '15:00')) ?: '15:00';

        $composedReadingHours = self::formatReadingHours($dayStart, $dayEnd, $timeStart, $timeEnd);
        $storedReadingHours = trim($get('reading_hours', ''));
        $readingHours = $storedReadingHours !== '' ? $storedReadingHours : $composedReadingHours;

        return [
            'school_name' => trim($get('school_name', 'SMP Telkom Purwokerto')) ?: 'SMP Telkom Purwokerto',
            'school_logo' => $logoRaw,
            'school_logo_url' => $logoUrl,
            'reading_hours' => $readingHours,
            'reading_day_start' => $dayStart,
            'reading_day_end' => $dayEnd,
            'reading_time_start' => $timeStart,
            'reading_time_end' => $timeEnd,
            'emergency_contact' => $emergencyRaw,
            'emergency_wa_url' => strlen($waDigits) >= 8 ? 'https://wa.me/'.$waDigits : '',
            'emergency_tel_url' => strlen($digits) >= 6 ? 'tel:'.$emergencyRaw : '',
            'school_website_url' => $websiteUrl,
            'retention_date' => trim($get('retention_date', '')),
            'story_max_length' => $storyMax,
            'training_mode' => $get('training_mode', '0') === '1',
            'nlp_enabled' => $get('nlp_enabled', '0') === '1',
        ];
    }

    /**
     * Format rentang hari kerja & jam kerja sekolah menjadi keterangan jam baca.
     */
    public static function formatReadingHours(string $dayStart, string $dayEnd, string $timeStart, string $timeEnd): string
    {
        $dayPart = $dayStart === $dayEnd ? $dayStart : "{$dayStart}–{$dayEnd}";
        $tStart = str_replace(':', '.', $timeStart);
        $tEnd = str_replace(':', '.', $timeEnd);

        return "{$dayPart}, {$tStart}–{$tEnd} WIB";
    }

    /**
     * Kategori khusus konselor BK (tidak terlihat di akun Kesiswaan): Masalah Pribadi & Teman Cerita.
     */
    public static function isPrivateCategoryLabel(string $label): bool
    {
        $lower = strtolower(trim($label));

        return str_contains($lower, 'masalah pribadi') || str_contains($lower, 'teman cerita');
    }

    /**
     * Daftar bentuk kekerasan Permendikbudristek No. 46 Tahun 2023 (FR-08).
     *
     * @return array<int, string>
     */
    public static function permendikbudTypes(): array
    {
        return [
            'Kekerasan Fisik (Permendikbudristek 46/2023)',
            'Kekerasan Psikis (Permendikbudristek 46/2023)',
            'Perundungan / Bullying (Permendikbudristek 46/2023)',
            'Perundungan Daring (Permendikbudristek 46/2023)',
            'Kekerasan Seksual (Permendikbudristek 46/2023)',
            'Diskriminasi & Intoleransi (Permendikbudristek 46/2023)',
            'Kebijakan Mengandung Kekerasan (Permendikbudristek 46/2023)',
            'Pendampingan Konseling / Lainnya',
        ];
    }
}
