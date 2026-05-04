<?php
// app/Jobs/SendBookingAcceptanceEmail.php

namespace App\Jobs;

use App\Mail\BookingAcceptanceEmail;
use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingAcceptanceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $recipient;  // Changed from $user to $recipient to avoid confusion
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking, Userr $recipient, $type)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::to($this->recipient->email)->send(new BookingAcceptanceEmail($this->booking, $this->recipient, $this->type));
            Log::info("Booking acceptance email sent to: {$this->recipient->email}", [
                'booking_id' => $this->booking->id,
                'type' => $this->type
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send booking acceptance email: " . $e->getMessage(), [
                'booking_id' => $this->booking->id,
                'recipient_email' => $this->recipient->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}