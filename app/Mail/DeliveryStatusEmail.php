<?php
// app/Mail/DeliveryStatusEmail.php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Userr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $recipient;
    public $status;

    public function __construct(Booking $booking, Userr $recipient, $status)
    {
        $this->booking = $booking;
        $this->recipient = $recipient;
        $this->status = $status;
    }

    public function build()
    {
        $subjects = [
            'vehicle_dispatched' => [
                'customer' => 'Your Order Has Been Dispatched - TruckLink',
                'provider' => 'Order Dispatched - You Have Dispatched a Booking - TruckLink'
            ],
            'in_transit' => [
                'customer' => 'Your Order Is In Transit - TruckLink',
                'provider' => 'Order In Transit - Your Booking Is On The Way - TruckLink'
            ],
            'delivered' => [
                'customer' => 'Your Order Has Been Delivered - TruckLink',
                'provider' => 'Order Delivered - You Have Completed a Delivery - TruckLink'
            ]
        ];

        $type = $this->recipient->role === 'customer' ? 'customer' : 'provider';
        $subject = $subjects[$this->status][$type] ?? 'Delivery Status Update - TruckLink';

        return $this->subject($subject)
                    ->view('emails.delivery_status')
                    ->with([
                        'booking' => $this->booking,
                        'recipient' => $this->recipient,
                        'status' => $this->status,
                        'dashboard_url' => $this->recipient->role === 'customer' 
                            ? route('mybookings') 
                            : route('provider.login')
                    ]);
    }
}