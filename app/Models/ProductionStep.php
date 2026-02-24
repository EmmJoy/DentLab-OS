<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'predict3d_id',
        'step_number',
        'upper_value',
        'lower_value',
        // Legacy support: some databases have this NOT NULL column
        'patient_predict3d_id',
        // Legacy support: some databases require a non-null step_type
        'step_type',
        // Legacy support: some databases require created_by
        'created_by',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'upper_value' => 'integer',
        'lower_value' => 'integer',
        'patient_predict3d_id' => 'string',
        'step_type' => 'string',
        'created_by' => 'integer',
    ];
}
