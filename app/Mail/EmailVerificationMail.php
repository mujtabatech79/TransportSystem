<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Userr;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationToken;

    public function __construct(Userr $user, $verificationToken)
    {
        $this->user = $user;
        $this->verificationToken = $verificationToken;
    }

    public function build()
    {
        $verificationUrl = url('/verify-email/' . $this->verificationToken);
        
        return $this->subject('Verify Your Email - TruckLink')
                    ->view('emails.verification')
                    ->with([
                        'user' => $this->user,
                        'verificationUrl' => $verificationUrl,
                    ]);
    }
}