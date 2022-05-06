<?php

namespace App\Events;

use App\Models\NotificationModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationDetailsModel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SendNotificationUserSpecific implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $manager, $noti;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($manager, $noti)
    {
        $this->manager = $manager;
        $this->noti = $noti;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //attach manager id to the channel name so that it can be a dynamic channel
        if($this->manager == null)
        return new Channel('hello-tec-channel');
        else
        return new Channel('hello-tec-channel'.$this->manager);
    }
    public function broadcastAs()
    {
        return "sendNotification";
    }
    public function broadcastWith()
    {
        return $this->noti;
    }
    // public function broadcastF
}
