<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('student_reports', 'ticket_code')) {
            Schema::table('student_reports', function (Blueprint $table) {
                try {
                    $table->dropUnique('student_reports_ticket_code_unique');
                } catch (Throwable) {
                    // Index may already be absent
                }
            });
        }

        $dropCols = array_values(array_filter([
            'ticket_code',
            'school_name',
            'attachment_path',
            'notes',
            'from_qr',
            'ip_address',
            'created_at',
            'updated_at',
        ], fn (string $col) => Schema::hasColumn('student_reports', $col)));

        if (! empty($dropCols)) {
            Schema::table('student_reports', function (Blueprint $table) use ($dropCols) {
                $table->dropColumn($dropCols);
            });
        }

        Schema::table('student_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('student_reports', 'ticket_hash')) {
                $table->string('ticket_hash', 64)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('student_reports', 'involved_parties')) {
                $table->text('involved_parties')->nullable()->after('location');
            }
            if (! Schema::hasColumn('student_reports', 'submitted_date')) {
                $table->date('submitted_date')->nullable()->after('is_archived');
            }
            if (! Schema::hasColumn('student_reports', 'submitted_daypart')) {
                $table->string('submitted_daypart', 16)->default('pagi')->after('submitted_date');
            }
            if (! Schema::hasColumn('student_reports', 'status_updated_date')) {
                $table->date('status_updated_date')->nullable()->after('submitted_daypart');
            }
        });

        if (Schema::hasTable('school_settings')) {
            DB::table('school_settings')->where('key', 'qr_scans_count')->delete();
            DB::table('school_settings')->updateOrInsert(
                ['key' => 'school_name'],
                ['value' => 'SMP Telkom Purwokerto', 'updated_at' => now(), 'created_at' => now()]
            );
        }

        if (Schema::hasTable('learning_materials') && Schema::hasColumn('learning_materials', 'author_name')) {
            Schema::table('learning_materials', function (Blueprint $table) {
                $table->string('author_name')->default('Guru BK')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            if (Schema::hasColumn('student_reports', 'ticket_hash')) {
                try {
                    $table->dropUnique(['ticket_hash']);
                } catch (Throwable) {
                }
                $table->dropColumn('ticket_hash');
            }

            $dropCols = array_values(array_filter([
                'involved_parties',
                'submitted_date',
                'submitted_daypart',
                'status_updated_date',
            ], fn (string $col) => Schema::hasColumn('student_reports', $col)));

            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }

            $table->string('ticket_code', 32)->nullable()->unique()->after('id');
            $table->string('school_name')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('from_qr')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }
};
