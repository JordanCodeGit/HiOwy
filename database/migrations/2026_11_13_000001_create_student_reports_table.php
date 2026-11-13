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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 32)->unique();
            $table->string('school_name')->nullable();
            $table->string('category', 120);
            $table->string('location', 120);
            $table->string('urgency', 32)->default('Sedang'); // Darurat, Tinggi, Sedang, Rendah
            $table->string('reporter_role', 64)->default('Siswa'); // Korban Langsung, Saksi / Teman Korban, Orang Tua
            $table->string('incident_time', 120)->nullable();
            $table->text('story');
            $table->boolean('is_anonymous')->default(true);
            $table->string('reporter_name')->nullable();
            $table->string('reporter_class')->nullable();
            $table->string('reporter_contact')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status', 32)->default('belum_dibaca'); // belum_dibaca, sedang_diproses, selesai, iseng
            $table->text('notes')->nullable();
            $table->boolean('from_qr')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
