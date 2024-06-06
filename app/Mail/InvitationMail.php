<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $reunion;
    protected $userId; // Ajoutez cette variable pour passer l'ID de l'utilisateur

    public function __construct($reunion, $userId) // Modifiez le constructeur pour accepter $userId
    {
        $this->reunion = $reunion;
        $this->userId = $userId; // Initialisez la variable $userId
    }

    public function build()
    {
        return $this->view('emails.invitation')
                    ->with([
                        'reunion' => $this->reunion,
                        'userId' => $this->userId, 
                    ])
                    ->subject('Invitation to the meeting');
    }
}