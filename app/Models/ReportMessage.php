<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_report_id',
        'sender',
        'user_id',
        'body',
        'sent_date',
        'bk_sent_time',
        'read_at',
    ];

    protected $casts = [
        'body' => 'encrypted',
        'sent_date' => 'date',
        'read_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(StudentReport::class, 'student_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
