<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingInvitation extends Mailable
{
    use Queueable, SerializesModels;

    protected $meeting;

    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    public function build()
    {
        return $this->view('emails.meeting_invitation')
                    ->with([
                        'meeting' => $this->meeting,
                    ])
                    ->subject('Invitation à une réunion en ligne');
    }
}
