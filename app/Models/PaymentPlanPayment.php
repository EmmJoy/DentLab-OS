<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_plan_id',
        'amount',
        'payment_date',
        'payment_method',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];
}
