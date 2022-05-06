<?php

namespace App\Console\Commands\BuyBox;

use Illuminate\Console\Command;
use App\Events\SendNotification;
use App\Models\BuyBoxModels\BuyBoxAsinListModel;
use App\Models\BuyBoxModels\BuyBoxActivityTrackerModel;
use App\Models\BuyBoxModels\UserHierarchy\BuyBoxAccountsAsinModel;

class GetBuyBoxAsinAccountsIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buybox:getAccountIds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Account Ids of asins uploaded by user for buybox and then scraping will start';

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
        if(!BuyBoxAsinListModel::doesntHave("getAsinAccounts")->exists())
        return;
        $dataManyToMany = [];
        $data = [];
        $rejectedASINs = [];
        $failData = [];
        $failCount = 0;
        $ress =  BuyBoxAsinListModel::with("accounts")->get()->reject(function ($asin) use (&$rejectedASINs, &$failData, &$failCount){
            if(!count($asin->accounts) > 0)
            {
                $rejectedASINs[] = $asin;
                $failData["null"][] = ($asin);
                $failCount++;
            }
            return !count($asin->accounts) > 0;//reject Those which doesn't have accounts
        });
        // BuyBoxAsinListModel::whereIn("id",$rejectedASINs)->delete();
        foreach ($ress as $key => $asin) {
            if(count($asin->accounts) > 0)
            {
                foreach ($asin->accounts as $accountkey => $account) {
                    $data[] = [        
                        "fkAccountId"=>$account->fk_account_id,
                        "fkAsinId"=>$asin->id,
                        "uniqueColumn"=>$asin->id."|".$account->fk_account_id,
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
                    "fkAsinId"=>$rejectedASIN->id,
                    "uniqueColumn"=>$rejectedASIN->id."|null",
                    "createdaAt"=>date('Y-m-d H:i:s'),
                ];
            }
        }
        //storing data in chunks
        $dataManyToMany = array_chunk($data,1000);
        foreach ($dataManyToMany as $dataManyToManykey => $dataManyToManyValue) {
            BuyBoxAccountsAsinModel::insertOrUpdate($dataManyToManyValue,BuyBoxAccountsAsinModel::$tableName);
        }
        // $notiDetails = [];
        // $notiDetails["Total Fail"] = $failCount;
        // $notiDetails["Details Download Link"] = "Download Details";
        // $notiDetails["Failed At"] = date("Y-m-d H:i");
        // broadcast(new SendNotification(
        //     null, 
        //     $failData,
        //     3,
        //     "Buy Box NO Account Ids Found Error",
        //     "Not able to find account id against some asins",
        //     json_encode($notiDetails),
        //     null,
        //     date("Y-m-d H:i"))
        // )->toOthers();
        BuyBoxActivityTrackerModel::setActivity(
            "scrapper:getAccountIds command Ran Total ASINS => ".count($ress),
            "Constant ERROR",
            "GetDailyAsinAccountIds",
            "App\Console\Commands\ScrapperCommands",
            date('Y-m-d H:i:s')
        );
    }
}
