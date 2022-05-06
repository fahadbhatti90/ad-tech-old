<?php

namespace App\Events;

use App\Models\NotificationModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationDetailsModel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SendClientLoginStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $id, $host, $type, $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $type, $status)
    {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->host = getHostForNoti();
       
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('hello-tec-login-status');
    }
    public function broadcastAs()
    {
        return "SendClientLoginStatus";
    }
    // public function broadcastWith()
    // {
        
    // }
    // public function broadcastF
}
