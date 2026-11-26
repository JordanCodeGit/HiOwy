<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('student_reports', 'ticket_password_hash')) {
            Schema::table('student_reports', function (Blueprint $table) {
                $table->string('ticket_password_hash')->nullable()->after('ticket_hash');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('student_reports', 'ticket_password_hash')) {
            Schema::table('student_reports', function (Blueprint $table) {
                $table->dropColumn('ticket_password_hash');
            });
        }
    }
};
