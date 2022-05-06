<?php

namespace App\Models;

use App\Notifications\BuyBoxAlert;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class BuyBoxModel
 * @package App\Models
 */
class mynotification extends Model
{
    use \Illuminate\Notifications\Notifiable;

    public $email;
    public function __construct($email)
    {
        $this->email = $email;
    }
  
}