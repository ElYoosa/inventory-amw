<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnamtaPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($username, $resetUrl)
    {
        $this->username = $username;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🔐 Permintaan Reset Password Akun ANAMTA')
            ->from('umroh.anamta@gmail.com', 'ANAMTA Inventory System')
            ->view('emails.anamta-reset')
            ->with([
                'username' => $this->username,
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
