<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuyBoxEmailAlertMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $customMessage;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $customMessage)
    {
        $this->title = $title;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.buyboxEmailView');
    }
}
