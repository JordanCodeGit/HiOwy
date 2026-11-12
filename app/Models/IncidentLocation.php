<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'active',
        'permanent',
        'order_index',
    ];

    protected $casts = [
        'active' => 'boolean',
        'permanent' => 'boolean',
        'order_index' => 'integer',
    ];
}
