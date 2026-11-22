<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incident_categories') && ! Schema::hasColumn('incident_categories', 'permendikbud_type')) {
            Schema::table('incident_categories', function (Blueprint $table) {
                $table->string('permendikbud_type', 120)->nullable()->after('label');
            });

            $defaultMappings = [
                'Ejekan & Intimidasi Verbal' => 'Perundungan Verbal / Kekerasan Psikis (Permendikbudristek 46/2023)',
                'Perundungan Fisik & Pemukulan' => 'Kekerasan Fisik (Permendikbudristek 46/2023)',
                'Pengucilan & Diskriminasi Sosial' => 'Diskriminasi & Intoleransi (Permendikbudristek 46/2023)',
                'Pelecehan Melalui Chat & Medsos' => 'Kekerasan Psikis / Perundungan Daring (Permendikbudristek 46/2023)',
                'Sentuhan atau Perilaku Tidak Pantas' => 'Kekerasan Seksual (Permendikbudristek 46/2023)',
                'Pemerasan & Tekanan Kelompok' => 'Kekerasan Psikis / Pemerasan (Permendikbudristek 46/2023)',
                'Masalah Pribadi & Lainnya' => 'Pendampingan Konseling / Lainnya',
            ];

            foreach ($defaultMappings as $label => $mapped) {
                DB::table('incident_categories')
                    ->where('label', $label)
                    ->whereNull('permendikbud_type')
                    ->update(['permendikbud_type' => $mapped]);
            }
        }

        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                if (! Schema::hasColumn('student_reports', 'incident_date')) {
                    $table->date('incident_date')->nullable()->after('incident_time');
                }
                if (! Schema::hasColumn('student_reports', 'bk_read_at')) {
                    $table->timestamp('bk_read_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('student_reports', 'kesiswaan_notes')) {
                    $table->text('kesiswaan_notes')->nullable()->after('internal_notes');
                }
            });
        }

        if (Schema::hasTable('report_messages')) {
            Schema::table('report_messages', function (Blueprint $table) {
                if (! Schema::hasColumn('report_messages', 'bk_sent_time')) {
                    $table->string('bk_sent_time', 16)->nullable()->after('sent_date');
                }
                if (! Schema::hasColumn('report_messages', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('bk_sent_time');
                }
            });
        }

        if (! Schema::hasTable('retention_summaries')) {
            Schema::create('retention_summaries', function (Blueprint $table) {
                $table->id();
                $table->date('retention_date');
                $table->unsignedInteger('deleted_reports_count')->default(0);
                $table->json('summary_payload');
                $table->string('executed_by', 120)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (Schema::hasTable('school_settings')) {
            $defaults = [
                'reading_hours' => 'Senin–Jumat, 07.00–15.00 WIB (Hari Kerja Sekolah)',
                'emergency_contact' => '',
                'school_website_url' => '',
                'school_logo' => '',
                'retention_date' => '',
                'story_max_length' => '2000',
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
        Schema::dropIfExists('retention_summaries');

        if (Schema::hasTable('report_messages')) {
            Schema::table('report_messages', function (Blueprint $table) {
                $cols = array_values(array_filter(['bk_sent_time', 'read_at'], fn ($c) => Schema::hasColumn('report_messages', $c)));
                if ($cols !== []) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                $cols = array_values(array_filter(['incident_date', 'bk_read_at', 'kesiswaan_notes'], fn ($c) => Schema::hasColumn('student_reports', $c)));
                if ($cols !== []) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('incident_categories') && Schema::hasColumn('incident_categories', 'permendikbud_type')) {
            Schema::table('incident_categories', function (Blueprint $table) {
                $table->dropColumn('permendikbud_type');
            });
        }
    }
};
