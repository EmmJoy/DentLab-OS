<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'Predict3DId',
        'FullName',
        'ScanningFor',
        'ScanningForOthers',
        'case_type',
        'DoctorName',
        'doctor_email',
        'ChamberName',
        'TerritoryName',
        'RegionalName',
        'PhoneNumber',
        'EmergencyContact',
        'Gender',
        'DateOfBirth',
        'Address',
        'UpperCases',
        'LowerCases',
        'created_by',
        'status'
    ];

    protected $primaryKey = 'Predict3DId';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'DateOfBirth' => 'date',
        'UpperCases' => 'integer',
        'LowerCases' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'Predict3DId';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'patient_id', 'Predict3DId');
    }
}
