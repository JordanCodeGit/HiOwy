<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth ID — nullable karena user form biasa tidak punya
            $table->string('google_id')->nullable()->unique()->after('remember_token');
            // Nama instansi/sekolah dari form pendaftaran
            $table->string('school_name')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'school_name']);
        });
    }
};
