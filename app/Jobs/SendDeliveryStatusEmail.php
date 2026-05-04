<?php
// app/Jobs/SendDeliveryStatusEmail.php

namespace App\Jobs;

use App\Mail\DeliveryStatusEmail;
use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDeliveryStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $recipient;
    protected $status;

    public function __construct(Booking $booking, Userr $recipient, $status)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->status = $status;
    }

    public function handle()
    {
        try {
            Mail::to($this->recipient->email)->send(new DeliveryStatusEmail($this->booking, $this->recipient, $this->status));
            Log::info("Delivery status email sent to: {$this->recipient->email}", [
                'booking_id' => $this->booking->id,
                'status' => $this->status,
                'recipient_type' => $this->recipient->role
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send delivery status email: " . $e->getMessage(), [
                'booking_id' => $this->booking->id,
                'recipient_email' => $this->recipient->email,
                'status' => $this->status
            ]);
        }
    }
}