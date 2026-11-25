<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incident_categories')) {
            // Semua kategori aktif secara bawaan agar siswa melihat semua kategori buatan Tim BK
            DB::table('incident_categories')->update(['active' => true]);

            // Semua kategori terlihat di Kesiswaan KECUALI Masalah Pribadi & Teman Cerita
            $categories = DB::table('incident_categories')->get();
            foreach ($categories as $cat) {
                $lower = strtolower((string) $cat->label);
                $isPrivate = str_contains($lower, 'masalah pribadi') || str_contains($lower, 'teman cerita');
                DB::table('incident_categories')
                    ->where('id', $cat->id)
                    ->update(['visible_to_kesiswaan' => ! $isPrivate]);
            }
        }

        if (Schema::hasTable('school_settings')) {
            $defaults = [
                'reading_day_start' => 'Senin',
                'reading_day_end' => 'Jumat',
                'reading_time_start' => '07:00',
                'reading_time_end' => '15:00',
            ];

            foreach ($defaults as $key => $val) {
                if (! DB::table('school_settings')->where('key', $key)->exists()) {
                    DB::table('school_settings')->insert([
                        'key' => $key,
                        'value' => $val,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Reversible data defaults no-op
    }
};
