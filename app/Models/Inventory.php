<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'category',
        'quantity',
        'unit_price',
        'supplier',
        'expiry_date',
        'description',
        'status',
        'managed_by'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }
}
