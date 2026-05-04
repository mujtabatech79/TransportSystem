<?php
// app/Jobs/SendBookingRejectionEmail.php

namespace App\Jobs;

use App\Mail\BookingRejectionEmail;
use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingRejectionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $recipient;  // Changed from $user to $recipient
    protected $rejectionReason;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking, Userr $recipient, $rejectionReason)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::to($this->recipient->email)->send(new BookingRejectionEmail($this->booking, $this->recipient, $this->rejectionReason));
            Log::info("Booking rejection email sent to: {$this->recipient->email}", [
                'booking_id' => $this->booking->id,
                'reason' => $this->rejectionReason
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send booking rejection email: " . $e->getMessage(), [
                'booking_id' => $this->booking->id,
                'recipient_email' => $this->recipient->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}