<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $reunion;

    public function __construct($reunion)
    {
        $this->reunion = $reunion;
    }

    public function build()
    {
        return $this->view('emails.invitation')
                    ->with([
                        'reunion' => $this->reunion,
                    ])
                    ->subject('Invitation à la réunion');
    }
}
