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
    public $rejectionReason;

    public function __construct(Vehicle $vehicle, Userr $user, $status, $rejectionReason = null)
    {
        $this->vehicle = $vehicle;
        $this->user = $user;
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
    }

    public function build()
    {
        $subject = $this->status === 'approved' 
            ? 'Congratulations! Your Vehicle Has Been Approved - TruckLink' 
            : 'Vehicle Registration Update - TruckLink';
            
        return $this->subject($subject)
                    ->view('emails.vehicle_approval_status')
                    ->with([
                        'vehicle' => $this->vehicle,
                        'user' => $this->user,
                        'status' => $this->status,
                        'rejectionReason' => $this->rejectionReason,
                        'dashboard_url' => route('provider.login')
                    ]);
    }
}