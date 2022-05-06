<?php

namespace App\Console\Commands\ScrapperCommands;

use Illuminate\Console\Command;
use App\Events\SendNotification;
use App\Models\ScrapingModels\asinModel;
use App\Models\ScrapingModels\ActivityTrackerModel;
use App\Http\Controllers\ClientAuth\NotesController;
use App\Models\ScrapingModels\UserHierarchy\AccountsAsinModel;

class GetDailyAsinAccountIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapper:getAccountIds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Account Ids of asins uploaded by user and then scraping will start';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(!asinModel::doesntHave("getAsinAccounts")->exists())
        return;
        $dataManyToMany = [];
        $data = [];
        $rejectedASINs = [];
        $failData = [];
        $failCount = 0;
        
        $ress =  asinModel::with("accounts")->get()->reject(function ($asin) use (&$rejectedASINs, &$failData, &$failCount){
            if(!(count($asin->accounts) > 0))
            {
                // $rejectedASINs[] = $asin->asin_id;
                $rejectedASINs[] = $asin;
                $failData["null"][] = ($asin);
                $failCount++;
            }
            return !count($asin->accounts) > 0;//reject Those which doesn't have accounts by return true
        });
        
        // asinModel::whereIn("asin_id",$rejectedASINs)->delete();
        foreach ($ress as $key => $asin) {
            if(count($asin->accounts) > 0)
            {
                foreach ($asin->accounts as $accountkey => $account) {
                    $data[] = [        
                        "fkAccountId"=>$account->fk_account_id,
                        "fkAsinId"=>$asin->asin_id,
                        "uniqueColumn"=>$asin->asin_id."|".$account->fk_account_id,
                        "createdaAt"=>date('Y-m-d H:i:s'),
                    ];
                }
            }
        }//end foreach
        if(count($rejectedASINs) > 0)
        {
            
            foreach ($rejectedASINs as $rejectedASINkey => $rejectedASIN) {
                $data[] = [        
                    "fkAccountId"=>NULL,
                    "fkAsinId"=>$rejectedASIN->asin_id,
                    "uniqueColumn"=>$rejectedASIN->asin_id."|null",
                    "createdaAt"=>date('Y-m-d H:i:s'),
                ];
            }
        }
        //storing data in chunks
        $dataManyToMany = array_chunk($data,1000);
        foreach ($dataManyToMany as $dataManyToManykey => $dataManyToManyValue) {
            AccountsAsinModel::insertOrUpdate($dataManyToManyValue,AccountsAsinModel::$tableName);
        }
        // $notiDetails = [];
        // $notiDetails["Total Fail"] = $failCount;
        // $notiDetails["Details Download Link"] = "Download Details";
        // $notiDetails["Failed At"] = date("Y-m-d H:i");
        // broadcast(new SendNotification(
        //     null, 
        //     $failData,
        //     3,
        //     "Daily Asins NO Account Ids Found Error",
        //     "Not able to find account id against some asins",
        //     json_encode($notiDetails),
        //     null,
        //     date("Y-m-d H:i"))
        // )->toOthers();
        ActivityTrackerModel::setActivity(
            "scrapper:getAccountIds command Ran Total ASINS => ".(count($ress) + count($rejectedASINs)),
            "Constant ERROR",
            "GetDailyAsinAccountIds",
            "App\Console\Commands\ScrapperCommands",
            date('Y-m-d H:i:s')
        );
    }
}
