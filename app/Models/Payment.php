<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'provider_id',
        'payment_method',
        'amount',
        'status',
        'transaction_id',
        'sandbox_mode',
        'card_number_masked',
        'sandbox_token',
        'payment_response',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'sandbox_mode' => 'boolean',
        'payment_response' => 'array',
        'paid_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const METHOD_JAZZCASH = 'jazzcash';
    const METHOD_EASYPAISA = 'easypaisa';
    const METHOD_COD = 'cod';
    const METHOD_CARD = 'card';

    /**
     * Get the booking associated with this payment
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Userr::class, 'customer_id');
    }

    /**
     * Get the provider
     */
    public function provider()
    {
        return $this->belongsTo(Userr::class, 'provider_id');
    }

    /**
     * Mark payment as completed
     */
   public function markAsCompleted($transactionId = null)
{
    $this->status = self::STATUS_COMPLETED;
    $this->paid_at = now();
    if ($transactionId) {
        $this->transaction_id = $transactionId;
    }
    $this->save();
    
    // Booking payment_status deliberately nahi update kar rahe
    // Yeh acceptBooking aur COD delivery pe handle hoga
    
    return $this;
}

    /**
     * Mark payment as failed
     */
    public function markAsFailed($response = null)
    {
        $this->status = self::STATUS_FAILED;
        if ($response) {
            $this->payment_response = $response;
        }
        $this->save();
        
        return $this;
    }

    /**
     * Generate sandbox transaction ID
     */
    public static function generateSandboxTransactionId()
    {
        return 'SANDBOX_' . strtoupper(uniqid()) . '_' . rand(100000, 999999);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}