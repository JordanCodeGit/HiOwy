<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentReport extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_hash',
        'category',
        'location',
        'involved_parties',
        'urgency',
        'reporter_role',
        'incident_time',
        'story',
        'is_anonymous',
        'reporter_name',
        'reporter_class',
        'reporter_contact',
        'status',
        'assigned_counselor',
        'internal_notes',
        'submitted_date',
        'submitted_daypart',
        'status_updated_date',
        'archived_at',
        'archive_reason',
        'is_training',
        'ml_label',
        'ml_confidence',
        'ml_model_version',
        'ml_status',
    ];

    protected $hidden = [
        'ticket_hash',
        'ml_label',
        'ml_confidence',
        'ml_model_version',
        'ml_status',
    ];

    protected $casts = [
        'story' => 'encrypted',
        'involved_parties' => 'encrypted',
        'reporter_name' => 'encrypted',
        'reporter_class' => 'encrypted',
        'reporter_contact' => 'encrypted',
        'internal_notes' => 'encrypted',
        'is_anonymous' => 'boolean',
        'is_training' => 'boolean',
        'ml_label' => 'integer',
        'ml_confidence' => 'float',
        'submitted_date' => 'date',
        'status_updated_date' => 'date',
        'archived_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ReportMessage::class, 'student_report_id')->orderBy('id');
    }

    public function forwards(): HasMany
    {
        return $this->hasMany(Forward::class, 'student_report_id')->orderByDesc('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeProduction(Builder $query): Builder
    {
        return $query->where('is_training', false);
    }

    public function scopeTraining(Builder $query): Builder
    {
        return $query->where('is_training', true);
    }

    public function getMlBadgeLabelAttribute(): string
    {
        if ($this->ml_status === 'selesai' && $this->ml_label !== null) {
            return $this->ml_label === 1 ? 'Indikasi Berat (ML)' : 'Indikasi Ringan (ML)';
        }

        return 'Belum Diklasifikasi';
    }

    public function isVisibleToKesiswaan(): bool
    {
        return IncidentCategory::where('label', $this->category)
            ->where('visible_to_kesiswaan', true)
            ->exists();
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->archived_at !== null;
    }

    public function getForwardedToWalasAttribute(): bool
    {
        return $this->relationLoaded('forwards')
            ? $this->forwards->isNotEmpty()
            : $this->forwards()->exists();
    }

    public function getCaseLabelAttribute(): string
    {
        return 'RPT-'.str_pad((string) ($this->id ?? 0), 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedTimeAttribute(): string
    {
        $daypart = ucfirst((string) ($this->submitted_daypart ?: 'pagi'));

        if ($this->submitted_date) {
            if ($this->submitted_date->isToday()) {
                return 'Hari ini • '.$daypart;
            }
            if ($this->submitted_date->isYesterday()) {
                return 'Kemarin • '.$daypart;
            }

            return $this->submitted_date->translatedFormat('d M Y').' • '.$daypart;
        }

        return $this->incident_time ?: $daypart;
    }

    public function getUrgencyNormalizedAttribute(): string
    {
        $u = strtolower((string) ($this->urgency ?: ''));
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
        $c = strtolower((string) ($this->category ?: ''));
        if (str_contains($c, 'fisik')) {
            return 'fisik';
        }
        if (str_contains($c, 'verbal') || str_contains($c, 'ejekan')) {
            return 'verbal';
        }
        if (str_contains($c, 'kucil') || str_contains($c, 'pengucilan')) {
            return 'dikucilkan';
        }
        if (str_contains($c, 'cyber')) {
            return 'cyber';
        }
        if (str_contains($c, 'pelecehan')) {
            return 'pelecehan';
        }

        return 'lainnya';
    }

    public function getRoleNormalizedAttribute(): string
    {
        $r = strtolower((string) ($this->reporter_role ?: ''));
        if (str_contains($r, 'ortu') || str_contains($r, 'orang tua') || str_contains($r, 'wali')) {
            return 'ortu';
        }
        if (str_contains($r, 'teman') || str_contains($r, 'saksi')) {
            return 'teman';
        }
        if (str_contains($r, 'guru')) {
            return 'guru';
        }

        return 'siswa';
    }

    public function getWantsContactAttribute(): bool
    {
        return ! empty($this->reporter_contact);
    }
}
