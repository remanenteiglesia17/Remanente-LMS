<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'curso_id',
        'code',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];
}