<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class BuyBoxAlert extends Notification
{
    use Queueable;
    public $messageCustom;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($messageCustom)
    {
        $this->messageCustom = $messageCustom;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }
    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = url('public/uploads/SoldByAlert.csv');
        return (new SlackMessage)
                    ->content($this->messageCustom)
                    ->attachment(function ($attachment) use ($url) {
                    $attachment->title('AsinAlert', $url);
                });
    }
}
