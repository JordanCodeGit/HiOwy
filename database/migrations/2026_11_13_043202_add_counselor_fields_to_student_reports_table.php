<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            $table->string('assigned_counselor')->nullable()->after('notes');
            $table->text('internal_notes')->nullable()->after('assigned_counselor');
            $table->text('counselor_reply')->nullable()->after('internal_notes');
            $table->timestamp('counselor_replied_at')->nullable()->after('counselor_reply');
            $table->boolean('forwarded_to_walas')->default(false)->after('counselor_replied_at');
            $table->timestamp('forwarded_at')->nullable()->after('forwarded_to_walas');
            $table->boolean('is_archived')->default(false)->after('forwarded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            $table->dropColumn([
                'assigned_counselor',
                'internal_notes',
                'counselor_reply',
                'counselor_replied_at',
                'forwarded_to_walas',
                'forwarded_at',
                'is_archived',
            ]);
        });
    }
};
