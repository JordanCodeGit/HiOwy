<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'permendikbud_type',
        'active',
        'permanent',
        'visible_to_kesiswaan',
        'order_index',
    ];

    protected $casts = [
        'active' => 'boolean',
        'permanent' => 'boolean',
        'visible_to_kesiswaan' => 'boolean',
        'order_index' => 'integer',
    ];
}
