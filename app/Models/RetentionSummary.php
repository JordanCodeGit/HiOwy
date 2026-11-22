<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionSummary extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'retention_date',
        'deleted_reports_count',
        'summary_payload',
        'executed_by',
        'created_at',
    ];

    protected $casts = [
        'retention_date' => 'date',
        'deleted_reports_count' => 'integer',
        'summary_payload' => 'array',
        'created_at' => 'datetime',
    ];
}
