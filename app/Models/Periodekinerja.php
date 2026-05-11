<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodekinerja extends Model
{
    protected $table = 'periode_kinerja';

    protected $fillable = [
        'blth',
        'status',
    ];

    public $timestamps = false;

    protected $casts = [
        'status' => 'string',
    ];
}
