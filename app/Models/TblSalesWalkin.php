<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblSalesWalkin extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales_walkin';

    protected $fillable = [
        'sold_at',
        'container_type',
        'quantity',
        'price_per_container',
        'total_amount',
        'customer_type',
        'payment_status',
        'note',
    ];

    protected $casts = [
        'sold_at'             => 'datetime',
        'quantity'            => 'integer',
        'price_per_container' => 'decimal:2',
        'total_amount'        => 'decimal:2',
    ];
}
