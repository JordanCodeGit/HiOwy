<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'audience',
        'content',
        'is_visible',
        'author_name',
        'author_id',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function audienceLabel(): string
    {
        return match ($this->audience) {
            'orang_tua' => 'Orang Tua & Wali',
            'umum'      => 'Umum & Satgas TPPK',
            default     => 'Siswa & Remaja',
        };
    }

    public function audienceBadgeClass(): string
    {
        return match ($this->audience) {
            'orang_tua' => 'bg-amber-100 text-amber-800',
            'umum'      => 'bg-rose-100 text-rose-800',
            default     => 'bg-blue-100 text-blue-800',
        };
    }
}
