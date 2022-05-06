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

class SendNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $manager, $accounts, $id, $type, $title, $message, $notiDetails, $details, $created_at;
    public static $notiId = [];
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($manager, $accounts, $type, $title, $message, $notiDetails, $details = null, $created_at)
    {
        $this->manager = $manager;
        $this->accounts = $accounts;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->notiDetails = $notiDetails;
        $this->details = $details;
        $this->created_at = $created_at;
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
        $noti = array(
        "type"=> $this->type,
        "title"=> $this->title,
        "message"=> $this->message,
        "host" => getHostForNoti(),
        "created_at"=> $this->created_at,
        );
        
        //add foreach loop on account id here and make a multidimensional array for mass insertion
        $accounts = $this->accounts;
        foreach ($accounts as $accountId => $accountData) {
            # code...
            $DBnoti = array(
            "type"=> $this->type,
            "title"=> $this->title,
            "message"=> $this->message,
            "details"=> $this->notiDetails,
            //add fkAccountId Column Here
            "fkAccountId"=> $accountId == "null"? NULL : $accountId,
            "created_at"=> $this->created_at,
            );
            DB::beginTransaction();
            try {
                    //change create to insert function for mass insertion
                    $addNotificaiton = NotificationModel::create($DBnoti);
                    $DBnotiDetails = [];
                    foreach ($accountData as $key => $value) {
                        $DBnotiDetail = array(
                            "n_id"=> $addNotificaiton->id,
                            "details"=> json_encode($value),
                            "created_at"=> $this->created_at,
                        ); 
                        array_push($DBnotiDetails,$DBnotiDetail);
                        if(count($DBnotiDetails) >= 1000) {
                            NotificationDetailsModel::insert($DBnotiDetails);
                            $DBnotiDetails = [];
                        }   
                    }//end foreach
                    if(count($DBnotiDetails) > 0) {
                        NotificationDetailsModel::insert($DBnotiDetails);
                    }
                    
                    $this->id = $addNotificaiton->id;
                    $noti["id"] = $this->id;
                    self::$notiId = $this->id;
                    DB::commit();
            } catch (\Exception $e) {
                    DB::rollback();
                    Log::error("Error Saving Notificaiton in DB");
                    Log::error($e->getMessage());
                    Log::error($e->getTrace());
                    Log::error($this->details);
            }  
        }
        return $noti;
    }
    // public function broadcastF
}
