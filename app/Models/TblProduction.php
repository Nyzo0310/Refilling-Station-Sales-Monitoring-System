<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblProduction extends Model
{
    use HasFactory;

    protected $table = 'tbl_production';

    protected $fillable = [
        'date',
        'gallons_produced',
        'production_cost_per_gallon',
        'total_production_cost',
    ];

    protected $casts = [
        'date'                      => 'date',
        'gallons_produced'          => 'decimal:2',
        'production_cost_per_gallon'=> 'decimal:2',
        'total_production_cost'     => 'decimal:2',
    ];
}
