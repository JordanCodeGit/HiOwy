<?php

namespace App\Support;

use App\Models\StudentReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class AcademicYearService
{
    public const ALL = 'all';

    /**
     * Tahun ajaran berjalan berdasarkan kalender pendidikan Indonesia (1 Juli – 30 Juni).
     */
    public static function current(?Carbon $now = null): string
    {
        $date = ($now ?? Carbon::now('Asia/Jakarta'))->copy()->timezone('Asia/Jakarta');
        $startYear = $date->month >= 7 ? $date->year : $date->year - 1;

        return $startYear.'/'.($startYear + 1);
    }

    /**
     * Label semester berjalan (Ganjil: Jul–Des, Genap: Jan–Jun) atau mengikuti sesi.
     */
    public static function semesterLabel(): string
    {
        $sessionSemester = session('academic_semester');
        if ($sessionSemester === 'ganjil') {
            return 'Semester Ganjil';
        }
        if ($sessionSemester === 'genap') {
            return 'Semester Genap';
        }

        $month = Carbon::now('Asia/Jakarta')->month;

        return $month >= 7 ? 'Semester Ganjil' : 'Semester Genap';
    }

    /**
     * Validasi format tahun ajaran ("all" atau "YYYY/ZZZZ" dengan ZZZZ = YYYY + 1).
     */
    public static function isValid(?string $value): bool
    {
        if ($value === self::ALL) {
            return true;
        }

        if (! is_string($value)) {
            return false;
        }

        if (preg_match('/^(\d{4})\/(\d{4})$/', trim($value), $matches) !== 1) {
            return false;
        }

        return ((int) $matches[2]) === ((int) $matches[1] + 1);
    }

    /**
     * Tahun ajaran yang sedang dipilih pada sesi staf saat ini.
     */
    public static function selected(): string
    {
        $raw = session('academic_year');
        if (is_string($raw) && self::isValid($raw)) {
            return trim($raw);
        }

        return self::current();
    }

    /**
     * Label tampilan di header & laporan cetak.
     */
    public static function label(?string $year = null): string
    {
        $selected = $year ?? self::selected();

        if ($selected === self::ALL) {
            return 'Semua Tahun Ajaran';
        }

        return 'Tahun Ajaran '.$selected;
    }

    /**
     * Rentang tanggal [mulai, akhir] untuk tahun ajaran (null jika "Semua Tahun Ajaran").
     *
     * @return array{0: string, 1: string}|null
     */
    public static function dateRange(?string $year = null): ?array
    {
        $selected = $year ?? self::selected();

        if ($selected === self::ALL || ! self::isValid($selected)) {
            return null;
        }

        [$startYear, $endYear] = explode('/', $selected);

        return ["{$startYear}-07-01", "{$endYear}-06-30"];
    }

    /**
     * Konversi tanggal ke format tahun ajaran Indonesia (YYYY/ZZZZ).
     */
    public static function yearFromDate(Carbon|string $date): string
    {
        $carbon = $date instanceof Carbon
            ? $date->copy()
            : Carbon::parse($date, 'Asia/Jakarta');

        $startYear = $carbon->month >= 7 ? $carbon->year : $carbon->year - 1;

        return $startYear.'/'.($startYear + 1);
    }

    /**
     * Daftar opsi dropdown tahun ajaran (termasuk "Semua Tahun Ajaran").
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $current = self::current();
        $currentStart = (int) explode('/', $current)[0];

        $startYears = [];
        $minYear = min(2023, $currentStart - 2);
        for ($y = $currentStart; $y >= $minYear; $y--) {
            $startYears[$y] = true;
        }

        $selected = self::selected();
        if ($selected !== self::ALL && self::isValid($selected)) {
            $startYears[(int) explode('/', $selected)[0]] = true;
        }

        $reportDates = rescue(function () {
            if (! Schema::hasTable('student_reports')) {
                return [];
            }

            return StudentReport::query()
                ->whereNotNull('submitted_date')
                ->select('submitted_date')
                ->distinct()
                ->pluck('submitted_date')
                ->all();
        }, [], false);

        foreach ($reportDates as $date) {
            if (! empty($date)) {
                $ay = self::yearFromDate($date);
                $startYears[(int) explode('/', $ay)[0]] = true;
            }
        }

        krsort($startYears);

        $options = [
            self::ALL => 'Semua Tahun Ajaran',
        ];

        foreach (array_keys($startYears) as $startYear) {
            $key = $startYear.'/'.($startYear + 1);
            $options[$key] = 'Tahun Ajaran '.$key;
        }

        return $options;
    }

    /**
     * Terapkan filter tahun ajaran aktif ke query StudentReport berdasarkan submitted_date.
     */
    public static function applyToReports(Builder $query, ?string $year = null): Builder
    {
        $range = self::dateRange($year);
        if ($range === null) {
            return $query;
        }

        return $query->whereBetween('submitted_date', $range);
    }

    /**
     * Terapkan filter tahun ajaran aktif ke query Forward berdasarkan forwarded_at.
     */
    public static function applyToForwards(Builder $query, ?string $year = null): Builder
    {
        $range = self::dateRange($year);
        if ($range === null) {
            return $query;
        }

        return $query->whereBetween('forwarded_at', [
            $range[0].' 00:00:00',
            $range[1].' 23:59:59',
        ]);
    }
}
