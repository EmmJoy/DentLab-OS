<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'predict3d_id',
        'total_amount',
        'payment_method',
        'is_installment',
        'next_payment_date',
        'remaining_amount',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'is_installment' => 'boolean',
        'next_payment_date' => 'date',
    ];
}
