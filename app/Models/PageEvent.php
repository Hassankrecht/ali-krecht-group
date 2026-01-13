<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageEvent extends Model
{
    protected $fillable = [
        'action',
        'path',
        'referrer',
        'ip',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
