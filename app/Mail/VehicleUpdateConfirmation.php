<?php
// app/Mail/VehicleUpdateConfirmation.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Vehicle;
use App\Models\Userr;

class VehicleUpdateConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicle;
    public $user;

    public function __construct(Vehicle $vehicle, Userr $user)
    {
        $this->vehicle = $vehicle;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Vehicle Update Confirmation - TruckLink')
                    ->view('emails.vehicle_update_confirmation')
                    ->with([
                        'vehicle' => $this->vehicle,
                        'user' => $this->user
                    ]);
    }
}