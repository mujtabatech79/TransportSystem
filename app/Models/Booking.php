<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'booking_date',
        'pickup_location',
        'pickup_lat',
        'pickup_lng',
        'dropoff_location',
        'dropoff_lat',
        'dropoff_lng',
        'status', // This will now store: request, accept, reject, complete
        'pickup_time',
        'goods_type',
        'goods_weight',
        'special_instructions',
        'estimated_distance',
        'estimated_fare',
         'estimated_duration',  // Add this
    'actual_duration',     // Add this
    'duration_text', 
        'actual_distance',
        'actual_fare',
        'penalty_amount',  // Add this field
        'payment_method',
        'payment_status',
        'route_polyline',
        'route_directions',
        'selected_route_name',
        'has_tolls',
        'toll_cost',
        'route_options',
        // Existing fields (keep them as they are)
        'request_status',
        'delivery_status',
        'is_booking_complete',
        'rejection_reason',
        'accepted_at',
        'rejected_at',
        'dispatched_at',
        'in_transit_at',
        'delivered_at',
         'is_resubmit', // New field
        'original_booking_id','live_lat', 'live_lng', 'is_sharing_location', 'location_updated_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'dropoff_lat' => 'decimal:8',
        'dropoff_lng' => 'decimal:8',
        'goods_weight' => 'decimal:2',
        'estimated_distance' => 'decimal:2',
        'estimated_fare' => 'decimal:2',
         'estimated_duration' => 'integer',
    'actual_duration' => 'integer',
        'actual_distance' => 'decimal:2',
        'actual_fare' => 'decimal:2',
           'penalty_amount' => 'decimal:2',  // Add this
        'has_tolls' => 'boolean',
        'toll_cost' => 'decimal:2',
        'route_options' => 'array',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'live_lat' => 'decimal:7',
'live_lng' => 'decimal:7',
'is_sharing_location' => 'boolean',
'location_updated_at' => 'datetime',
         'is_resubmit' => 'boolean'
    ];

    protected $attributes = [
        'has_tolls' => false,
        'status' => 'request', // Default status is 'request'
        'payment_status' => 'pending',
        'request_status' => 'pending',
        'is_booking_complete' => 'no',
        'is_resubmit' => false
    ];








    


public function reviews()
{
    return $this->hasMany(Review::class);
}

public function complaints()
{
    return $this->hasMany(Complaint::class);
}

public function hasReviewFromCustomer($customerId)
{
    return $this->reviews()->where('customer_id', $customerId)->exists();
}

public function canReview($customerId)
{
    return $this->status === 'complete' && !$this->hasReviewFromCustomer($customerId);
}























// for resubmit
public function originalBooking()
    {
        return $this->belongsTo(Booking::class, 'original_booking_id');
    }

    /**
     * Get resubmitted bookings (for original booking)
     */
    public function resubmittedBookings()
    {
        return $this->hasMany(Booking::class, 'original_booking_id');
    }

    /**
     * Check if this booking is a resubmission
     */
    public function isResubmission()
    {
        return $this->is_resubmit == true;
    }

    /**
     * Check if this booking can be resubmitted
     */
    public function canResubmit()
    {
        return $this->status === 'reject' && $this->is_resubmit == false;
    }



















    
    /**
     * Get the customer who made this booking
     */
    public function customer()
    {
        return $this->belongsTo(Userr::class, 'customer_id');
    }

    /**
     * Get the vehicle that is booked
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope a query to only include pending requests
     */
    public function scopePendingRequests($query)
    {
        return $query->where('status', 'request');
    }

    /**
     * Scope a query to only include accepted bookings
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accept');
    }

    /**
     * Scope a query to only include rejected bookings
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'reject');
    }

    /**
     * Scope a query to only include completed bookings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'complete');
    }

    /**
     * Accept the booking request
     */
   public function acceptRequest()
{
    $this->status = 'accept';
    $this->request_status = 'accepted';
    $this->delivery_status = 'order_confirmed';
    $this->is_booking_complete = 'no';
    $this->accepted_at = now();
    $this->save();

    // Mark vehicle as booked
    if ($this->vehicle) {
        $this->vehicle->update(['is_booked' => 'yes']);
    }

    return true;
}

    /**
     * Reject the booking request
     */
    public function rejectRequest($reason = null)
    {
        $this->status = 'reject'; // Change to reject
        $this->request_status = 'rejected';
        $this->rejection_reason = $reason;
        $this->rejected_at = now();
        $this->save();

        // Vehicle remains available
        return true;
    }

    /**
     * Complete the booking (when delivered)
     */
    public function completeBooking()
    {
        $this->status = 'complete'; // Change to complete
        $this->delivery_status = 'delivered';
        $this->is_booking_complete = 'yes';
        $this->delivered_at = now();
        $this->save();

        // Make vehicle available again
        if ($this->vehicle) {
            $this->vehicle->update(['is_booked' => 'no']);
        }

        return true;
    }

    /**
     * Update delivery status
     */
// public function updateDeliveryStatus($status)
// {
//     $validStatuses = ['order_confirmed', 'vehicle_dispatched', 'in_transit', 'delivered'];
    
//     if (!in_array($status, $validStatuses)) {
//         return false;
//     }

//     $oldStatus = $this->delivery_status;
//     $this->delivery_status = $status;

//     // Set timestamps based on status
//     switch ($status) {
//         case 'vehicle_dispatched':
//             $this->dispatched_at = now();
//             break;
//         case 'in_transit':
//             $this->in_transit_at = now();
//             break;
//         case 'delivered':
//             $this->delivered_at = now();
//             $this->is_booking_complete = 'yes';
//             $this->status = 'complete'; // Mark as complete when delivered
            
//             // Make vehicle available again
//             if ($this->vehicle) {
//                 $this->vehicle->update(['is_booked' => 'no']);
//             }
//             break;
//     }

//     $this->save();
    
//     \Log::info('Delivery status updated', [
//         'booking_id' => $this->id,
//         'old_status' => $oldStatus,
//         'new_status' => $status,
//         'dispatched_at' => $this->dispatched_at,
//         'in_transit_at' => $this->in_transit_at,
//         'delivered_at' => $this->delivered_at
//     ]);
    
//     return true;
// }

    /**
     * Get status badge class based on status field
     */
    public function getStatusBadgeAttribute()
    {
        $classes = [
            'request' => 'bg-warning',
            'accept' => 'bg-success',
            'reject' => 'bg-danger',
            'complete' => 'bg-info'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'request' => 'Request Pending',
            'accept' => 'Accepted',
            'reject' => 'Rejected',
            'complete' => 'Completed'
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    /**
     * Get delivery status badge class
     */
    public function getDeliveryStatusBadgeAttribute()
    {
        $classes = [
            'order_confirmed' => 'bg-info',
            'vehicle_dispatched' => 'bg-primary',
            'in_transit' => 'bg-warning',
            'delivered' => 'bg-success'
        ];

        return $classes[$this->delivery_status] ?? 'bg-secondary';
    }

    /**
     * Get delivery status text
     */
    public function getDeliveryStatusTextAttribute()
    {
        $texts = [
            'order_confirmed' => 'Order Confirmed',
            'vehicle_dispatched' => 'Vehicle Dispatched',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered'
        ];

        return $texts[$this->delivery_status] ?? 'N/A';
    }

    /**
     * Get request status badge class
     */
    public function getRequestStatusBadgeAttribute()
    {
        $classes = [
            'pending' => 'bg-warning',
            'accepted' => 'bg-success',
            'rejected' => 'bg-danger'
        ];

        return $classes[$this->request_status] ?? 'bg-secondary';
    }

    /**
     * Check if booking is pending (request)
     */
    public function isPending()
    {
        return $this->status === 'request';
    }

    /**
     * Check if booking is accepted
     */
    public function isAccepted()
    {
        return $this->status === 'accept';
    }

    /**
     * Check if booking is rejected
     */
    public function isRejected()
    {
        return $this->status === 'reject';
    }

    /**
     * Check if booking is completed
     */
    public function isCompleted()
    {
        return $this->status === 'complete';
    }

    /**
     * Check if booking is delivered
     */
    public function isDelivered()
    {
        return $this->delivery_status === 'delivered';
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Set default values
            if (!isset($booking->status)) {
                $booking->status = 'request';
            }
            if (!isset($booking->request_status)) {
                $booking->request_status = 'pending';
            }
            if (!isset($booking->is_booking_complete)) {
                $booking->is_booking_complete = 'no';
            }
        });

        static::created(function ($booking) {
            \Log::info('New booking request created', [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'vehicle_id' => $booking->vehicle_id,
                'status' => $booking->status
            ]);
        });
    }









/**
 * Calculate penalty based on actual delivery time vs estimated duration
 * 
 * @return array ['penalty' => float, 'actual_fare' => float, 'delay_hours' => float, 'message' => string]
 */
public function calculatePenalty()
{
    // Check if both accepted_at and delivered_at exist
    if (!$this->accepted_at || !$this->delivered_at) {
        return [
            'penalty' => 0,
            'actual_fare' => $this->estimated_fare ?? 0,
            'delay_hours' => 0,
            'message' => 'Delivery time information is incomplete.'
        ];
    }
    
    // Calculate actual duration in minutes
    $accepted = \Carbon\Carbon::parse($this->accepted_at);
    $delivered = \Carbon\Carbon::parse($this->delivered_at);
    $actualMinutes = $accepted->diffInMinutes($delivered);
    
    // Store actual duration in minutes
    $this->actual_duration = $actualMinutes;
    
    // Get estimated duration (assuming stored in minutes)
    $estimatedMinutes = $this->estimated_duration ?? 0;
    
    // Calculate delay in minutes (only if actual > estimated)
    $delayMinutes = max(0, $actualMinutes - $estimatedMinutes);
    
    // Convert delay to hours (rounded up to nearest hour)
    $delayHours = ceil($delayMinutes / 60);
    
    // Calculate penalty: 200 per hour delay
    $penalty = $delayHours * 200;
    
    // Calculate actual fare
    $estimatedFare = $this->estimated_fare ?? 0;
    $actualFare = max(0, $estimatedFare - $penalty);
    
    // Generate message
    if ($delayHours > 0) {
        $message = "You were late by {$delayHours} hour(s). Penalty applied: Rs " . number_format($penalty) . ".";
    } else {
        $message = "Delivery was on time or early. No penalty applied.";
    }
    
    return [
        'penalty' => $penalty,
        'actual_fare' => $actualFare,
        'delay_hours' => $delayHours,
        'delay_minutes' => $delayMinutes,
        'actual_minutes' => $actualMinutes,
        'estimated_minutes' => $estimatedMinutes,
        'message' => $message
    ];
}

/**
 * Apply penalty when marking as delivered
 * 
 * @return array Result of penalty calculation
 */
public function applyPenalty()
{
    $penaltyData = $this->calculatePenalty();
    
    $this->penalty_amount = $penaltyData['penalty'];
    $this->actual_fare = $penaltyData['actual_fare'];
    $this->save();
    
    return $penaltyData;
}

/**
 * Update delivery status with penalty calculation for delivered status
 */
public function updateDeliveryStatus($status)
{
    $validStatuses = ['order_confirmed', 'vehicle_dispatched', 'in_transit', 'delivered'];
    
    if (!in_array($status, $validStatuses)) {
        return false;
    }

    $oldStatus = $this->delivery_status;
    $this->delivery_status = $status;
    
    $penaltyResult = null;

    // Set timestamps based on status
    switch ($status) {
        case 'vehicle_dispatched':
            $this->dispatched_at = now();
            break;
        case 'in_transit':
            $this->in_transit_at = now();
            break;
        case 'delivered':
            $this->delivered_at = now();
            $this->is_booking_complete = 'yes';
            $this->status = 'complete';
            
            // Penalty calculate karo (estimated_fare - penalty = actual_fare)
            $penaltyResult = $this->applyPenalty();
            
            // COD payment: delivery pe payment_status paid karo
            // Card/JazzCash/Easypaisa ke liye acceptBooking pe already paid ho chuka hai
            if ($this->payment_method === 'cod' && $this->payment_status === 'pending') {
                $this->payment_status = 'paid';
                
                // Payment record bhi update karo
                \App\Models\Payment::where('booking_id', $this->id)
                    ->update([
                        'status' => \App\Models\Payment::STATUS_COMPLETED,
                        'paid_at' => now()
                    ]);
            }
            
            // Make vehicle available again
            if ($this->vehicle) {
                $this->vehicle->update(['is_booked' => 'no']);
            }
            break;
    }

    $this->save();
    
    \Log::info('Delivery status updated', [
        'booking_id' => $this->id,
        'old_status' => $oldStatus,
        'new_status' => $status,
        'payment_method' => $this->payment_method,
        'payment_status' => $this->payment_status,
        'dispatched_at' => $this->dispatched_at,
        'in_transit_at' => $this->in_transit_at,
        'delivered_at' => $this->delivered_at,
        'penalty_applied' => $penaltyResult ? $penaltyResult['penalty'] : 0,
        'actual_fare' => $this->actual_fare
    ]);
    
    return $penaltyResult;
}











}