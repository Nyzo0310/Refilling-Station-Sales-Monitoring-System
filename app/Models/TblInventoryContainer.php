<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryContainer extends Model
{
    use HasFactory;

    protected $table = 'tbl_inventory_containers';

    protected $fillable = [
        'container_type',
        'size_liters',
        'stock_in',
        'stock_out',
        'current_stock',
        'remarks',
    ];

    protected $casts = [
        'size_liters'   => 'decimal:2',
        'stock_in'      => 'integer',
        'stock_out'     => 'integer',
        'current_stock' => 'integer',
    ];
}
