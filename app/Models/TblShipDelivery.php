<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblShipDelivery extends Model
{
    use HasFactory;

    protected $table = 'tbl_ship_deliveries';

    protected $fillable = [
        'ship_name',
        'crew_name',
        'contact_number',
        'container_size_liters',
        'container_type',
        'quantity',
        'price_per_container',
        'total_amount',
        'delivered_at',
        'payment_status',
        'money_received',
        'delivery_fee_included',
        'remarks',
    ];

    protected $casts = [
        'container_size_liters'  => 'decimal:2',
        'quantity'               => 'integer',
        'price_per_container'    => 'decimal:2',
        'total_amount'           => 'decimal:2',
        'money_received'         => 'decimal:2',
        'delivery_fee_included'  => 'boolean',
        'delivered_at'           => 'datetime',
    ];
}
