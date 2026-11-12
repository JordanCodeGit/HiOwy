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
        Schema::create('incident_categories', function (Blueprint $table) {
            $table->id();
            $table->string('label', 120);
            $table->boolean('active')->default(true);
            $table->boolean('permanent')->default(false);
            $table->boolean('visible_to_kesiswaan')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('incident_locations', function (Blueprint $table) {
            $table->id();
            $table->string('label', 120);
            $table->boolean('active')->default(true);
            $table->boolean('permanent')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('role')->default('kesiswaan'); // kesiswaan, walikelas
            $table->string('class_name')->nullable();     // e.g. 7A, 8A, 9B
            $table->string('status')->default('pending');  // aktif, pending, nonaktif
            $table->timestamp('invited_at')->nullable();
            $table->timestamps();
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('incident_locations');
        Schema::dropIfExists('incident_categories');
    }
};
