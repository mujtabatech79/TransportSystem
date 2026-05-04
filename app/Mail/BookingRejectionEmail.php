<?php
// app/Mail/BookingRejectionEmail.php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $recipient;
    public $rejectionReason;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, Userr $recipient, $rejectionReason)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->recipient->role === 'customer'
            ? 'Booking Request Update - TruckLink' 
            : 'Booking Rejected - You Have Rejected a Booking Request - TruckLink';
            
        return $this->subject($subject)
                    ->view('emails.booking_rejection')
                    ->with([
                        'booking' => $this->booking,
                        'user' => $this->recipient,  // Pass recipient as user for the view
                        'rejectionReason' => $this->rejectionReason,
                        'dashboard_url' => route($this->recipient->role === 'customer' ? 'mybookings' : 'provider.login')
                    ]);
    }
}