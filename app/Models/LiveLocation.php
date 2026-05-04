<?php
// app/Models/LiveLocation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'vehicle_id', 'provider_id', 'latitude', 'longitude',
        'speed', 'heading', 'address', 'location_time', 'is_sharing'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'location_time' => 'datetime',
        'is_sharing' => 'boolean'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function provider()
    {
        return $this->belongsTo(Userr::class, 'provider_id');
    }
}