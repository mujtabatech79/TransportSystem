<?php
// app/Mail/BookingAcceptanceEmail.php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingAcceptanceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $recipient;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, Userr $recipient, $type)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->type = $type;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->type === 'customer' 
            ? 'Congratulations! Your Booking Has Been Confirmed - TruckLink' 
            : 'Booking Accepted - You Have a New Confirmed Booking - TruckLink';
            
        return $this->subject($subject)
                    ->view('emails.booking_acceptance')
                    ->with([
                        'booking' => $this->booking,
                        'user' => $this->recipient,  // Pass recipient as user for the view
                        'type' => $this->type,
                        'dashboard_url' => route($this->type === 'customer' ? 'mybookings' : 'provider.login')
                    ]);
    }
}