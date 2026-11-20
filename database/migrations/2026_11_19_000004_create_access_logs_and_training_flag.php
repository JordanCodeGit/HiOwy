<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('access_logs')) {
            Schema::create('access_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name', 120);
                $table->string('user_role', 32)->nullable();
                $table->string('action', 64)->index();
                $table->foreignId('student_report_id')->nullable()->constrained('student_reports')->nullOnDelete();
                $table->text('details')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
            });
        }

        Schema::table('student_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('student_reports', 'is_training')) {
                $table->boolean('is_training')->default(false)->after('archive_reason');
            }
        });

        if (Schema::hasTable('school_settings')) {
            foreach (['training_mode' => '0', 'nlp_enabled' => '0'] as $key => $val) {
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
        Schema::dropIfExists('access_logs');

        Schema::table('student_reports', function (Blueprint $table) {
            if (Schema::hasColumn('student_reports', 'is_training')) {
                $table->dropColumn('is_training');
            }
        });
    }
};
