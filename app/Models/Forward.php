<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forward extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_report_id',
        'from_user_id',
        'to_user_id',
        'summary',
        'read_at',
        'forwarded_at',
    ];

    protected $casts = [
        'summary' => 'encrypted',
        'read_at' => 'datetime',
        'forwarded_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(StudentReport::class, 'student_report_id');
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
