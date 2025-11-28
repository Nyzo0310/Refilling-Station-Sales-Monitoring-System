<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblExpense extends Model
{
    use HasFactory;

    protected $table = 'tbl_expenses';

    protected $fillable = [
        'date',
        'expense_type',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];
}
