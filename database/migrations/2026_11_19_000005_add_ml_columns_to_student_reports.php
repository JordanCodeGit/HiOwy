<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('student_reports', 'ml_label')) {
                $table->tinyInteger('ml_label')->nullable()->after('is_training');
            }
            if (! Schema::hasColumn('student_reports', 'ml_confidence')) {
                $table->decimal('ml_confidence', 5, 4)->nullable()->after('ml_label');
            }
            if (! Schema::hasColumn('student_reports', 'ml_model_version')) {
                $table->string('ml_model_version', 64)->nullable()->after('ml_confidence');
            }
            if (! Schema::hasColumn('student_reports', 'ml_status')) {
                $table->string('ml_status', 32)->default('belum_diklasifikasi')->after('ml_model_version');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            $cols = array_filter(
                ['ml_label', 'ml_confidence', 'ml_model_version', 'ml_status'],
                fn (string $c): bool => Schema::hasColumn('student_reports', $c)
            );
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
