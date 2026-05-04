<?php
// app/Models/Complaint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'provider_id',
        'complaint_type',
        'subject',
        'description',
        'status',
        'admin_response',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];


    public function scopeInReview($query)
{
    return $query->where('status', 'in_review');
}

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Userr::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(Userr::class, 'provider_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'in_review' => 'bg-info',
            'resolved' => 'bg-success',
            'rejected' => 'bg-danger'
        ];
        
        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Pending',
            'in_review' => 'Under Review',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected'
        ];
        
        return $texts[$this->status] ?? $this->status;
    }

    public function markResolved($response = null)
    {
        $this->status = 'resolved';
        $this->admin_response = $response;
        $this->resolved_at = now();
        $this->save();
        
        return true;
    }
}