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
        'from_qr',
        'ip_address',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'from_qr'      => 'boolean',
    ];
}
