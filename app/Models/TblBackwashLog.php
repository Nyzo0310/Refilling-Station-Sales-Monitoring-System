<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBackwashLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_backwash_logs';

    protected $fillable = [
        'backwash_at',
        'remarks',
    ];

    protected $casts = [
        'backwash_at' => 'datetime',
    ];
}
