<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBackwashStatus extends Model
{
    use HasFactory;

    protected $table = 'tbl_backwash_status';

    protected $fillable = [
        'last_backwash_at',
        'gallons_since_last',
        'threshold_gallons',
    ];

    protected $casts = [
        'last_backwash_at'   => 'datetime',
        'gallons_since_last' => 'decimal:2',
        'threshold_gallons'  => 'integer',
    ];
}
