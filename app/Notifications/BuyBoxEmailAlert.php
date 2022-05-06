<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BuyBoxEmailAlert extends Notification
{
    use Queueable;
    
    public $subject;
    public $messages;
    public $path;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $messages, $path)
    {
        $this->subject = $subject;
        $this->messages = $messages;
        $this->path = $path;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage());
        $mailMessage->from("sahil30252@gmail.com");
        $mailMessage->greeting('Hello!');
        $mailMessage->subject($this->subject);
        foreach ($this->messages as $value) {
            $mailMessage->line($value);
        } 
        
        $mailMessage->attach($this->path);

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
