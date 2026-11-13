<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    protected $fillable = [
        'ticket_code',
        'school_name',
        'category',
        'location',
        'urgency',
        'reporter_role',
        'incident_time',
        'story',
        'is_anonymous',
        'reporter_name',
        'reporter_class',
        'reporter_contact',
        'attachment_path',
        'status',
        'notes',
        'assigned_counselor',
        'internal_notes',
        'counselor_reply',
        'counselor_replied_at',
        'forwarded_to_walas',
        'forwarded_at',
        'is_archived',
        'from_qr',
        'ip_address',
    ];

    protected $casts = [
        'is_anonymous'         => 'boolean',
        'from_qr'              => 'boolean',
        'forwarded_to_walas'   => 'boolean',
        'is_archived'          => 'boolean',
        'counselor_replied_at' => 'datetime',
        'forwarded_at'         => 'datetime',
    ];

    public function getFormattedTimeAttribute(): string
    {
        if ($this->incident_time && str_contains($this->incident_time, '•')) {
            return $this->incident_time;
        }
        if ($this->created_at) {
            if ($this->created_at->isToday()) {
                return 'Hari ini • ' . $this->created_at->format('H:i');
            }
            if ($this->created_at->isYesterday()) {
                return 'Kemarin • ' . $this->created_at->format('H:i');
            }
            return $this->created_at->translatedFormat('d M') . ' • ' . $this->created_at->format('H:i');
        }
        return $this->incident_time ?: 'Baru saja';
    }

    public function getUrgencyNormalizedAttribute(): string
    {
        $u = strtolower($this->urgency ?: '');
        if (str_contains($u, 'darurat') || str_contains($u, 'urgent') || str_contains($u, 'tinggi')) {
            return 'urgent';
        }
        if (str_contains($u, 'sedang') || str_contains($u, 'medium')) {
            return 'medium';
        }
        return 'easy';
    }

    public function getCategoryNormalizedAttribute(): string
    {
        $c = strtolower($this->category ?: '');
        if (str_contains($c, 'fisik')) return 'fisik';
        if (str_contains($c, 'verbal') || str_contains($c, 'ejekan')) return 'verbal';
        if (str_contains($c, 'kucil') || str_contains($c, 'pengucilan')) return 'dikucilkan';
        if (str_contains($c, 'cyber')) return 'cyber';
        if (str_contains($c, 'pelecehan')) return 'pelecehan';
        return 'lainnya';
    }

    public function getRoleNormalizedAttribute(): string
    {
        $r = strtolower($this->reporter_role ?: '');
        if (str_contains($r, 'ortu') || str_contains($r, 'orang tua') || str_contains($r, 'wali')) return 'ortu';
        if (str_contains($r, 'teman') || str_contains($r, 'saksi')) return 'teman';
        if (str_contains($r, 'guru')) return 'guru';
        return 'siswa';
    }

    public function getWantsContactAttribute(): bool
    {
        return !empty($this->reporter_contact);
    }
}
