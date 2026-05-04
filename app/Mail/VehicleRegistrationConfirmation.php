<?php
// app/Mail/VehicleRegistrationConfirmation.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Vehicle;
use App\Models\Userr;

class VehicleRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicle;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Vehicle $vehicle, Userr $user)
    {
        $this->vehicle = $vehicle;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Vehicle Registration Confirmation - TruckLink')
                    ->view('emails.vehicle_registration_confirmation')
                    ->with([
                        'vehicle' => $this->vehicle,
                        'user' => $this->user,
                        'approval_url' => route('provider.login'),
                        'support_email' => env('MAIL_FROM_ADDRESS', 'support@trucklink.com')
                    ]);
    }
}