<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'student_report_id',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'details' => 'encrypted',
        'created_at' => 'datetime',
    ];

    /**
     * Enforces strict append-only immutability on audit logs (FR-62, FR-63).
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new \LogicException('Log akses bersifat append-only dan tidak dapat diubah.');
        });

        static::deleting(function (): void {
            throw new \LogicException('Log akses bersifat append-only dan tidak dapat dihapus.');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(StudentReport::class, 'student_report_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'login_success' => 'Login Berhasil (2FA Terverifikasi)',
            'login_failed' => 'Percobaan Login Gagal',
            'login_locked' => 'Akun Dikunci Sementara (5x Gagal)',
            '2fa_failed' => 'Verifikasi 2FA Gagal',
            'logout' => 'Logout Sistem',
            'view_report' => 'Membuka Detail Laporan',
            'update_status' => 'Mengubah Status / Penugasan',
            'correct_category' => 'Mengoreksi Jenis Kejadian',
            'add_note' => 'Menambah Catatan Internal BK',
            'add_kesiswaan_note' => 'Menambah Catatan Tindak Lanjut Kesiswaan',
            'reply_report' => 'Mengirim Balasan ke Pelapor',
            'forward_report' => 'Meneruskan Ringkasan ke Wali Kelas',
            'mark_iseng' => 'Menandai Laporan Iseng',
            'archive_report' => 'Mengarsipkan Laporan',
            'unarchive_report' => 'Mengeluarkan Laporan dari Arsip',
            'view_forward' => 'Membuka Ringkasan Wali Kelas',
            'toggle_forward_read' => 'Mengubah Status Baca Ringkasan',
            'toggle_training_mode' => 'Mengubah Mode Latihan',
            'toggle_nlp_mode' => 'Mengubah Status Modul NLP',
            'update_school_settings' => 'Memperbarui Konfigurasi Sekolah',
            'run_retention' => 'Menjalankan Retensi Otomatis Arsip',
            'server_delete_report' => 'Menghapus Laporan Permanen (Otoritas Server)',
            'invite_staff' => 'Mengundang Akun Staf',
            'update_staff' => 'Memperbarui Akses Staf',
            'reset_staff_access' => 'Mereset Kata Sandi & 2FA Staf',
            'revoke_staff' => 'Mencabut Akses Staf',
            default => ucfirst(str_replace('_', ' ', (string) $this->action)),
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match (strtolower((string) $this->user_role)) {
            'bk' => 'Guru BK / Konselor',
            'kesiswaan' => 'Tim Kesiswaan',
            'walas', 'wali_kelas', 'walikelas' => 'Wali Kelas',
            'system' => 'Keamanan Sistem',
            default => $this->user_role ?: 'Staf',
        };
    }
}
