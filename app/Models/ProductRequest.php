<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
        'image',
        'status', // pending, approved, rejected
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
