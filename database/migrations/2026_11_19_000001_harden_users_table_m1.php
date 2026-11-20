<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'google_id')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->dropUnique('users_google_id_unique');
                } catch (Throwable) {
                    // Index may already be absent on some setups
                }
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('google_id');
            });
        }

        if (Schema::hasColumn('users', 'school_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('school_name');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_name')) {
                $table->string('school_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('remember_token');
            }
            $table->string('role')->default('bk')->change();
        });
    }
};
