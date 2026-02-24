<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function totalAmount(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->unit_price * (int) $item->quantity;
        });
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'checked_out')->whereNotNull('confirmed_at');
    }
}


