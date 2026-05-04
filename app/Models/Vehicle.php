<?php
// app/Models/Vehicle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_number',
        'chassis_number',
        'vehicle_type',
        'can_carry',
        'weight_capacity',
        'status',
        'is_booked',
        'vehicle_image',
        'smartcard_image',
        'is_active',
    ];



public function user()
{
    return $this->belongsTo(Userr::class, 'user_id');
}

    public function getVehicleDetails($id)
{
    $vehicle = Vehicle::with('user')->findOrFail($id);
    return response()->json($vehicle);
}
    /**
     * Get the user (provider) who owns this vehicle
     */
    // public function user()
    // {
    //     return $this->belongsTo(Userr::class);
    // }

    /**
     * Get all bookings for this vehicle
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get pending booking requests for this vehicle
     */
    public function pendingRequests()
    {
        return $this->hasMany(Booking::class)->where('request_status', 'pending');
    }

    /**
     * Check if vehicle has any pending requests
     */
    public function hasPendingRequests()
    {
        return $this->pendingRequests()->exists();
    }

    /**
     * Get active booking (accepted and not delivered)
     */
    public function activeBooking()
    {
        return $this->hasMany(Booking::class)
            ->where('request_status', 'accepted')
            ->where('delivery_status', '!=', 'delivered')
            ->latest()
            ->first();
    }

    /**
     * Scope to get only active vehicles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get available vehicles
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_booked', 'no')->where('is_active', true);
    }













    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            Booking::class,
            'vehicle_id', // Foreign key on bookings table
            'booking_id', // Foreign key on reviews table
            'id',         // Local key on vehicles table
            'id'          // Local key on bookings table
        )->where('bookings.status', 'complete')
         ->where('reviews.is_approved', true);
    }

    /**
     * Get average rating for this vehicle
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count for this vehicle
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }




    // * Get rating distribution (1-5 stars)
     
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->reviews()->where('rating', $i)->count();
        }
        return $distribution;
    }

  
    
}