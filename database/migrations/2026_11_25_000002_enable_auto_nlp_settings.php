<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_settings')) {
            return;
        }

        $now = now();
        $defaults = [
            'nlp_enabled' => '1',
            'training_mode' => '0',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('school_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        // Reversible no-op; settings remain manageable.
    }
};
