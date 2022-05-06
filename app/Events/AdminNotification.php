<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AdminNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public  $id, $type, $title, $message, $created_at;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $type, $title, $message, $created_at){
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->created_at = $created_at;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('hello-tec-channel');
    }
    public function broadcastAs()
    {
        return "sendNotification";
    }
    public function broadcastWith()
    {
        $noti = array(
        "id"=> $this->id,
        "type"=> $this->type,
        "title"=> $this->title,
        "message"=> $this->message,
        "host" => getHostForNoti(),
        "created_at"=> $this->created_at,
        );
        
        //add foreach loop on account id here and make a multidimensional array for mass insertion
        
        return $noti ;
    }
    // public function broadcastF
}
