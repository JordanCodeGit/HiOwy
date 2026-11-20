<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('student_reports', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('status_updated_date');
            }
            if (! Schema::hasColumn('student_reports', 'archive_reason')) {
                $table->string('archive_reason', 32)->nullable()->after('archived_at');
            }
        });

        // Backfill archived_at if is_archived existed and was true
        if (Schema::hasColumn('student_reports', 'is_archived')) {
            DB::table('student_reports')
                ->where('is_archived', true)
                ->whereNull('archived_at')
                ->update([
                    'archived_at' => now(),
                    'archive_reason' => 'selesai',
                ]);
        }

        // Normalize legacy status values to match SRS (belum_dibaca, dibaca, ditindaklanjuti, selesai)
        DB::table('student_reports')->whereIn('status', ['unread'])->update(['status' => 'belum_dibaca']);
        DB::table('student_reports')->whereIn('status', ['read'])->update(['status' => 'dibaca']);
        DB::table('student_reports')->whereIn('status', ['sedang_diproses', 'in_progress'])->update(['status' => 'ditindaklanjuti']);
        DB::table('student_reports')->whereIn('status', ['resolved'])->update(['status' => 'selesai']);
        DB::table('student_reports')->where('status', 'iseng')->update([
            'status' => 'selesai',
            'archived_at' => now(),
            'archive_reason' => 'iseng',
        ]);

        if (! Schema::hasTable('report_messages')) {
            Schema::create('report_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_report_id')->constrained('student_reports')->cascadeOnDelete();
                $table->string('sender', 16); // bk, reporter
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('body'); // encrypted via model cast
                $table->date('sent_date');
            });
        }

        // Migrate any existing counselor_reply into report_messages before dropping legacy column
        if (Schema::hasColumn('student_reports', 'counselor_reply')) {
            $legacyReplies = DB::table('student_reports')
                ->whereNotNull('counselor_reply')
                ->select(['id', 'counselor_reply', 'counselor_replied_at', 'submitted_date'])
                ->get();

            foreach ($legacyReplies as $row) {
                if (! empty($row->counselor_reply)) {
                    DB::table('report_messages')->insert([
                        'student_report_id' => $row->id,
                        'sender' => 'bk',
                        'user_id' => null,
                        'body' => $row->counselor_reply,
                        'sent_date' => $row->counselor_replied_at
                            ? date('Y-m-d', strtotime((string) $row->counselor_replied_at))
                            : ($row->submitted_date ?: now()->toDateString()),
                    ]);
                }
            }
        }

        $dropLegacy = array_values(array_filter([
            'is_archived',
            'counselor_reply',
            'counselor_replied_at',
            'forwarded_to_walas',
            'forwarded_at',
        ], fn (string $col) => Schema::hasColumn('student_reports', $col)));

        if (! empty($dropLegacy)) {
            Schema::table('student_reports', function (Blueprint $table) use ($dropLegacy) {
                $table->dropColumn($dropLegacy);
            });
        }

        if (! Schema::hasTable('forwards')) {
            Schema::create('forwards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_report_id')->constrained('student_reports')->cascadeOnDelete();
                $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
                $table->text('summary'); // encrypted via model cast
                $table->timestamp('read_at')->nullable();
                $table->timestamp('forwarded_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('forwards');
        Schema::dropIfExists('report_messages');

        Schema::table('student_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('student_reports', 'is_archived')) {
                $table->boolean('is_archived')->default(false);
            }
            if (! Schema::hasColumn('student_reports', 'counselor_reply')) {
                $table->text('counselor_reply')->nullable();
            }
            if (! Schema::hasColumn('student_reports', 'counselor_replied_at')) {
                $table->timestamp('counselor_replied_at')->nullable();
            }
            if (! Schema::hasColumn('student_reports', 'forwarded_to_walas')) {
                $table->boolean('forwarded_to_walas')->default(false);
            }
            if (! Schema::hasColumn('student_reports', 'forwarded_at')) {
                $table->timestamp('forwarded_at')->nullable();
            }

            $dropCols = array_values(array_filter([
                'archived_at',
                'archive_reason',
            ], fn (string $col) => Schema::hasColumn('student_reports', $col)));

            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
