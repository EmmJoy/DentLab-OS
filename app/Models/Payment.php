<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'amount',
        'payment_method',
        'payment_date',
        'description',
        'status',
        'processed_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'Predict3DId');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
