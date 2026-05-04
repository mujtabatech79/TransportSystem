<?php
// app/Mail/VehicleApprovalStatus.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Vehicle;
use App\Models\Userr;

class VehicleApprovalStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicle;
    public $user;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Vehicle $vehicle, Userr $user, $status)
    {
        $this->vehicle = $vehicle;
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'approved' 
            ? 'Vehicle Approved - TruckLink' 
            : 'Vehicle Registration Update - TruckLink';
            
        return $this->subject($subject)
                    ->view('emails.vehicle_approval_status')
                    ->with([
                        'vehicle' => $this->vehicle,
                        'user' => $this->user,
                        'status' => $this->status,
                        'dashboard_url' => route('provider.login')
                    ]);
    }
}